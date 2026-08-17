<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Turns extracted Bill of Lading text into the registration fields the form
 * needs — matching names to master data, and working out what the document
 * does not carry at all.
 *
 * Nothing here writes. A value that cannot be established returns null rather
 * than a guess, so a wrong ID is never saved silently.
 */
class RegistrationInferenceService
{
    private const MEDIUM = 0.60;

    /** Extracted field → master data table, matched by name similarity. */
    private const OPTION_SOURCES = [
        'carrier'   => ['table' => 'ship_carrier',   'idCol' => 'CarrierID',   'labelCol' => 'CarrierName', 'field' => 'ShippingLine', 'fallback' => 'VesselName', 'activeCol' => 'Status'],
        'shipper'   => ['table' => 'shipper_main',   'idCol' => 'ShipperID',   'labelCol' => 'ShipperName', 'field' => 'ShipperName', 'activeCol' => 'Status'],
        'pol'       => ['table' => 'pol',            'idCol' => 'POL_ID',      'labelCol' => 'POL_Name',    'field' => 'POL'],
        'pod'       => ['table' => 'pod',            'idCol' => 'POD_ID',      'labelCol' => 'POD_Name',    'field' => 'POD'],
        'consignee' => ['table' => 'consignee_main', 'idCol' => 'ConsigneeID', 'labelCol' => 'FullName',    'field' => 'ConsigneeName', 'activeCol' => 'Status'],
    ];

    private array $settings;

    public function __construct(
        private BLParserService $parser
    ) {
        $this->settings = DB::table('system_settings')
            ->where('group', 'agent')
            ->pluck('value', 'key')
            ->all();
    }

    // ── Entry point ─────────────────────────────────────────────────────────

    /**
     * Everything derivable from a parsed BL: master data matches, commodity,
     * and release type. Callers pass the scored fields from BLParserService.
     */
    public function enrich(array $fields): array
    {
        $matches = $this->matchOptions($fields);

        $matches['commodity']   = ['CmdtCategoryID' => null, 'CmdtTypeID' => null, 'status' => 'empty'];
        $matches['releaseType'] = $this->predictReleaseType(
            (int) ($matches['consignee']['id'] ?? 0),
            (int) ($matches['carrier']['id'] ?? 0)
        );

        return $matches;
    }

    // ── Master data matching ────────────────────────────────────────────────

    private function matchOptions(array $fields): array
    {
        $matches = [];

        foreach (self::OPTION_SOURCES as $key => $source) {
            $options = DB::table($source['table'])
                ->when(isset($source['activeCol']), fn($q) => $q->where($source['activeCol'], 1))
                ->select("{$source['idCol']} as id", "{$source['labelCol']} as label")
                ->get()
                ->map(fn($o) => (array) $o)
                ->all();

            // One row means there is nothing to choose — take it.
            if (count($options) === 1) {
                $matches[$key] = [
                    'id'         => $options[0]['id'],
                    'label'      => $options[0]['label'],
                    'confidence' => 1.0,
                    'status'     => 'ok',
                ];
                continue;
            }

            $text = trim((string) ($fields[$source['field']]['value'] ?? ''));

            if ($text === '' && isset($source['fallback'])) {
                $text = trim((string) ($fields[$source['fallback']]['value'] ?? ''));
            }

            $matches[$key] = $this->parser->matchOption($text, $options);
        }

        return $matches;
    }

    // ── Commodity ───────────────────────────────────────────────────────────

    /** Cargo text against the category/type lists. Null when unresolved. */
    public function inferCommodity(string $cargoText): array
    {
        $empty = ['CmdtCategoryID' => null, 'CmdtTypeID' => null, 'confidence' => 0.0, 'reason' => '', 'status' => 'empty'];

        $cargoText = trim($cargoText);

        if ($cargoText === '') {
            return $empty;
        }

        $types = DB::table('commodity_type as t')
            ->join('commodity_category as c', 'c.ID', '=', 't.CategoryID')
            ->orderBy('c.CategoryName')
            ->get(['t.TypeID', 't.TypeName', 'c.ID as CategoryID', 'c.CategoryName']);

        if ($types->isEmpty()) {
            return $empty;
        }

        $answer = $this->ask($this->commodityPrompt($cargoText, $types));

        if ($answer === null) {
            return array_merge($empty, ['status' => 'low']);
        }

        $confidence = (float) ($answer['Confidence'] ?? 0);
        $reason     = trim((string) ($answer['Reason'] ?? ''));

        // The model picks from a list we supplied — verify it did not invent one.
        $match = $types->firstWhere('TypeID', (int) ($answer['TypeID'] ?? 0));

        if ($match === null || $confidence < self::MEDIUM) {
            return array_merge($empty, ['status' => 'low', 'reason' => $reason]);
        }

        return [
            'CmdtCategoryID' => (int) $match->CategoryID,
            'CmdtTypeID'     => (int) $match->TypeID,
            'CategoryName'   => $match->CategoryName,
            'TypeName'       => $match->TypeName,
            'confidence'     => round($confidence, 2),
            'reason'         => $reason,
            'status'         => $confidence >= 0.85 ? 'ok' : 'review',
        ];
    }

    /** Shipment description, else the container item details. */
    private function cargoText(array $fields): string
    {
        $description = trim((string) ($fields['Description']['value'] ?? ''));

        if ($description !== '') {
            return $description;
        }

        return collect($fields['Containers'] ?? [])
            ->map(fn($c) => trim((string) ($c['ItemDetails']['value'] ?? '')))
            ->filter()
            ->unique()
            ->implode('; ');
    }

    // ── Release type ────────────────────────────────────────────────────────

    /** What this consignee usually uses, else this carrier, else nothing. */
    public function predictReleaseType(int $consigneeId, int $carrierId): array
    {
        $minimum = max(1, (int) ($this->settings['release_type_min_history'] ?? 3));

        $signals = [
            ['ConsigneeID', $consigneeId, 'consignee'],
            ['CarrierID',   $carrierId,   'carrier'],
        ];

        foreach ($signals as [$column, $value, $source]) {

            if ($value <= 0) {
                continue;
            }

            $row = DB::table('container_main')
                ->where($column, $value)
                ->where('Status', '<>', 9)
                ->where('ReleaseType', '>', 0)
                ->groupBy('ReleaseType')
                ->orderByDesc(DB::raw('COUNT(*)'))
                ->select('ReleaseType', DB::raw('COUNT(*) as Uses'))
                ->first();

            if ($row && $row->Uses >= $minimum) {
                return [
                    'ReleaseType' => (int) $row->ReleaseType,
                    'source'      => $source,
                    'count'       => (int) $row->Uses,
                    'status'      => 'review',
                ];
            }
        }

        return ['ReleaseType' => null, 'source' => null, 'count' => 0, 'status' => 'empty'];
    }

    // ── Model call ──────────────────────────────────────────────────────────

    private function ask(string $prompt): ?array
    {
        $key = config('services.anthropic.key', '');

        if (! $key) {
            Log::warning('[RegistrationInference] No Anthropic key configured.');
            return null;
        }

        try {
            $response = Http::withHeaders([
                'x-api-key'         => $key,
                'anthropic-version' => '2023-06-01',
                'content-type'      => 'application/json',
            ])->timeout(30)->post('https://api.anthropic.com/v1/messages', [
                'model'      => $this->settings['registration_inference_model'] ?? 'claude-haiku-4-5',
                'max_tokens' => 300,
                'messages'   => [['role' => 'user', 'content' => $prompt]],
            ]);

            if ($response->failed()) {
                Log::error('[RegistrationInference] API error: ' . $response->body());
                return null;
            }

            return $this->parseJson((string) $response->json('content.0.text'));
        } catch (\Throwable $e) {
            Log::error('[RegistrationInference] Request failed: ' . $e->getMessage());
            return null;
        }
    }

    private function commodityPrompt(string $cargoText, $types): string
    {
        $list = $types
            ->map(fn($t) => "{$t->TypeID}: {$t->CategoryName} / {$t->TypeName}")
            ->implode("\n");

        return "You classify shipped cargo against a fixed list of commodity types.\n\n"
            . "Cargo description:\n{$cargoText}\n\n"
            . "Available types (TypeID: Category / Type):\n{$list}\n\n"
            . "Choose the single best TypeID from the list above. Never invent a TypeID. "
            . "If the description is too vague or nothing fits, return TypeID 0. "
            . "Confidence is 0 to 1 — how certain you are this is the right type.\n\n"
            . 'Return ONLY this JSON, no markdown, no explanation: '
            . '{"TypeID": 0, "Confidence": 0.0, "Reason": "one short sentence"}';
    }

    private function parseJson(string $text): ?array
    {
        $clean = trim(preg_replace('/```json|```/i', '', $text));

        if (preg_match('/\{.*\}/s', $clean, $matches)) {
            $clean = $matches[0];
        }

        $decoded = json_decode($clean, true);

        return (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) ? $decoded : null;
    }
}
