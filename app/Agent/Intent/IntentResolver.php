<?php

namespace App\Agent\Intent;

use App\Agent\Llm\LlmAdapter;
use App\Agent\Llm\LlmResponse;
use Illuminate\Support\Facades\Log;

/**
 * Layer 3. Asks the model which playbook an instruction means.
 *
 * Side-effect free by design: no database writes, no cache writes. It resolves
 * and returns; the router decides what happens next. That is what makes it
 * testable against a fixture catalogue rather than live data.
 *
 * Every failure path returns 'none'. A Layer 3 outage must degrade to "not
 * sure", never take the Command Center down.
 */
class IntentResolver
{
    public const RESOLVED = 'resolved';
    public const SUGGEST  = 'suggest';
    public const NONE     = 'none';

    private const MAX_SUGGESTIONS = 3;

    public function __construct(
        private LlmAdapter $llm,
        private ResolverPrompt $prompt,
    ) {}

    public function resolve(string $instruction, array $catalogue): array
    {
        if (empty($catalogue) || ! $this->llm->isConfigured()) {
            return $this->none();
        }

        $response = $this->llm->complete(
            $this->prompt->system($catalogue),
            $this->prompt->user($instruction),
            ['temperature' => 0, 'max_tokens' => 400],
        );

        if (! $response->ok) {
            Log::warning('[Layer3] Call failed: ' . $response->error);

            return $this->none($response);
        }

        $data = $this->decode($response->text);

        if ($data === null) {
            Log::warning('[Layer3] Unparseable output', ['raw' => mb_substr($response->text, 0, 500)]);

            return $this->none($response);
        }

        return $this->interpret($data, $instruction, $catalogue, $response);
    }

    // ── Validation ──────────────────────────────────────────────────────────

    private function interpret(array $data, string $instruction, array $catalogue, LlmResponse $response): array
    {
        $keys = array_column($catalogue, 'key');
        $key  = $data['playbook'] ?? null;

        // Not on the list we sent — the model invented it.
        if ($key !== null && ! in_array($key, $keys, true)) {
            Log::warning('[Layer3] Unknown playbook key returned', ['key' => $key]);
            $key = null;
        }

        if ($key === null) {
            return $this->none($response, $this->suggestions($data, $keys, $catalogue));
        }

        $confidence = $this->confidence($data['confidence'] ?? 0);
        $reference  = $this->reference($data['reference'] ?? null, $instruction);
        $params     = $this->params($data['params'] ?? [], $key, $catalogue, $reference);

        // A required reference the model could not evidence: missing beats invented.
        if ($reference === null && $this->requiresReference($key, $catalogue)) {
            Log::warning('[Layer3] Playbook needs a reference but none was evidenced', ['key' => $key]);

            return $this->none($response, $this->suggestions($data, $keys, $catalogue));
        }

        $outcome = $confidence >= (float) config('agent.llm.confidence_floor', 0.70)
            ? self::RESOLVED
            : self::SUGGEST;

        return [
            'outcome'     => $outcome,
            'playbookKey' => $key,
            'confidence'  => $confidence,
            'params'      => $params,
            'reference'   => $reference,
            'suggestions' => $outcome === self::SUGGEST
                ? $this->rankedWith($key, $confidence, $data, $keys, $catalogue)
                : [],
            'provider'    => config('agent.llm.driver'),
            'model'       => $this->llm->model(),
            'latencyMs'   => $response->latencyMs,
        ];
    }

    /**
     * A value the model did not read out of the instruction is a value it made
     * up. Compared case-insensitively.
     */
    private function reference($value, string $instruction): ?string
    {
        $value = is_string($value) ? trim($value) : '';

        if ($value === '') {
            return null;
        }

        return mb_stripos($instruction, $value) === false ? null : $value;
    }

    /** Only parameters the playbook actually declares survive. */
    private function params($given, string $key, array $catalogue, ?string $reference): array
    {
        $given    = is_array($given) ? $given : [];
        $declared = $this->declaredParams($key, $catalogue);

        $out = array_intersect_key($given, $declared);

        if ($reference !== null && array_key_exists('Reference', $declared)) {
            $out['Reference'] = $reference;
        }

        return $out;
    }

    private function requiresReference(string $key, array $catalogue): bool
    {
        $declared = $this->declaredParams($key, $catalogue);

        return ! empty($declared['Reference']['required']);
    }

    private function declaredParams(string $key, array $catalogue): array
    {
        foreach ($catalogue as $entry) {
            if (($entry['key'] ?? null) === $key) {
                return $entry['params'] ?? [];
            }
        }

        return [];
    }

    // ── Suggestions ─────────────────────────────────────────────────────────

    private function rankedWith(string $key, float $confidence, array $data, array $keys, array $catalogue): array
    {
        $alternates = is_array($data['alternates'] ?? null) ? $data['alternates'] : [];

        return $this->suggestions(
            ['alternates' => array_merge([['playbook' => $key, 'confidence' => $confidence]], $alternates)],
            $keys,
            $catalogue
        );
    }

    private function suggestions(array $data, array $keys, array $catalogue): array
    {
        $titles     = array_column($catalogue, 'title', 'key');
        $alternates = is_array($data['alternates'] ?? null) ? $data['alternates'] : [];
        $seen       = [];
        $out        = [];

        foreach ($alternates as $alternate) {
            $key = is_array($alternate) ? ($alternate['playbook'] ?? null) : null;

            if (! is_string($key) || ! in_array($key, $keys, true) || isset($seen[$key])) {
                continue;
            }

            $seen[$key] = true;

            $out[] = [
                'key'        => $key,
                'title'      => $titles[$key] ?? $key,
                'confidence' => $this->confidence($alternate['confidence'] ?? 0),
            ];
        }

        usort($out, fn($a, $b) => $b['confidence'] <=> $a['confidence']);

        return array_slice($out, 0, self::MAX_SUGGESTIONS);
    }

    // ── Parsing ─────────────────────────────────────────────────────────────

    /** Tolerates fenced JSON and surrounding prose; anything else is a failure. */
    private function decode(string $raw): ?array
    {
        $raw   = preg_replace('/```(?:json)?/i', '', trim($raw));
        $start = mb_strpos($raw, '{');
        $end   = mb_strrpos($raw, '}');

        if ($start === false || $end === false || $end < $start) {
            return null;
        }

        $decoded = json_decode(mb_substr($raw, $start, $end - $start + 1), true);

        return is_array($decoded) ? $decoded : null;
    }

    private function confidence($value): float
    {
        return max(0.0, min(1.0, (float) $value));
    }

    private function none(?LlmResponse $response = null, array $suggestions = []): array
    {
        return [
            'outcome'     => self::NONE,
            'playbookKey' => null,
            'confidence'  => 0.0,
            'params'      => [],
            'reference'   => null,
            'suggestions' => $suggestions,
            'provider'    => config('agent.llm.driver'),
            'model'       => $this->llm->model(),
            'latencyMs'   => $response?->latencyMs ?? 0,
        ];
    }
}
