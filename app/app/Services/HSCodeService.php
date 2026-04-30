<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HSCodeService
{
    // ── Confidence thresholds ────────────────────────────────────────────────
    private const RULES_CONFIDENCE_THRESHOLD = 70;  // below this, call Gemini

    private const MIN_KEYWORD_MATCHES = 2;   // minimum keyword hits for rules engine

    // ── Number of candidates to return ──────────────────────────────────────
    private const MAX_CANDIDATES = 5;

    // ════════════════════════════════════════════════════════════════════════
    // MAIN ENTRY POINT
    // ════════════════════════════════════════════════════════════════════════

    /**
     * Predict HS codes for a given item description.
     *
     * Returns array of candidates sorted by duty rate (lowest first):
     * [
     *   [
     *     'HSCode'        => '8703',
     *     'HeadingDesc'   => 'Motor cars — passenger',
     *     'Chapter'       => '87',
     *     'ChapterDesc'   => 'Vehicles',
     *     'ImportDutyRate'=> 20.00,
     *     'VATRate'       => 15.00,
     *     'NHILRate'      => 2.50,
     *     'GETFundRate'   => 2.50,
     *     'COVIDRate'     => 1.00,
     *     'ECOWASRate'    => 0.50,
     *     'AURate'        => 0.20,
     *     'TotalLevyRate' => 41.70,     // sum of all applicable rates
     *     'Confidence'    => 85,         // 0–100
     *     'Source'        => 'rules',    // 'rules' or 'gemini'
     *     'Justification' => '...',      // legal argument
     *     'Exclusions'    => '...',      // what this heading excludes
     *     'IsRecommended' => true,       // lowest duty with strongest argument
     *   ],
     *   ...
     * ]
     */
    public function predict(string $description, ?string $itemType = null): array
    {
        $description = trim($description);

        if (empty($description)) {
            return [];
        }

        // Combine description + itemType for richer matching
        $searchText = $itemType
            ? $description.' '.$itemType
            : $description;

        // ── Step 1: Local rules engine ───────────────────────────────────────
        $rulesResults = $this->runRulesEngine($searchText);

        // ── Step 2: Decide if we need Gemini ────────────────────────────────
        $topConfidence = ! empty($rulesResults) ? $rulesResults[0]['Confidence'] : 0;

        if ($topConfidence < self::RULES_CONFIDENCE_THRESHOLD) {
            // Rules engine not confident enough — call Gemini
            $geminiResults = $this->callGemini($description, $rulesResults);
            if (! empty($geminiResults)) {
                $candidates = $this->mergeResults($rulesResults, $geminiResults);
            } else {
                // Gemini failed or unavailable — use rules results as fallback
                $candidates = $rulesResults;
            }
        } else {
            $candidates = $rulesResults;
        }

        // ── Step 3: Enrich each candidate ────────────────────────────────────
        $candidates = array_map(fn ($c) => $this->enrichCandidate($c), $candidates);

        // ── Step 4: Sort by duty rate (lowest first) ─────────────────────────
        usort($candidates, fn ($a, $b) => $a['ImportDutyRate'] <=> $b['ImportDutyRate']);

        // ── Step 5: Limit to MAX_CANDIDATES ──────────────────────────────────
        $candidates = array_slice($candidates, 0, self::MAX_CANDIDATES);

        // ── Step 6: Mark recommended (lowest duty + highest confidence) ───────
        if (! empty($candidates)) {
            $candidates[0]['IsRecommended'] = true;
        }

        return $candidates;
    }

    // ════════════════════════════════════════════════════════════════════════
    // STEP 1 — LOCAL RULES ENGINE
    // Keyword matching against hs_codes.Keywords column
    // Fast, free, no API calls
    // ════════════════════════════════════════════════════════════════════════

    private function runRulesEngine(string $searchText): array
    {
        $searchText = strtolower($searchText);
        $searchWords = preg_split('/[\s,]+/', $searchText, -1, PREG_SPLIT_NO_EMPTY);

        if (empty($searchWords)) {
            return [];
        }

        // Load all active HS codes with their keywords
        $hsCodes = DB::table('hs_codes')
            ->where('IsActive', 1)
            ->get([
                'id', 'HSCode', 'Chapter', 'ChapterDesc',
                'Heading', 'HeadingDesc', 'ImportDutyRate',
                'VATRate', 'NHILRate', 'GETFundRate',
                'COVIDRate', 'ECOWASRate', 'AURate',
                'Keywords', 'Notes', 'Inclusions', 'Exclusions',
            ]);

        $scored = [];

        foreach ($hsCodes as $code) {
            if (empty($code->Keywords)) {
                continue;
            }

            $keywords = preg_split('/[\s,]+/', strtolower($code->Keywords), -1, PREG_SPLIT_NO_EMPTY);
            $matchCount = 0;
            $matchedWords = [];

            foreach ($searchWords as $word) {
                if (strlen($word) < 2) {
                    continue;
                }
                foreach ($keywords as $keyword) {
                    // Partial match — "toyota" matches "toyota" and "toyota camry"
                    if (str_contains($keyword, $word) || str_contains($word, $keyword)) {
                        $matchCount++;
                        $matchedWords[] = $word;
                        break;
                    }
                }
            }

            if ($matchCount < self::MIN_KEYWORD_MATCHES) {
                continue;
            }

            // Also check heading description for direct word matches
            $headingWords = preg_split('/[\s,]+/', strtolower($code->HeadingDesc), -1, PREG_SPLIT_NO_EMPTY);
            foreach ($searchWords as $word) {
                if (in_array($word, $headingWords)) {
                    $matchCount += 2; // heading match worth double
                    $matchedWords[] = $word.'(heading)';
                }
            }

            // Confidence = (matched keywords / total search words) * 100
            // Capped at 95 — only Gemini can give 100
            $confidence = min(95, (int) round(($matchCount / max(count($searchWords), 1)) * 100));

            $scored[] = [
                'HSCode' => $code->HSCode,
                'Chapter' => $code->Chapter,
                'ChapterDesc' => $code->ChapterDesc,
                'Heading' => $code->Heading,
                'HeadingDesc' => $code->HeadingDesc,
                'ImportDutyRate' => (float) $code->ImportDutyRate,
                'VATRate' => (float) $code->VATRate,
                'NHILRate' => (float) $code->NHILRate,
                'GETFundRate' => (float) $code->GETFundRate,
                'COVIDRate' => (float) $code->COVIDRate,
                'ECOWASRate' => (float) $code->ECOWASRate,
                'AURate' => (float) $code->AURate,
                'Notes' => $code->Notes,
                'Exclusions' => $code->Exclusions,
                'Confidence' => $confidence,
                'Source' => 'rules',
                'MatchedWords' => array_unique($matchedWords),
                'IsRecommended' => false,
                'Justification' => $this->buildRulesJustification(
                    $code->HeadingDesc,
                    $code->HSCode,
                    array_unique($matchedWords),
                    $code->Notes,
                    $code->Exclusions
                ),
            ];
        }

        // Sort by confidence descending
        usort($scored, fn ($a, $b) => $b['Confidence'] <=> $a['Confidence']);

        return array_slice($scored, 0, self::MAX_CANDIDATES * 2); // pass more to Gemini for re-ranking
    }

    // ════════════════════════════════════════════════════════════════════════
    // STEP 2 — GEMINI API
    // Called when rules engine confidence is below threshold
    // Uses existing Gemini integration pattern from the codebase
    // ════════════════════════════════════════════════════════════════════════

    private function callGemini(string $description, array $rulesResults): array
    {
        $apiKey = config('services.gemini.api_key');

        if (empty($apiKey)) {
            Log::warning('HSCodeService: Gemini API key not configured — falling back to rules engine.');

            return [];
        }

        // Build candidate list from rules engine for Gemini to re-rank
        $candidateList = '';
        foreach ($rulesResults as $r) {
            $candidateList .= "\n- {$r['HSCode']}: {$r['HeadingDesc']} (Duty: {$r['ImportDutyRate']}%)";
        }

        // Load all HS headings for Gemini to reference
        $allCodes = DB::table('hs_codes')
            ->where('IsActive', 1)
            ->orderBy('HSCode')
            ->get(['HSCode', 'HeadingDesc', 'Chapter', 'ChapterDesc', 'ImportDutyRate', 'Keywords', 'Exclusions'])
            ->map(fn ($c) => "{$c->HSCode}: {$c->HeadingDesc} (Ch.{$c->Chapter} - {$c->ChapterDesc}, Duty: {$c->ImportDutyRate}%)")
            ->join("\n");

        $prompt = <<<PROMPT
You are an expert customs classifier specializing in Ghana GRA HS Code classification under the ECOWAS Common External Tariff (CET).

ITEM DESCRIPTION: "{$description}"

AVAILABLE HS CODES IN THE SYSTEM:
{$allCodes}

RULES ENGINE SUGGESTIONS (may be incomplete):
{$candidateList}

YOUR TASK:
1. Analyse the item description carefully
2. Identify the TOP 5 most appropriate HS headings from the available codes above
3. For EACH heading provide:
   - The HS code
   - Confidence score (0-100)
   - A detailed legal justification (3-5 sentences) using WCO classification principles
   - Why this heading applies to these specific goods
   - Any counter-argument a customs officer might use for a HIGHER duty code, and how to rebut it

CRITICAL RULES:
- Only use HS codes from the provided list above
- Rank by LOWEST duty rate first (most favourable to importer)
- If the same goods can legitimately fall under two headings, always argue for the lower duty heading
- Use GRI (General Rules for Interpretation) principles in justifications
- Be specific — reference the item description words directly

RESPOND IN THIS EXACT JSON FORMAT (no other text):
{
  "candidates": [
    {
      "HSCode": "8703",
      "Confidence": 92,
      "Justification": "The goods described as [exact words] are classifiable under heading 8703 because...",
      "CounterArgument": "A customs officer may argue heading 8704...",
      "Rebuttal": "However, under GRI 1, the primary function..."
    }
  ]
}
PROMPT;

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->timeout(30)->post(
                "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}",
                [
                    'contents' => [[
                        'parts' => [['text' => $prompt]],
                    ]],
                    'generationConfig' => [
                        'temperature' => 0.1,  // low temp = consistent classification
                        'maxOutputTokens' => 2000,
                    ],
                ]
            );

            if (! $response->successful()) {
                Log::error('HSCodeService: Gemini API error', ['status' => $response->status()]);

                return [];
            }

            $text = $response->json('candidates.0.content.parts.0.text', '');
            $text = preg_replace('/```json|```/', '', $text);
            $data = json_decode(trim($text), true);

            if (! isset($data['candidates'])) {
                return [];
            }

            $results = [];
            foreach ($data['candidates'] as $c) {
                // Look up the full HS code record
                $hsRecord = DB::table('hs_codes')
                    ->where('HSCode', $c['HSCode'])
                    ->where('IsActive', 1)
                    ->first();

                if (! $hsRecord) {
                    continue;
                }

                // Build detailed justification including counter-argument and rebuttal
                $fullJustification = $c['Justification'] ?? '';
                if (! empty($c['CounterArgument'])) {
                    $fullJustification .= "\n\n**Potential customs counter-argument:** ".$c['CounterArgument'];
                }
                if (! empty($c['Rebuttal'])) {
                    $fullJustification .= "\n\n**Agent rebuttal:** ".$c['Rebuttal'];
                }

                $results[] = [
                    'HSCode' => $hsRecord->HSCode,
                    'Chapter' => $hsRecord->Chapter,
                    'ChapterDesc' => $hsRecord->ChapterDesc,
                    'Heading' => $hsRecord->Heading,
                    'HeadingDesc' => $hsRecord->HeadingDesc,
                    'ImportDutyRate' => (float) $hsRecord->ImportDutyRate,
                    'VATRate' => (float) $hsRecord->VATRate,
                    'NHILRate' => (float) $hsRecord->NHILRate,
                    'GETFundRate' => (float) $hsRecord->GETFundRate,
                    'COVIDRate' => (float) $hsRecord->COVIDRate,
                    'ECOWASRate' => (float) $hsRecord->ECOWASRate,
                    'AURate' => (float) $hsRecord->AURate,
                    'Notes' => $hsRecord->Notes,
                    'Exclusions' => $hsRecord->Exclusions,
                    'Confidence' => (int) ($c['Confidence'] ?? 70),
                    'Source' => 'gemini',
                    'MatchedWords' => [],
                    'IsRecommended' => false,
                    'Justification' => $fullJustification,
                ];
            }

            return $results;

        } catch (\Exception $e) {
            Log::error('HSCodeService: Gemini call failed', ['error' => $e->getMessage()]);

            return [];
        }
    }

    // ════════════════════════════════════════════════════════════════════════
    // STEP 3 — MERGE RESULTS
    // Combine rules engine + Gemini, deduplicating by HSCode
    // Gemini results take precedence (higher confidence justification)
    // ════════════════════════════════════════════════════════════════════════

    private function mergeResults(array $rulesResults, array $geminiResults): array
    {
        $merged = [];
        $seen = [];

        // Gemini results first (higher quality justification)
        foreach ($geminiResults as $r) {
            $merged[$r['HSCode']] = $r;
            $seen[] = $r['HSCode'];
        }

        // Add rules results not already covered by Gemini
        foreach ($rulesResults as $r) {
            if (! in_array($r['HSCode'], $seen)) {
                $merged[$r['HSCode']] = $r;
            }
        }

        return array_values($merged);
    }

    // ════════════════════════════════════════════════════════════════════════
    // HELPERS
    // ════════════════════════════════════════════════════════════════════════

    // Calculate total levy rate — all applicable charges
    private function enrichCandidate(array $c): array
    {
        $c['TotalLevyRate'] = round(
            $c['ImportDutyRate'] +
            $c['VATRate'] +
            $c['NHILRate'] +
            $c['GETFundRate'] +
            $c['ECOWASRate'] +
            $c['AURate'],
            2
        );

        return $c;
    }

    // Build a plain-language justification from the rules engine match
    private function buildRulesJustification(
        string $headingDesc,
        string $hsCode,
        array $matchedWords,
        ?string $notes,
        ?string $exclusions
    ): string {
        $words = implode(', ', array_slice($matchedWords, 0, 5));

        $justification = "The goods match heading {$hsCode} ({$headingDesc}) "
            ."based on the following descriptors in the item description: {$words}. ";

        if ($notes) {
            $justification .= "Classification note: {$notes}. ";
        }

        if ($exclusions) {
            $justification .= "Note the following exclusions from this heading: {$exclusions}.";
        }

        return $justification;
    }

    // ════════════════════════════════════════════════════════════════════════
    // DUTY CALCULATION
    // Calculates estimated duty on a CIF value for a given HS code
    // ════════════════════════════════════════════════════════════════════════

    /**
     * Calculate full duty breakdown for a CIF value and HS code.
     *
     * Returns:
     * [
     *   'CIFValue'      => 10000.00,
     *   'ImportDuty'    => 2000.00,  (CIF × ImportDutyRate)
     *   'NHIL'          => 250.00,   (CIF × NHILRate)
     *   'GETFund'       => 250.00,
     *   'COVID'         => 100.00,
     *   'ECOWAS'        => 50.00,
     *   'AULevy'        => 20.00,
     *   'VAT'           => ...,      (VAT applied on CIF + duty + levies)
     *   'TotalDuty'     => ...,
     *   'EffectiveRate' => ...,      (TotalDuty / CIF × 100)
     * ]
     */
    public function calculateDuty(string $hsCode, float $cifValue): array
    {
        $hs = DB::table('hs_codes')
            ->where('HSCode', $hsCode)
            ->where('IsActive', 1)
            ->first();

        if (! $hs || $cifValue <= 0) {
            return [];
        }

        $importDuty = round($cifValue * ($hs->ImportDutyRate / 100), 2);
        $nhil = round($cifValue * ($hs->NHILRate / 100), 2);
        $getFund = round($cifValue * ($hs->GETFundRate / 100), 2);
        $ecowas = round($cifValue * ($hs->ECOWASRate / 100), 2);
        $auLevy = round($cifValue * ($hs->AURate / 100), 2);

        // VAT applied on CIF + import duty + NHIL + GETFund (COVID levy scrapped)
        $vatBase = $cifValue + $importDuty + $nhil + $getFund;
        $vat = round($vatBase * ($hs->VATRate / 100), 2);

        $totalDuty = round($importDuty + $nhil + $getFund + $ecowas + $auLevy + $vat, 2);

        return [
            'HSCode' => $hs->HSCode,
            'HeadingDesc' => $hs->HeadingDesc,
            'CIFValue' => $cifValue,
            'ImportDuty' => $importDuty,
            'NHIL' => $nhil,
            'GETFund' => $getFund,
            'ECOWAS' => $ecowas,
            'AULevy' => $auLevy,
            'VAT' => $vat,
            'TotalDuty' => $totalDuty,
            'EffectiveRate' => $cifValue > 0
                ? round(($totalDuty / $cifValue) * 100, 2)
                : 0,
        ];
    }
}
