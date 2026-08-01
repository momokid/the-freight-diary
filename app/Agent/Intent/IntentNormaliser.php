<?php

namespace App\Agent\Intent;

use Illuminate\Support\Facades\DB;

/**
 * Reduces an instruction to a stable pattern and fingerprint.
 *
 * "What is the status of BL MEDUY9898550?" and "status MSCU4421889"
 * both become "status {ref}" — one cached row serves every reference.
 */
class IntentNormaliser
{
    /** Loaded once per request. Small table, no cross-request staleness. */
    private static ?array $verbs = null;

    private const STOPWORDS = [
        'the',
        'what',
        'a',
        'an',
        'of',
        'for',
        'to',
        'on',
        'in',
        'at',
        'is',
        'are',
        'was',
        'were',
        'be',
        'my',
        'our',
        'me',
        'us',
        'i',
        'you',
        'it',
        'this',
        'that',
        'please',
        'kindly',
        'can',
        'could',
        'would',
        'will',
        'bl',
        'hbl',
        'container',
        'consignment',
        'no',
        'number',
        'ref',
    ];

    public function normalise(string $raw): array
    {
        $raw = trim($raw);

        $references = $this->extractReferences($raw);
        $text       = $this->maskReferences($raw, $references);

        $tokens = $this->tokenise($text);
        $tokens = $this->dropStopwords($tokens);

        [$tokens, $canonicalVerb] = $this->canonicaliseVerb($tokens);

        $pattern = implode(' ', $tokens);

        return [
            'raw'           => $raw,
            'pattern'       => $pattern,
            'fingerprint'   => hash('sha256', $pattern),
            'canonicalVerb' => $canonicalVerb,
            'references'    => $references,
        ];
    }

    /** Fingerprint a seed phrase that already contains a placeholder. */
    public function fingerprintExample(string $example): string
    {
        $text   = preg_replace('/\{[A-Za-z]+\}/', '{ref}', $example);
        $tokens = $this->dropStopwords($this->tokenise($text));

        [$tokens] = $this->canonicaliseVerb($tokens);

        return hash('sha256', implode(' ', $tokens));
    }

    // ── References ──────────────────────────────────────────────────────────

    /**
     * Deliberately loose: any token mixing letters and digits, four or more
     * characters. Catches MEDUY9898550, ONEYSH6AC2926400 and NEWBL3 alike.
     * A wrong guess is cheap — ResolveConsignmentStep rejects it cleanly.
     */
    private function extractReferences(string $text): array
    {
        preg_match_all('/\b[A-Za-z0-9\-\/]{4,}\b/', $text, $matches);

        $found = [];

        foreach ($matches[0] as $token) {
            $bare = str_replace(['-', '/'], '', $token);

            if (preg_match('/[A-Za-z]/', $bare) && preg_match('/\d/', $bare)) {
                $found[] = strtoupper($token);
            }
        }

        return array_values(array_unique($found));
    }

    private function maskReferences(string $text, array $references): string
    {
        foreach ($references as $ref) {
            $text = str_ireplace($ref, ' {ref} ', $text);
        }

        return $text;
    }

    // ── Tokens ──────────────────────────────────────────────────────────────

    private function tokenise(string $text): array
    {
        $text = mb_strtolower($text);
        $text = str_replace('{ref}', ' {ref} ', $text);
        $text = preg_replace('/[^a-z0-9{}\s]/', ' ', $text);

        return array_values(array_filter(preg_split('/\s+/', $text)));
    }

    private function dropStopwords(array $tokens): array
    {
        return array_values(array_filter(
            $tokens,
            fn($t) => $t === '{ref}' || ! in_array($t, self::STOPWORDS, true)
        ));
    }

    // ── Verbs ───────────────────────────────────────────────────────────────

    /** Replace the first recognised verb with its canonical form. */
    private function canonicaliseVerb(array $tokens): array
    {
        $verbs = $this->verbs();
        $found = null;

        foreach ($tokens as $i => $token) {
            if (isset($verbs[$token])) {
                $found      = $verbs[$token];
                $tokens[$i] = $found;
                break;
            }
        }

        return [$tokens, $found];
    }

    private function verbs(): array
    {
        if (self::$verbs !== null) {
            return self::$verbs;
        }

        self::$verbs = DB::table('agent_verb_synonyms')
            ->where('Status', 1)
            ->pluck('CanonicalVerb', 'Verb')
            ->map(fn($v) => mb_strtolower($v))
            ->toArray();

        return self::$verbs;
    }
}
