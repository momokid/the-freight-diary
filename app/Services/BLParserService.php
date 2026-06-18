<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BLParserService
{
    // ─────────────────────────────────────────────────────────────────────────
    // Config
    // ─────────────────────────────────────────────────────────────────────────
    private string $provider;

    private string $claudeKey;
    private string $claudeModel;

    private string $groqKey;
    private string $groqModel;

    private const HIGH   = 0.85;
    private const MEDIUM = 0.60;

    public function __construct()
    {
        $this->provider   = config('services.bl_parser.provider', 'claude');

        $this->claudeKey  = config('services.anthropic.key',   '');
        $this->claudeModel = config('services.anthropic.model', 'claude-haiku-4-5');

        $this->groqKey    = config('services.groq.key',        '');
        $this->groqModel  = config('services.groq.model',      'llama-3.2-90b-vision-preview');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Public entry point
    // ─────────────────────────────────────────────────────────────────────────
    public function extract(\Illuminate\Http\UploadedFile $file): array
    {
        $mimeType = $file->getMimeType();
        $isPdf    = $mimeType === 'application/pdf';

        // ── Primary provider ──────────────────────────────────────────────
        $result = match ($this->provider) {
            'claude' => $this->extractViaClaude($file, $mimeType),
            'groq'   => $this->extractViaGroq($file, $mimeType, $isPdf),
            default  => $this->extractViaClaude($file, $mimeType),
        };

        // ── Fallback to Groq if Claude fails ──────────────────────────────
        if (! $result['success'] && $this->provider !== 'groq') {
            Log::warning('[BLParser] Claude failed, trying Groq fallback.', [
                'message' => $result['message'] ?? '',
            ]);

            // Groq does not support PDF natively
            if ($isPdf) {
                return [
                    'success' => false,
                    'message' => 'Extraction failed. Please upload the BL as a JPG or PNG image and try again.',
                ];
            }

            $result = $this->extractViaGroq($file, $mimeType, $isPdf);
        }

        if (! $result['success']) {
            return [
                'success' => false,
                'message' => $result['message'] ?? 'Extraction failed. Please try again or fill the form manually.',
            ];
        }

        return [
            'success'  => true,
            'fields'   => $this->scoreFields($result['fields']),
            'provider' => $result['provider'],
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Claude — primary provider
    // Supports PDF and images natively
    // ─────────────────────────────────────────────────────────────────────────
    private function extractViaClaude(
        \Illuminate\Http\UploadedFile $file,
        string $mimeType
    ): array {
        if (! $this->claudeKey) {
            return ['success' => false, 'message' => 'Claude API key not configured.'];
        }

        try {
            $base64  = base64_encode(file_get_contents($file->getRealPath()));
            $isPdf   = $mimeType === 'application/pdf';

            // PDF uses type=document, images use type=image
            $filePart = $isPdf
                ? [
                    'type'   => 'document',
                    'source' => [
                        'type'       => 'base64',
                        'media_type' => 'application/pdf',
                        'data'       => $base64,
                    ],
                ]
                : [
                    'type'   => 'image',
                    'source' => [
                        'type'       => 'base64',
                        'media_type' => $mimeType,
                        'data'       => $base64,
                    ],
                ];

            $response = Http::withHeaders([
                'x-api-key'         => $this->claudeKey,
                'anthropic-version' => '2023-06-01',
                'content-type'      => 'application/json',
            ])->timeout(60)->post('https://api.anthropic.com/v1/messages', [
                'model'      => $this->claudeModel,
                'max_tokens' => 1024,
                'messages'   => [
                    [
                        'role'    => 'user',
                        'content' => [
                            $filePart,
                            [
                                'type' => 'text',
                                'text' => $this->buildPrompt(),
                            ],
                        ],
                    ],
                ],
            ]);

            if ($response->failed()) {
                Log::error('[BLParser] Claude API error: ' . $response->body());
                return [
                    'success' => false,
                    'message' => 'Claude extraction failed: ' . $response->status(),
                ];
            }

            $text   = $response->json('content.0.text');
            $fields = $this->parseJson($text);

            if (! $fields) {
                Log::error('[BLParser] Claude response not parseable: ' . $text);
                return ['success' => false, 'message' => 'Could not parse Claude response.'];
            }

            return ['success' => true, 'fields' => $fields, 'provider' => 'claude'];
        } catch (\Exception $e) {
            Log::error('[BLParser] Claude exception: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Claude request failed.'];
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Groq — fallback provider
    // Images only — PDF not supported
    // ─────────────────────────────────────────────────────────────────────────
    private function extractViaGroq(
        \Illuminate\Http\UploadedFile $file,
        string $mimeType,
        bool $isPdf
    ): array {
        if (! $this->groqKey) {
            return ['success' => false, 'message' => 'Groq API key not configured.'];
        }

        if ($isPdf) {
            return ['success' => false, 'message' => 'Groq does not support PDF files.'];
        }

        try {
            $base64  = base64_encode(file_get_contents($file->getRealPath()));
            $dataUrl = "data:{$mimeType};base64,{$base64}";

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->groqKey,
                'Content-Type'  => 'application/json',
            ])->timeout(60)->post('https://api.groq.com/openai/v1/chat/completions', [
                'model'       => $this->groqModel,
                'messages'    => [[
                    'role'    => 'user',
                    'content' => [
                        [
                            'type'      => 'image_url',
                            'image_url' => ['url' => $dataUrl],
                        ],
                        [
                            'type' => 'text',
                            'text' => $this->buildPrompt(),
                        ],
                    ],
                ]],
                'temperature' => 0,
                'max_tokens'  => 1024,
            ]);

            if ($response->failed()) {
                Log::error('[BLParser] Groq API error: ' . $response->body());
                return [
                    'success' => false,
                    'message' => 'Groq extraction failed: ' . $response->status(),
                ];
            }

            $text   = $response->json('choices.0.message.content');
            $fields = $this->parseJson($text);

            if (! $fields) {
                Log::error('[BLParser] Groq response not parseable: ' . $text);
                return ['success' => false, 'message' => 'Could not parse Groq response.'];
            }

            return ['success' => true, 'fields' => $fields, 'provider' => 'groq'];
        } catch (\Exception $e) {
            Log::error('[BLParser] Groq exception: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Groq request failed.'];
        }
    }


    // Prompt
    private function buildPrompt(): string
    {
        return 'You are a Bill of Lading data extraction assistant. ' .
            'Extract the following fields from this Bill of Lading document. ' .
            'Return ONLY a valid JSON object with no markdown, no code blocks, no explanation. ' .
            'If a field is not found or not applicable, use empty string "". ' .
            'For the Containers array, create one object per container found on the document. ' .
            'If a container has its own weight stated explicitly on the document, use that exact value. ' .
            'If only a combined total weight is given for all containers, leave each container Weight as empty string "" ' .
            'and place the combined figure in TotalGrossWeight instead.' .
            "\n\nReturn exactly this JSON structure:\n" .
            '{
                "MainBL": "Master Bill of Lading number",
                "VesselName": "name of the vessel or ship",
                "VoyageNo": "voyage number",
                "POIS": "place of issue",
                "DOIS": "date of issue in YYYY-MM-DD format",
                "SOB": "shipped on board date in YYYY-MM-DD format",
                "POL": "port of loading full name",
                "POD": "port of discharge full name",
                "Destination": "final destination",
                "ETA": "estimated time of arrival in YYYY-MM-DD format if stated",
                "ShipperName": "full name of shipper",
                "ShipperAddress": "shipper address",
                "ConsigneeName": "full name of consignee",
                "ConsigneeAddress": "consignee address",
                "ConsigneeTel": "consignee telephone number",
                "NotifyParty": "notify party name and address",
                "Description": "cargo or commodity description",
                "HSCode": "HS code if stated",
                "TotalGrossWeight": "combined gross weight as number only in KG, for all containers",
                "TotalVolume": "combined volume in CBM as number only if stated",
                "Packages": "number of packages as number only",
                "PackageUnit": "unit e.g. CARTONS, PALLETS, PIECES",
                "MarksAndNumbers": "marks and numbers on cargo if stated",
                "FreightTerms": "PREPAID or COLLECT",
                "ContractNo": "contract or booking reference number if stated",
                "Containers": [
                    {
                        "ContainerNo": "container number",
                        "SealNo": "seal number for this container",
                        "Size": "container size e.g. 20, 40, 40HQ, 40HC",
                        "Weight": "this specific container weight as number only in KG, or empty string if only a combined total is given"
                    }
                ]
            }';
    }

    private function scoreFields(array $fields): array
    {

        $containers = $fields['Containers'] ?? [];
        unset($fields['Containers']);

        $scored     = [];
        $critical   = ['MainBL', 'ConsigneeName', 'POL', 'POD'];
        $expected   = [
            'MainBL',
            'VesselName',
            'POL',
            'POD',
            'ShipperName',
            'ConsigneeName',
            'TotalGrossWeight',
            'Description',
            'FreightTerms',
        ];

        $dateFields = ['DOIS', 'SOB', 'ETA'];

        // ── Score every normal field exactly as before ─────────────────────
        foreach ($fields as $key => $value) {
            $value = trim((string) $value);

            if ($value === '') {
                $confidence = in_array($key, $expected) ? 0.3 : 0.5;
                $status     = 'empty';
            } else {
                if (in_array($key, $dateFields)) {
                    if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
                        try {
                            $value = \Carbon\Carbon::parse($value)->format('Y-m-d');
                        } catch (\Exception $e) {
                            // Unparseable — leave as-is, scoreValue gives low confidence
                        }
                    }
                }

                $confidence = $this->scoreValue($key, $value, $critical);
                $status     = $confidence >= self::HIGH   ? 'ok'
                    : ($confidence >= self::MEDIUM ? 'review' : 'low');
            }

            $scored[$key] = [
                'value'      => $value,
                'confidence' => $confidence,
                'status'     => $status,
            ];
        }

        // ── Score each container separately ─────────────────────────────────
        $scored['Containers'] = $this->scoreContainers($containers);

        return $scored;
    }

    private function scoreContainers(array $containers): array
    {
        $scored = [];

        foreach ($containers as $container) {
            $scoredContainer = [];

            foreach ($container as $key => $value) {
                $value = trim((string) $value);

                if ($value === '') {
                    // Weight empty is expected when BL only gives a combined total
                    // ContainerNo/SealNo/Size empty is more concerning
                    $confidence = $key === 'Weight' ? 0.4 : 0.3;
                    $status     = 'empty';
                } else {
                    $confidence = $this->scoreContainerValue($key, $value);
                    $status     = $confidence >= self::HIGH   ? 'ok'
                        : ($confidence >= self::MEDIUM ? 'review' : 'low');
                }

                $scoredContainer[$key] = [
                    'value'      => $value,
                    'confidence' => $confidence,
                    'status'     => $status,
                ];
            }

            $scored[] = $scoredContainer;
        }

        return $scored;
    }

    private function scoreContainerValue(string $key, string $value): float
    {
        if ($key === 'ContainerNo') {
            return preg_match('/^[A-Z]{4}\d{7}$/', $value) ? 0.95 : 0.60;
        }

        if ($key === 'Size') {
            return preg_match('/^\d{2}[A-Z]{0,2}$/', $value) ? 0.90 : 0.60;
        }

        if ($key === 'Weight') {
            return is_numeric($value) ? 0.90 : 0.55;
        }

        // SealNo — no strict format standard, moderate confidence if present
        return 0.75;
    }

    private function scoreValue(string $key, string $value, array $critical): float
    {
        if (in_array($key, ['DOIS', 'SOB', 'ETA'])) {
            return preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) ? 0.90 : 0.55;
        }

        if (in_array($key, ['GrossWeight', 'NetWeight', 'Volume', 'Packages'])) {
            return is_numeric($value) ? 0.90 : 0.55;
        }

        if ($key === 'MainBL') {
            $len = strlen($value);
            return ($len >= 6 && $len <= 30 && preg_match('/^[A-Z0-9\-\/]+$/i', $value))
                ? 0.90 : 0.60;
        }

        if (in_array($key, $critical)) return 0.80;

        return 0.75;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Parse JSON from model response
    // Strips markdown fences if present
    // ─────────────────────────────────────────────────────────────────────────
    private function parseJson(string $text): ?array
    {
        $clean = preg_replace('/```json|```/i', '', $text);
        $clean = trim($clean);

        if (preg_match('/\{.*\}/s', $clean, $matches)) {
            $clean = $matches[0];
        }

        $decoded = json_decode($clean, true);
        return (json_last_error() === JSON_ERROR_NONE && is_array($decoded))
            ? $decoded
            : null;
    }
}
