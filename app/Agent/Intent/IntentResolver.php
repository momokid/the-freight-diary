<?php

namespace App\Agent\Intent;

use App\Agent\Llm\LlmAdapter;
use App\Agent\Llm\LlmResponse;
use Carbon\Carbon;
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

    /** Container counts and quantities are spoken as often as they are typed. */
    private const NUMBER_WORDS = [
        'one' => 1,
        'two' => 2,
        'three' => 3,
        'four' => 4,
        'five' => 5,
        'six' => 6,
        'seven' => 7,
        'eight' => 8,
        'nine' => 9,
        'ten' => 10,
        'eleven' => 11,
        'twelve' => 12,
        'thirteen' => 13,
        'fourteen' => 14,
        'fifteen' => 15,
        'sixteen' => 16,
        'seventeen' => 17,
        'eighteen' => 18,
        'nineteen' => 19,
        'twenty' => 20,
    ];

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
        $params     = $this->params($data['params'] ?? [], $key, $catalogue, $reference, $instruction);

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

    /**
     * Only declared parameters survive, and only when the instruction actually
     * evidences them. A dropped parameter becomes a question to the user; an
     * invented one becomes a wrong write.
     */
    private function params($given, string $key, array $catalogue, ?string $reference, string $instruction): array
    {
        $given    = is_array($given) ? $given : [];
        $declared = $this->declaredParams($key, $catalogue);
        $out      = [];

        foreach (array_intersect_key($given, $declared) as $name => $value) {
            $checked = $this->evidenced($value, $declared[$name]['type'] ?? 'string', $instruction);

            if ($checked === null) {
                Log::warning('[Layer3] Value not evidenced in instruction', [
                    'playbook'  => $key,
                    'parameter' => $name,
                ]);

                continue;
            }

            $out[$name] = $checked;
        }

        if ($reference !== null && array_key_exists('Reference', $declared)) {
            $out['Reference'] = $reference;
        }

        return $out;
    }

    /** Returns the value to use, normalised where the type allows it, or null. */
    private function evidenced($value, string $type, string $instruction): mixed
    {
        $value = is_scalar($value) ? trim((string) $value) : '';

        if ($value === '') {
            return null;
        }

        return match (strtolower($type)) {
            'date', 'datetime'                    => $this->evidencedDate($value, $instruction),
            'int', 'integer', 'number', 'decimal' => $this->evidencedNumber($value, $instruction),
            default                               => $this->evidencedString($value, $instruction),
        };
    }

    /** Verbatim, case-insensitive. Names and BLs must never be corrected. */
    private function evidencedString(string $value, string $instruction): ?string
    {
        return mb_stripos($instruction, $value) === false ? null : $value;
    }

    /**
     * The model may reformat a date — that is wanted. It may not choose one.
     * Accepted only when it matches a date the user actually wrote.
     */
    private function evidencedDate(string $value, string $instruction): ?string
    {
        $parsed = $this->parseDate($value);

        if ($parsed === null) {
            return null;
        }

        preg_match_all('/' . IntentNormaliser::DATE_PATTERN . '/i', $instruction, $matches);

        foreach ($matches[0] as $candidate) {
            if ($this->parseDate(trim($candidate)) === $parsed) {
                return $parsed;
            }
        }

        return null;
    }

    /** Digits as written, or a spelled-out number of the same value. */
    private function evidencedNumber(string $value, string $instruction): int|float|null
    {
        $clean = str_replace(',', '', $value);

        if (! is_numeric($clean)) {
            return null;
        }

        $number = $clean + 0;

        preg_match_all('/\b\d[\d,]*(?:\.\d+)?\b/', $instruction, $matches);

        foreach ($matches[0] as $candidate) {
            if (str_replace(',', '', $candidate) + 0 === $number) {
                return $number;
            }
        }

        foreach (self::NUMBER_WORDS as $word => $digit) {
            if ($digit === $number && preg_match('/\b' . $word . '\b/i', $instruction)) {
                return $number;
            }
        }

        return null;
    }

    /**
     * Day first. Carbon reads slashes as American, which would turn 03/07/2026
     * into March and silently save the wrong ETA.
     */
    private function parseDate(string $value): ?string
    {
        $value = trim($value);

        try {
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
                return Carbon::createFromFormat('Y-m-d', $value)->toDateString();
            }

            if (preg_match('/^(\d{1,2})[\/\-.](\d{1,2})[\/\-.](\d{2,4})$/', $value, $m)) {
                $format = mb_strlen($m[3]) === 2 ? 'd/m/y' : 'd/m/Y';

                return Carbon::createFromFormat($format, "{$m[1]}/{$m[2]}/{$m[3]}")->toDateString();
            }

            return Carbon::parse($value)->toDateString();
        } catch (\Throwable) {
            return null;
        }
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
