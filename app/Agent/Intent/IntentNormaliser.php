<?php

namespace App\Agent\Intent;

use Illuminate\Support\Facades\DB;

/**
 * Reduces an instruction to a stable pattern and fingerprint.
 *
 * Its only job is the cache key. Identifying what each value means is the
 * model's work, not this class's — so anything shaped like a value is masked
 * and returned in the order it appeared.
 */
class IntentNormaliser
{
    /** Loaded once per request. Small table, no cross-request staleness. */
    private static ?array $verbs = null;

    private const MONTHS = 'jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec';

    /** Shared with IntentResolver, which checks a returned date against the text. */
    public const DATE_PATTERN = '\b\d{4}-\d{2}-\d{2}\b'
        . '|\b\d{1,2}[\/\-.]\d{1,2}[\/\-.]\d{2,4}\b'
        . '|\b\d{1,2}(?:st|nd|rd|th)?\s+(?:' . self::MONTHS . ')[a-z]*\b'
        . '|\b(?:' . self::MONTHS . ')[a-z]*\s+\d{1,2}(?:st|nd|rd|th)?\b';

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

        [$text, $references] = $this->mask($raw);

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

    // ── Masking ─────────────────────────────────────────────────────────────

    /**
     * One left-to-right pass. Earlier alternatives win, so a date is never
     * broken apart into its digits. Returns the masked text and the values
     * in the order they appeared — one entry per {ref} in the result.
     */
    private function mask(string $raw): array
    {
        $pattern = '/
              (?P<date>' . self::DATE_PATTERN . ')
            | (?P<amount>
                  \b\d{1,3}(?:,\d{3})+(?:\.\d+)?\b
                | \b\d+\.\d+\b
              )
            | (?P<token>\b[A-Za-z0-9][A-Za-z0-9\-\/]*\b)
        /ix';

        $found = [];

        $text = preg_replace_callback($pattern, function ($m) use (&$found) {
            $value = trim($m[0]);

            if (($m['token'] ?? '') !== '' && ! $this->isValue($value)) {
                return $m[0];
            }

            $found[] = $value;

            return ' {ref} ';
        }, $raw);

        return [$text, $found];
    }

    /** All digits, or four-plus characters carrying both a letter and a digit. */
    private function isValue(string $token): bool
    {
        $bare = str_replace(['-', '/'], '', $token);

        if ($bare === '') {
            return false;
        }

        if (ctype_digit($bare)) {
            return true;
        }

        return mb_strlen($bare) >= 4
            && preg_match('/[A-Za-z]/', $bare)
            && preg_match('/\d/', $bare);
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
