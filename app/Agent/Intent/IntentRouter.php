<?php

namespace App\Agent\Intent;

use App\Agent\PlaybookCatalogue;
use App\Models\AgentPlaybook;
use App\Models\UserAuth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Decides what the Command Center should do with an input.
 *
 * Layer 0 — bare reference, no verb → search
 * Layer 1 — fingerprint matches the cache or a playbook example → run it
 * Layer 3 — the model picks from the playbooks this user may run
 *
 * Layer 1 writes back on match because a fingerprint hit is deterministic.
 * Layer 3 never writes here: it is a guess, and a guess must earn its place
 * in the cache by being approved or completed first.
 */
class IntentRouter
{
    public const SEARCH     = 'search';
    public const AGENT      = 'agent';
    public const SUGGEST    = 'suggest';
    public const UNRESOLVED = 'unresolved';

    public function __construct(
        private IntentNormaliser $normaliser,
        private IntentResolver $resolver,
        private PlaybookCatalogue $catalogue,
    ) {}

    public function route(string $input, UserAuth $userAuth): array
    {
        $n = $this->normaliser->normalise($input);

        // ── Layer 0 ──
        if ($this->looksLikeSearch($n)) {
            return [
                'decision'   => self::SEARCH,
                'pattern'    => $n['pattern'],
                'references' => $n['references'],
            ];
        }

        // ── Layer 1 ──
        // A match is only usable if we also have the reference the playbook
        // needs. The normaliser cannot see every BL — letters-only ones like
        // AAMEENAH slip past it — so an empty reference falls through to
        // Layer 3, which identifies it properly, rather than starting a run
        // that is certain to fail.
        if ($hit = $this->fromCache($n['fingerprint'])) {
            if ($this->hasUsableReference($n, $hit->PlaybookID)) {
                return $this->agentDecision($n, $hit->IntentKey, $hit->PlaybookID, 1);
            }
        }

        if ($playbook = $this->fromPlaybookExamples($n['fingerprint'])) {
            if ($this->hasUsableReference($n, $playbook->ID)) {
                $this->remember($n, $playbook, 1, 1.0);

                return $this->agentDecision($n, $playbook->PlaybookKey, $playbook->ID, 1);
            }
        }

        // ── Layer 3 ──
        $resolved = $this->resolver->resolve($input, $this->catalogue->forPrompt($userAuth));

        if ($resolved['outcome'] === IntentResolver::RESOLVED) {
            $playbook = AgentPlaybook::active()
                ->where('PlaybookKey', $resolved['playbookKey'])
                ->first();

            if ($playbook) {
                return $this->agentDecision(
                    $n,
                    $playbook->PlaybookKey,
                    $playbook->ID,
                    3,
                    $resolved
                );
            }
        }

        if (! empty($resolved['suggestions'])) {
            return [
                'decision'    => self::SUGGEST,
                'pattern'     => $n['pattern'],
                'fingerprint' => $n['fingerprint'],
                'references'  => $n['references'],
                'suggestions' => $resolved['suggestions'],
                'llm'         => $this->llmMeta($resolved),
            ];
        }

        return [
            'decision'      => self::UNRESOLVED,
            'pattern'       => $n['pattern'],
            'fingerprint'   => $n['fingerprint'],
            'canonicalVerb' => $n['canonicalVerb'],
            'references'    => $n['references'],
            'llm'           => $this->llmMeta($resolved),
        ];
    }

    /**
     * Write a resolved mapping back to Layer 1. Called by the caller once a
     * Layer 3 run is approved or completes — never at resolve time.
     */
    public function confirm(string $input, AgentPlaybook $playbook, float $confidence): void
    {
        $this->remember($this->normaliser->normalise($input), $playbook, 3, $confidence);
    }

    /** A wrong guess the user rejected. Downgrades the cached row over time. */
    public function recordMiss(string $fingerprint): void
    {
        DB::table('agent_intent_cache')
            ->where('Fingerprint', $fingerprint)
            ->update(['MissCount' => DB::raw('MissCount + 1')]);
    }

    // ── Layer 0 ─────────────────────────────────────────────────────────────

    /**
     * A bare reference with no verb is a lookup, not an instruction.
     * "MEDUY9898550" searches; "check MEDUY9898550" runs the agent.
     */
    private function looksLikeSearch(array $n): bool
    {
        if ($n['canonicalVerb'] !== null) {
            return false;
        }

        $tokens = array_filter(
            explode(' ', $n['pattern']),
            fn($t) => $t !== '' && $t !== '{ref}'
        );

        return count($tokens) === 0;
    }

    // ── Layer 1 ─────────────────────────────────────────────────────────────

    private function fromCache(string $fingerprint): ?object
    {
        $hit = DB::table('agent_intent_cache')
            ->where('Fingerprint', $fingerprint)
            ->where('Status', 1)
            ->first(['ID', 'IntentKey', 'PlaybookID']);

        if ($hit) {
            DB::table('agent_intent_cache')
                ->where('ID', $hit->ID)
                ->update([
                    'HitCount'   => DB::raw('HitCount + 1'),
                    'LastUsedAt' => Carbon::now(),
                ]);
        }

        return $hit;
    }

    /** Seed phrasings declared on each playbook, matched on first use. */
    private function fromPlaybookExamples(string $fingerprint): ?AgentPlaybook
    {
        $playbooks = AgentPlaybook::active()->get();

        foreach ($playbooks as $playbook) {
            foreach ($playbook->intentExamples() as $example) {
                if ($this->normaliser->fingerprintExample($example) === $fingerprint) {
                    return $playbook;
                }
            }
        }

        return null;
    }

    /**
     * True when the playbook either needs no reference, or the normaliser
     * found one. Playbooks with no gates take no reference, so they pass.
     */
    private function hasUsableReference(array $n, ?int $playbookId): bool
    {
        if (! empty($n['references'])) {
            return true;
        }

        $playbook = $playbookId ? AgentPlaybook::find($playbookId) : null;

        return $playbook === null || empty($playbook->StepsJson)
            ? true
            : ! $this->needsReference($playbook);
    }

    /** Does any step in this playbook declare a required Reference input? */
    private function needsReference(AgentPlaybook $playbook): bool
    {
        foreach ($playbook->StepsJson as $step) {
            if (($step['key'] ?? null) === 'consignment.resolve') {
                return true;
            }
        }

        return false;
    }

    private function remember(array $n, AgentPlaybook $playbook, int $layer, float $confidence): void
    {
        DB::statement(
            "INSERT IGNORE INTO `agent_intent_cache`
             (`Fingerprint`, `NormalisedPattern`, `IntentKey`, `PlaybookID`, `CanonicalVerb`,
              `ResolvedLayer`, `Confidence`, `HitCount`, `MissCount`, `LastUsedAt`, `CreatedAt`, `Status`)
             VALUES (?, ?, ?, ?, ?, ?, ?, 1, 0, ?, ?, 1)",
            [
                $n['fingerprint'],
                mb_substr($n['pattern'], 0, 255),
                $playbook->PlaybookKey,
                $playbook->ID,
                $n['canonicalVerb'],
                $layer,
                round($confidence, 4),
                Carbon::now(),
                Carbon::now(),
            ]
        );
    }

    private function agentDecision(
        array $n,
        string $intentKey,
        ?int $playbookId,
        int $layer,
        ?array $resolved = null
    ): array {
        return [
            'decision'        => self::AGENT,
            'intentKey'       => $intentKey,
            'playbookId'      => $playbookId,
            'resolutionLayer' => $layer,
            'pattern'         => $n['pattern'],
            'fingerprint'     => $n['fingerprint'],
            'canonicalVerb'   => $n['canonicalVerb'],
            'references'      => $n['references'],
            'confidence'      => $resolved['confidence'] ?? 1.0,
            'params'          => $resolved['params'] ?? [],
            'reference'       => $resolved['reference'] ?? ($n['references'][0] ?? null),
            'llm'             => $resolved ? $this->llmMeta($resolved) : null,
        ];
    }

    private function llmMeta(array $resolved): array
    {
        return [
            'provider'  => $resolved['provider'] ?? null,
            'model'     => $resolved['model'] ?? null,
            'latencyMs' => $resolved['latencyMs'] ?? 0,
        ];
    }
}
