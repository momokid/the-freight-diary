<?php

namespace App\Agent\Intent;

use App\Models\AgentPlaybook;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Decides what the Command Center should do with an input.
 *
 * Layer 0 — bare reference, no verb → search
 * Layer 1 — fingerprint matches the cache or a playbook example → run it
 * Layer 2/3 — not yet built; falls through as unresolved
 */
class IntentRouter
{
    public const SEARCH     = 'search';
    public const AGENT      = 'agent';
    public const UNRESOLVED = 'unresolved';

    public function __construct(
        private IntentNormaliser $normaliser
    ) {}

    public function route(string $input): array
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
        if ($hit = $this->fromCache($n['fingerprint'])) {
            return $this->agentDecision($n, $hit->IntentKey, $hit->PlaybookID, 1);
        }

        if ($playbook = $this->fromPlaybookExamples($n['fingerprint'])) {
            $this->remember($n, $playbook, 1);

            return $this->agentDecision($n, $playbook->PlaybookKey, $playbook->ID, 1);
        }

        // ── Layers 2 and 3 pending ──
        return [
            'decision'      => self::UNRESOLVED,
            'pattern'       => $n['pattern'],
            'fingerprint'   => $n['fingerprint'],
            'canonicalVerb' => $n['canonicalVerb'],
            'references'    => $n['references'],
        ];
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

    /** Write a resolved mapping back to Layer 1 so it is free next time. */
    private function remember(array $n, AgentPlaybook $playbook, int $layer): void
    {
        DB::statement(
            "INSERT IGNORE INTO `agent_intent_cache`
             (`Fingerprint`, `NormalisedPattern`, `IntentKey`, `PlaybookID`, `CanonicalVerb`,
              `ResolvedLayer`, `Confidence`, `HitCount`, `MissCount`, `LastUsedAt`, `CreatedAt`, `Status`)
             VALUES (?, ?, ?, ?, ?, ?, 1.0000, 1, 0, ?, ?, 1)",
            [
                $n['fingerprint'],
                mb_substr($n['pattern'], 0, 255),
                $playbook->PlaybookKey,
                $playbook->ID,
                $n['canonicalVerb'],
                $layer,
                Carbon::now(),
                Carbon::now(),
            ]
        );
    }

    private function agentDecision(array $n, string $intentKey, ?int $playbookId, int $layer): array
    {
        return [
            'decision'        => self::AGENT,
            'intentKey'       => $intentKey,
            'playbookId'      => $playbookId,
            'resolutionLayer' => $layer,
            'pattern'         => $n['pattern'],
            'fingerprint'     => $n['fingerprint'],
            'canonicalVerb'   => $n['canonicalVerb'],
            'references'      => $n['references'],
        ];
    }
}
