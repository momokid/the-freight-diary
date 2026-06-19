<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HSCodeService
{
    // ── Confidence thresholds ─────────────────────────────────────────────
    private const RULES_CONFIDENCE_THRESHOLD = 70;
    private const MIN_KEYWORD_MATCHES        = 2;
    private const MAX_CANDIDATES             = 10;

    // ── Claude config ─────────────────────────────────────────────────────
    private string $claudeKey;
    private string $claudeModel;
    private string $claudeFallbackModel;

    public function __construct()
    {
        $this->claudeKey  = config('services.anthropic.key', '');
        $this->claudeModel = config('services.hs_advisor.model', 'claude-sonnet-4-6');
        $this->claudeFallbackModel = config('services.hs_advisor.fallback_model', 'claude-haiku-4-5');
    }


    public function predict(string $description, ?string $itemType = null): array
    {
        $description = trim($description);
        if (empty($description)) return [];

        $searchText = $itemType
            ? $description . ' ' . $itemType
            : $description;

        // ── Step 1: Local rules engine ────────────────────────────────────
        $rulesResults = $this->runRulesEngine($searchText);

        // ── Step 2: Decide if we need Claude ─────────────────────────────
        $topConfidence = !empty($rulesResults) ? $rulesResults[0]['Confidence'] : 0;

        if ($topConfidence < self::RULES_CONFIDENCE_THRESHOLD) {
            $claudeResults = $this->callClaude($description, $rulesResults);
            if (!empty($claudeResults)) {
                $candidates = $this->mergeResults($rulesResults, $claudeResults);
            } else {
                $candidates = $rulesResults;
            }
        } else {
            $candidates = $rulesResults;
        }

        // ── Step 3: Enrich each candidate ─────────────────────────────────
        $candidates = array_map(fn($c) => $this->enrichCandidate($c), $candidates);

        // ── Step 4: Sort by duty rate (lowest first) ──────────────────────
        usort($candidates, fn($a, $b) => $a['ImportDutyRate'] <=> $b['ImportDutyRate']);

        // ── Step 5: Limit to MAX_CANDIDATES ──────────────────────────────
        $candidates = array_slice($candidates, 0, self::MAX_CANDIDATES);

        // ── Step 6: Mark recommended ──────────────────────────────────────
        if (!empty($candidates)) {
            $candidates[0]['IsRecommended'] = true;
        }

        return $candidates;
    }


    private function runRulesEngine(string $searchText): array
    {
        $searchText  = strtolower($searchText);
        $searchWords = preg_split('/[\s,]+/', $searchText, -1, PREG_SPLIT_NO_EMPTY);

        if (empty($searchWords)) return [];

        $hsCodes = DB::table('hs_codes')
            ->where('IsActive', 1)
            ->get([
                'id',
                'HSCode',
                'Chapter',
                'ChapterDesc',
                'Heading',
                'HeadingDesc',
                'ImportDutyRate',
                'VATRate',
                'NHILRate',
                'GETFundRate',
                'COVIDRate',
                'ECOWASRate',
                'AURate',
                'Keywords',
                'Notes',
                'Inclusions',
                'Exclusions',
            ]);

        $scored = [];

        foreach ($hsCodes as $code) {
            if (empty($code->Keywords)) continue;

            $keywords    = preg_split('/[\s,]+/', strtolower($code->Keywords), -1, PREG_SPLIT_NO_EMPTY);
            $matchCount  = 0;
            $matchedWords = [];

            foreach ($searchWords as $word) {
                if (strlen($word) < 2) continue;
                foreach ($keywords as $keyword) {
                    if (str_contains($keyword, $word) || str_contains($word, $keyword)) {
                        $matchCount++;
                        $matchedWords[] = $word;
                        break;
                    }
                }
            }

            if ($matchCount < self::MIN_KEYWORD_MATCHES) continue;

            // Heading description match worth double
            $headingWords = preg_split('/[\s,]+/', strtolower($code->HeadingDesc), -1, PREG_SPLIT_NO_EMPTY);
            foreach ($searchWords as $word) {
                if (in_array($word, $headingWords)) {
                    $matchCount   += 2;
                    $matchedWords[] = $word . '(heading)';
                }
            }

            $confidence = min(95, (int) round(($matchCount / max(count($searchWords), 1)) * 100));

            $scored[] = [
                'HSCode'        => $code->HSCode,
                'Chapter'       => $code->Chapter,
                'ChapterDesc'   => $code->ChapterDesc,
                'Heading'       => $code->Heading,
                'HeadingDesc'   => $code->HeadingDesc,
                'ImportDutyRate' => (float) $code->ImportDutyRate,
                'VATRate'       => (float) $code->VATRate,
                'NHILRate'      => (float) $code->NHILRate,
                'GETFundRate'   => (float) $code->GETFundRate,
                'COVIDRate'     => (float) $code->COVIDRate,
                'ECOWASRate'    => (float) $code->ECOWASRate,
                'AURate'        => (float) $code->AURate,
                'Notes'         => $code->Notes,
                'Exclusions'    => $code->Exclusions,
                'Confidence'    => $confidence,
                'Source'        => 'rules',
                'MatchedWords'  => array_unique($matchedWords),
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

        usort($scored, fn($a, $b) => $b['Confidence'] <=> $a['Confidence']);

        return array_slice($scored, 0, self::MAX_CANDIDATES * 2);
    }


    private function callClaude(string $description, array $rulesResults): array
    {
        if (empty($this->claudeKey)) {
            Log::warning('[HSCodeService] Claude API key not configured — using rules engine only.');
            return [];
        }

        // Build candidate context from rules engine top 10
        $candidateContext = '';
        foreach (array_slice($rulesResults, 0, 10) as $r) {
            $candidateContext .= "\n- {$r['HSCode']}: {$r['HeadingDesc']} "
                . "(Duty: {$r['ImportDutyRate']}%, Confidence: {$r['Confidence']}%)";
        }

        $prompt = <<<PROMPT
                You are an expert customs classification specialist with deep knowledge of the WCO Harmonized System (HS) nomenclature, the ECOWAS Common External Tariff (CET), and Ghana Revenue Authority (GRA/CEPS) tariff schedules.

                ITEM DESCRIPTION: "{$description}"

                RULES ENGINE CANDIDATES (pre-filtered from PSIL's HS code database):
                {$candidateContext}

                YOUR TASK:
                1. Analyse the item description carefully against the candidates above
                2. Select the TOP 5 most appropriate HS codes from the candidates
                3. Re-rank them by LOWEST duty rate first (most favourable to the importer)
                4. For each code provide:
                - A confidence score (0-100) based on how well it matches the description
                - A detailed legal justification (3-5 sentences) using WCO GRI principles
                - A counter-argument a GRA customs officer might use for a HIGHER duty code
                - A rebuttal to that counter-argument
                5. Eliminate candidates that clearly do not match the item description
                6. If fewer than 5 candidates are appropriate, return only the appropriate ones

                CRITICAL RULES:
                - Only use HS codes from the candidates list above
                - Rank by LOWEST duty rate first
                - Be specific — reference the exact words from the item description
                - Use GRI (General Rules of Interpretation) principles in justifications
                - Confidence must reflect LEGAL defensibility, not just keyword matching

                RESPOND IN THIS EXACT JSON FORMAT (no markdown, no preamble, no explanation):
                {
                "candidates": [
                    {
                    "HSCode": "1701",
                    "Confidence": 92,
                    "Justification": "The goods described as [exact words] are classifiable under heading 1701 because...",
                    "CounterArgument": "A GRA customs officer may argue heading...",
                    "Rebuttal": "However, under GRI 1, the primary classification criteria..."
                    }
                ]
                }
                PROMPT;

        // Try primary model first, fall back to Haiku if overloaded
        foreach ([$this->claudeModel, $this->claudeFallbackModel] as $model) {
            try {
                $response = Http::withHeaders([
                    'x-api-key'         => $this->claudeKey,
                    'anthropic-version' => '2023-06-01',
                    'content-type'      => 'application/json',
                ])->timeout(45)->post('https://api.anthropic.com/v1/messages', [
                    'model'      => $model,
                    'max_tokens' => 2000,
                    'messages'   => [[
                        'role'    => 'user',
                        'content' => $prompt,
                    ]],
                ]);

                if ($response->status() === 529) {
                    // Overloaded — try fallback model
                    Log::warning("[HSCodeService] {$model} overloaded, trying fallback.");
                    continue;
                }

                if (!$response->successful()) {
                    Log::error('[HSCodeService] Claude API error: ' . $response->body());
                    return [];
                }

                $text = $response->json('content.0.text', '');
                $text = preg_replace('/```json|```/i', '', $text);

                // Extract JSON object
                if (preg_match('/\{.*\}/s', $text, $matches)) {
                    $text = $matches[0];
                }

                $data = json_decode(trim($text), true);

                if (!isset($data['candidates']) || !is_array($data['candidates'])) {
                    Log::error('[HSCodeService] Claude response not parseable: ' . $text);
                    return [];
                }

                $results = [];
                foreach ($data['candidates'] as $c) {
                    // Look up the full HS code record from our database
                    $hsRecord = DB::table('hs_codes')
                        ->where('HSCode', $c['HSCode'])
                        ->where('IsActive', 1)
                        ->first();

                    if (!$hsRecord) continue;

                    // Build full justification including counter-argument and rebuttal
                    $fullJustification = $c['Justification'] ?? '';
                    if (!empty($c['CounterArgument'])) {
                        $fullJustification .= "\n\n**GRA Counter-argument:** " . $c['CounterArgument'];
                    }
                    if (!empty($c['Rebuttal'])) {
                        $fullJustification .= "\n\n**PSIL Rebuttal:** " . $c['Rebuttal'];
                    }

                    $results[] = [
                        'HSCode'         => $hsRecord->HSCode,
                        'Chapter'        => $hsRecord->Chapter,
                        'ChapterDesc'    => $hsRecord->ChapterDesc,
                        'Heading'        => $hsRecord->Heading,
                        'HeadingDesc'    => $hsRecord->HeadingDesc,
                        'ImportDutyRate' => (float) $hsRecord->ImportDutyRate,
                        'VATRate'        => (float) $hsRecord->VATRate,
                        'NHILRate'       => (float) $hsRecord->NHILRate,
                        'GETFundRate'    => (float) $hsRecord->GETFundRate,
                        'COVIDRate'      => (float) $hsRecord->COVIDRate,
                        'ECOWASRate'     => (float) $hsRecord->ECOWASRate,
                        'AURate'         => (float) $hsRecord->AURate,
                        'Notes'          => $hsRecord->Notes,
                        'Exclusions'     => $hsRecord->Exclusions,
                        'Confidence'     => (int) ($c['Confidence'] ?? 70),
                        'Source'         => 'analysis',
                        'MatchedWords'   => [],
                        'IsRecommended'  => false,
                        'Justification'  => $fullJustification,
                    ];
                }

                return $results;
            } catch (\Exception $e) {
                Log::error('[HSCodeService] Claude exception: ' . $e->getMessage());
                return [];
            }
        }

        return [];
    }


    private function mergeResults(array $rulesResults, array $claudeResults): array
    {
        $merged = [];
        $seen   = [];

        // Claude first — higher quality justification
        foreach ($claudeResults as $r) {
            $merged[$r['HSCode']] = $r;
            $seen[]               = $r['HSCode'];
        }

        // Rules engine fills gaps
        foreach ($rulesResults as $r) {
            if (!in_array($r['HSCode'], $seen)) {
                $merged[$r['HSCode']] = $r;
            }
        }

        return array_values($merged);
    }

    // HELPERS
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

    private function buildRulesJustification(
        string $headingDesc,
        string $hsCode,
        array  $matchedWords,
        ?string $notes,
        ?string $exclusions
    ): string {
        $words = implode(', ', array_slice($matchedWords, 0, 5));
        $justification = "The goods match heading {$hsCode} ({$headingDesc}) "
            . "based on the following descriptors: {$words}. ";

        if ($notes) {
            $justification .= "Classification note: {$notes}. ";
        }
        if ($exclusions) {
            $justification .= "Note the following exclusions: {$exclusions}.";
        }

        return $justification;
    }

    // DUTY CALCULATION
    public function calculateDuty(string $hsCode, float $cifValue): array
    {
        $hs = DB::table('hs_codes')
            ->where('HSCode', $hsCode)
            ->where('IsActive', 1)
            ->first();

        if (!$hs || $cifValue <= 0) return [];

        $importDuty = round($cifValue * ($hs->ImportDutyRate / 100), 2);
        $nhil       = round($cifValue * ($hs->NHILRate / 100), 2);
        $getFund    = round($cifValue * ($hs->GETFundRate / 100), 2);
        $ecowas     = round($cifValue * ($hs->ECOWASRate / 100), 2);
        $auLevy     = round($cifValue * ($hs->AURate / 100), 2);

        // VAT base = CIF + Import Duty + NHIL + GETFund
        $vatBase = $cifValue + $importDuty + $nhil + $getFund;
        $vat     = round($vatBase * ($hs->VATRate / 100), 2);

        $totalDuty = round($importDuty + $nhil + $getFund + $ecowas + $auLevy + $vat, 2);

        return [
            'HSCode'        => $hs->HSCode,
            'HeadingDesc'   => $hs->HeadingDesc,
            'CIFValue'      => $cifValue,
            'ImportDuty'    => $importDuty,
            'NHIL'          => $nhil,
            'GETFund'       => $getFund,
            'ECOWAS'        => $ecowas,
            'AULevy'        => $auLevy,
            'VAT'           => $vat,
            'TotalDuty'     => $totalDuty,
            'EffectiveRate' => $cifValue > 0
                ? round(($totalDuty / $cifValue) * 100, 2)
                : 0,
        ];
    }
}
