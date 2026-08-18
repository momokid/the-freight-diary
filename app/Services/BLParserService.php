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

    private const HIGH            = 0.85;
    private const MEDIUM          = 0.60;
    private const SHORTLIST_LIMIT = 5;

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

    public function compressForOcr(string $filePath, string $mimeType): array
    {
        if (! extension_loaded('imagick')) {
            return ['path' => $filePath, 'mime' => $mimeType]; // no-op locally
        }

        try {
            $isPdf = $mimeType === 'application/pdf';
            $imagick = new \Imagick();

            if ($isPdf) {
                $imagick->setResolution(150, 150);
                $imagick->readImage($filePath); // reads ALL pages, not just [0]
            } else {
                $imagick->readImage($filePath);
            }

            foreach ($imagick as $page) {
                $page->setImageColorspace(\Imagick::COLORSPACE_GRAY);

                $geometry = $page->getImageGeometry();
                if ($geometry['width'] > 1600) {
                    $page->resizeImage(1600, 0, \Imagick::FILTER_LANCZOS, 1);
                }

                $page->setImageCompressionQuality(80);
            }

            $outputPath = sys_get_temp_dir() . '/' . uniqid('ocr_', true) . ($isPdf ? '.pdf' : '.jpg');

            if ($isPdf) {
                $imagick->setImageFormat('jpeg'); // per-page compression codec inside the PDF
                $imagick->writeImages($outputPath, true); // true = adjoin all pages into one file
            } else {
                $imagick->setImageFormat('jpeg');
                $imagick->writeImage($outputPath);
            }

            $imagick->clear();
            $imagick->destroy();

            return ['path' => $outputPath, 'mime' => $isPdf ? 'application/pdf' : 'image/jpeg'];
        } catch (\Throwable $e) {
            return ['path' => $filePath, 'mime' => $mimeType]; // never block OCR over a compression failure
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
            'and place the combined figure in TotalGrossWeight instead. ' .
            'Container size may be labeled differently depending on the shipping line — look for labels such as ' .
            '"Size/Type", "Type/Size", "Container Type", "Equipment Type", or values embedded directly next to the ' .
            'container number (e.g. "1x40HC", "20\'GP", "40GP"). Extract only the two-digit size number, stripping any suffix such as HC, HQ, GP, RF, or ft ' .
            '(e.g. "40HC" → "40", "20\'GP" → "20", "45HQ" → "45"). ' .
            'Some bills have no container table at all — the details run together on one line in the particulars ' .
            'section, for example "SUDU7657700 ML-AE4299733 20 DRY 8\'6 1 LOT 23580.000 KGS 20.000 CBM". ' .
            'When that is the layout, read the container number, seal number, size, weight and volume from that ' .
            'single line. A container number is four letters followed by seven digits. Never use a voyage number, ' .
            'booking number or B/L number as a container number — if no four-letter-seven-digit code appears, ' .
            'leave ContainerNo as an empty string. ' .
            'Item description may also be labeled differently — look for "Description of Goods", "Commodity", ' .
            '"Cargo Description", "Said to Contain", or similar. If the description is given once for the whole shipment ' .
            'rather than per container, repeat that same description for every container in the Containers array. ' .
            'For the CargoLines array, extract one object per individually identifiable item listed on the document — ' .
            'typically vehicles listed with a chassis or VIN number. A VIN is 17 characters of letters and digits, ' .
            'and may be labelled "VIN", "Chassis No", "Chassis Number" or "Frame No". ' .
            'Copy VINs character for character and never guess a missing character. ' .
            'If the document describes cargo only in bulk, with no per-item listing, return an empty CargoLines array. ' .
            'Never invent lines to match a package count. ' .
            'For ShipmentTypeGuess, judge whether this is a full-container shipment (FCL) or a consolidation ' .
            'carrying cargo for multiple parties (LCL). Indicators of LCL: the description itemises separate ' .
            'consignments, house bill references appear per line, or several distinct consignees are named. ' .
            'Indicators of FCL: one consignee, one description covering the whole shipment, and cargo stated ' .
            'as whole containers. Note that "Shipper\'s Load Stow and Count" and "Said to Contain" appear on ' .
            'both types and are not decisive on their own. ' .
            'State your reasoning in one short sentence, and use "UNKNOWN" when the document does not show enough. ' .
            'Most bills carry a band of small labelled boxes near the signature block, typically including ' .
            '"Carrier\'s Receipt", "Place of Issue of B/L", "Number & Sequence of Original B(s)/L", ' .
            '"Date of Issue of B/L" and "Shipped on Board Date". Read every box in that band and report each ' .
            'labelled value. Two of those boxes often show the same date — that repetition is normal on a bill ' .
            'issued the day the cargo loaded, and is never a reason to omit one of them.' .
            "\n\nReturn exactly this JSON structure:\n" .
            '{
                "MainBL": "Master Bill of Lading number",
                "VesselName": "name of the vessel or ship",
                "ShippingLine": "the carrier/shipping line company name if explicitly stated on the document (e.g. Maersk, MSC, CMA CGM, ONE) — leave empty string if only the vessel name is given and no separate carrier/line name appears",
                "VoyageNo": "voyage number",
                "POIS": "place of issue of the B/L — look for labels such as `Place of Issue of B/L`, `Place and Date of Issue`, or a place named beside the Carrier`s Receipt block. Return the place only, never the date.",
                "DOIS": "date of issue in YYYY-MM-DD format — usually found near the signature block as `Place and Date of Issue`. Leave empty string if not explicitly stated.",
                "SOB": "shipped on board date in YYYY-MM-DD format — look for a field labelled `Shipped on Board Date`, `Shipped on Board`, `Laden on Board` or an on-board stamp. Extract it whenever such a label appears, even if the date shown is the same as the date of issue. Leave empty string only when no on-board label or stamp appears at all — never infer it from the issue date.",
                "POL": "port of loading full name",
                "POD": "port of discharge full name",
                "Destination": "final destination",
                "ETA": "estimated time of arrival in YYYY-MM-DD format — ETA is rarely printed on a Bill of Lading since the BL is issued before the vessel sails. Only extract if explicitly labeled ETA/Estimated Arrival on the document itself. Never calculate, estimate, or infer an ETA from other dates (SOB, DOIS, etc.) — leave empty string if not explicitly stated.",                "ShipperName": "full name of shipper",
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
               "FreightTerms": "PREPAID or COLLECT",
                "ContractNo": "contract or booking reference number if stated",
                "ShipmentTypeGuess": "FCL, LCL or UNKNOWN",
                "ShipmentTypeReason": "one short sentence explaining the judgement",
                "Containers": [
                    {
                        "ContainerNo": "container number",
                        "SealNo": "seal number for this container",
                        "Size": "container size e.g. 20, 40, 40HQ, 40HC",
                        "Weight": "this specific container weight as number only in KG, or empty string if only a combined total is given",
                        "ItemDetails": "description of goods/cargo for this container"
                    }
                ],
                "CargoLines": [
                    {
                        "VIN": "17-character VIN or chassis number exactly as printed, or empty string",
                        "Description": "the item as described on the document",
                        "Make": "manufacturer if identifiable e.g. TOYOTA, HYUNDAI",
                        "Model": "model if identifiable e.g. COROLLA, SANTAFE",
                        "Year": "4-digit year if stated, otherwise empty string",
                        "Weight": "weight of this item as number only in KG if stated per item, otherwise empty string",
                        "ContainerNo": "container this item is in, if the document states it per item"
                    }
                ]
            }';
    }

    private function scoreFields(array $fields): array
    {

        $containers = $fields['Containers'] ?? [];
        unset($fields['Containers']);

        $cargoLines = $fields['CargoLines'] ?? [];
        unset($fields['CargoLines']);

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

        // ── Cargo lines pass through unscored ───────────────────────────────
        $scored['CargoLines'] = is_array($cargoLines) ? array_values($cargoLines) : [];

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
                    // Weight/ItemDetails empty is expected — combined totals or shipment-level
                    // description are common; ContainerNo/SealNo/Size empty is more concerning
                    $confidence = in_array($key, ['Weight', 'ItemDetails']) ? 0.4 : 0.3;
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
            return preg_match('/^\d{2}$/', $value) ? 0.90 : 0.60;
        }

        if ($key === 'Weight') {
            return is_numeric($value) ? 0.90 : 0.55;
        }

        // SealNo — no strict format standard, moderate confidence if present
        return 0.75;
    }


    // Match extracted carrier name against actual carrier list
    public function matchCarrier(string $extractedName, array $carriers): array
    {
        $extractedName = trim($extractedName);

        if ($extractedName === '' || empty($carriers)) {
            return [
                'CarrierID'   => null,
                'CarrierName' => null,
                'confidence'  => 0.0,
                'status'      => 'empty',
            ];
        }

        // ── Exact match (case-insensitive) always wins outright ──
        foreach ($carriers as $carrier) {
            if (strcasecmp(trim($carrier['CarrierName']), $extractedName) === 0) {
                return [
                    'CarrierID'   => $carrier['CarrierID'],
                    'CarrierName' => $carrier['CarrierName'],
                    'confidence'  => 1.0,
                    'status'      => 'ok',
                ];
            }
        }

        // ── No exact match — fall back to similarity scoring ──
        $best = ['CarrierID' => null, 'CarrierName' => null, 'score' => 0.0];

        foreach ($carriers as $carrier) {
            similar_text(
                strtoupper($extractedName),
                strtoupper($carrier['CarrierName']),
                $percent
            );
            $score = $percent / 100;

            if ($score > $best['score']) {
                $best = [
                    'CarrierID'   => $carrier['CarrierID'],
                    'CarrierName' => $carrier['CarrierName'],
                    'score'       => $score,
                ];
            }
        }

        $status = $best['score'] >= self::HIGH   ? 'ok'
            : ($best['score'] >= self::MEDIUM ? 'review' : 'low');

        return [
            'CarrierID'   => $best['CarrierID'],
            'CarrierName' => $best['CarrierName'],
            'confidence'  => round($best['score'], 2),
            'status'      => $status,
        ];
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
    // Company names — carrier, POL. Registered names, written the same way on
    // every document, so exactness carries the weight. A shared industry word
    // like SHIPPING identifies nothing and earns nothing.
    // ─────────────────────────────────────────────────────────────────────────
    public function matchOption(string $extractedValue, array $options): array
    {
        $extractedValue = trim($extractedValue);

        if ($extractedValue === '' || empty($options)) {
            return $this->emptyMatch();
        }

        $exact = $this->exactMatches($extractedValue, $options);

        if (count($exact) === 1) {
            return $this->decided($exact[0], 1.0, 'exact');
        }

        // "CMA" inside "CMA CGM" is the same carrier — but only where one
        // option contains it. ANWAR sits inside ANWARs too.
        $contained = array_values(array_filter(
            $options,
            fn($o) => $this->contains((string) $o['label'], $extractedValue)
        ));

        if (count($contained) === 1) {
            return $this->decided($contained[0], 0.90, 'contained');
        }

        return $this->undecided($contained);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Parties — consignee, shipper. May be a person or a company, and two
    // people can share a name, so nothing here fills a field on similarity
    // alone. Options arrive pre-filtered by the caller's query.
    // ─────────────────────────────────────────────────────────────────────────
    public function matchParty(string $extractedValue, array $options): array
    {
        $extractedValue = trim($extractedValue);

        if ($extractedValue === '' || empty($options)) {
            return $this->emptyMatch();
        }

        $exact = $this->exactMatches($extractedValue, $options);

        if (count($exact) === 1) {
            return $this->decided($exact[0], 1.0, 'exact');
        }

        // Two records with the same name are a question only a person can answer.
        if (count($exact) > 1) {
            return $this->undecided($exact, 'duplicate');
        }

        $frequencies = $this->tokenFrequencies($options);
        $needle      = $this->significantTokens($extractedValue);

        $scored = [];

        foreach ($options as $option) {
            $score = $this->scoreParty($extractedValue, $needle, (string) $option['label'], $frequencies, count($options));

            if ($score > 0) {
                $scored[] = ['id' => $option['id'], 'label' => $option['label'], 'score' => $score];
            }
        }

        $ranked = collect($scored)->sortByDesc('score')->values()->all();

        return $this->undecided($ranked);
    }

    // ── Shared ──────────────────────────────────────────────────────────────

    private function exactMatches(string $value, array $options): array
    {
        return array_values(array_filter(
            $options,
            fn($o) => strcasecmp(trim((string) $o['label']), $value) === 0
        ));
    }

    private function contains(string $label, string $value): bool
    {
        $label = strtoupper(trim($label));
        $value = strtoupper($value);

        return $label !== '' && ($label === $value
            || str_contains($label, $value)
            || str_contains($value, $label));
    }

    private function decided(array $option, float $confidence, string $matchType): array
    {
        return [
            'id'         => $option['id'],
            'label'      => $option['label'],
            'confidence' => $confidence,
            'status'     => 'ok',
            'matchType'  => $matchType,
            'candidates' => [],
        ];
    }

    /** Nothing fills the field — the shortlist is the user's to resolve. */
    private function undecided(array $candidates, string $matchType = 'shortlist'): array
    {
        $shortlist = array_map(fn($row) => [
            'id'    => $row['id'],
            'label' => $row['label'],
        ], array_slice($candidates, 0, self::SHORTLIST_LIMIT));

        return [
            'id'         => null,
            'label'      => null,
            'confidence' => 0.0,
            'status'     => empty($shortlist) ? 'empty' : 'low',
            'matchType'  => empty($shortlist) ? 'none' : $matchType,
            'candidates' => $shortlist,
        ];
    }

    private function emptyMatch(): array
    {
        return [
            'id'         => null,
            'label'      => null,
            'confidence' => 0.0,
            'status'     => 'empty',
            'matchType'  => 'none',
            'candidates' => [],
        ];
    }

    /** Ranks a party name. Order is unreliable; a rare surname is not. */
    private function scoreParty(
        string $extracted,
        array $needle,
        string $label,
        array $frequencies,
        int $total
    ): float {
        $candidate = $this->significantTokens($label);

        if (empty($needle) || empty($candidate)) {
            similar_text(strtoupper($extracted), strtoupper($label), $percent);
            return $percent / 100;
        }

        $matched  = 0.0;
        $possible = 0.0;

        foreach ($needle as $token) {
            $weight    = $this->tokenWeight($token, $frequencies, $total);
            $possible += $weight;

            if (in_array($token, $candidate, true)) {
                $matched += $weight;
            }
        }

        if ($possible <= 0) {
            return 0.0;
        }

        $score = $matched / $possible;

        // Extra words on the candidate dilute the match a little.
        $extra = count(array_diff($candidate, $needle));

        return $score * (1 - min(0.25, $extra * 0.08));
    }

    /** A token shared by half the table says nothing; a rare one says a lot. */
    private function tokenWeight(string $token, array $frequencies, int $total): float
    {
        $seen = $frequencies[$token] ?? 0;

        if ($seen <= 0 || $total <= 0) {
            return 1.0;
        }

        return 1 / (1 + log(1 + ($seen / $total) * 100));
    }

    /** How many option labels each token appears in. */
    private function tokenFrequencies(array $options): array
    {
        $frequencies = [];

        foreach ($options as $option) {
            foreach (array_unique($this->significantTokens((string) $option['label'])) as $token) {
                $frequencies[$token] = ($frequencies[$token] ?? 0) + 1;
            }
        }

        return $frequencies;
    }

    /** Uppercase words, punctuation stripped, legal and filler words dropped. */
    public function significantTokens(string $value): array
    {
        $noise = [
            'LTD',
            'LIMITED',
            'CO',
            'COMPANY',
            'LLC',
            'INC',
            'CORP',
            'CORPORATION',
            'PLC',
            'GMBH',
            'ENTERPRISE',
            'ENTERPRISES',
            'VENTURES',
            'GROUP',
            'THE',
            'AND',
            'OF',
            'FOR',
            'MR',
            'MRS',
            'MISS',
        ];

        $words = preg_split('/[^A-Z0-9]+/', strtoupper($value), -1, PREG_SPLIT_NO_EMPTY) ?: [];

        return array_values(array_filter(
            $words,
            fn($w) => strlen($w) > 1 && ! in_array($w, $noise, true)
        ));
    }


    // Parse JSON from model response
    // Strips markdown fences if present

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
