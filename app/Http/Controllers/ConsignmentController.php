<?php

namespace App\Http\Controllers;

use App\Models\Carrier;
use App\Models\ContainerTemp;
use App\Models\Pod;
use App\Models\Pol;
use App\Models\Shipper;
use App\Services\ReceiptService;
use App\Models\UserAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\ClientNotificationService;

class ConsignmentController extends Controller
{
    public function create()
    {
        $user = Auth::user();

        // Check if user has pending containers in temp table
        $pendingContainers = ContainerTemp::where('Username', $user->ID)->get();
        $pendingBOL        = $pendingContainers->first()?->BOL;

        // Load all dropdowns
        $carriers    = Carrier::active()->orderBy('CarrierName')->get();
        $shippers    = Shipper::active()->orderBy('ShipperName')->get();
        $pols        = Pol::orderBy('POL_Name')->get();
        $pods        = Pod::orderBy('POD_Name')->get();

        // Default handling cost from active handling charge
        $defaultHandlingCost = DB::table('handling_charge')->orderBy('POrder')->value('Amount') ?? 0;

        return view('consignments.create', compact(
            'carriers',
            'shippers',
            'pols',
            'pods',
            'pendingContainers',
            'pendingBOL',
            'defaultHandlingCost'
        ));
    }

    // Add container to staging table
    public function addContainer(Request $request)
    {
        $request->validate([
            'BOL'           => ['required', 'string', 'max:50'],
            'SealNo'        => ['required', 'string', 'max:50'],
            'ContainerNo'   => ['required', 'string', 'max:50'],
            'ContainerSize' => ['required', 'string', 'max:15'],
            'Weight'        => ['required', 'numeric', 'min:0.01'],
            'HandlingCost'  => ['required', 'numeric', 'min:0'],
        ]);

        $user = Auth::user();

        // Check if user already has containers under a different BOL
        $existingBOL = ContainerTemp::where('Username', $user->ID)->value('BOL');
        if ($existingBOL && $existingBOL !== $request->BOL) {
            return response()->json([
                'success' => false,
                'message' => "You have pending containers under BL# {$existingBOL}. Submit or clear them first.",
            ], 409);
        }

        // Check if BL already exists in container_main
        $blExists = DB::table('container_main')->where('BL', $request->BOL)->exists();
        if ($blExists) {
            return response()->json([
                'success' => false,
                'message' => 'This Bill of Lading has already been registered.',
            ], 409);
        }

        // Check duplicate container no under same BOL
        $containerExists = ContainerTemp::where('Username', $user->ID)
            ->where('BOL', $request->BOL)
            ->where('ContainerNo', $request->ContainerNo)
            ->exists();
        if ($containerExists) {
            return response()->json([
                'success' => false,
                'message' => 'This container number has already been added under this BL.',
            ], 409);
        }

        $cleanSize = preg_replace('/[^0-9]/', '', $request->ContainerSize);

        if ($cleanSize === '') {
            return response()->json([
                'success' => false,
                'message' => 'Container Size must contain a valid number (e.g. 20, 40).',
            ], 422);
        }

        ContainerTemp::create([
            'BOL'           => strtoupper(trim($request->BOL)),
            'SealNo'        => strtoupper(trim($request->SealNo)),
            'ContainerNo'   => strtoupper(trim($request->ContainerNo)),
            'ContainerSize' => $cleanSize,
            'Weight'        => $request->Weight,
            'HandlingCost'  => $request->HandlingCost,
            'Username'      => $user->ID,
            'Date'          => now()->toDateString(),
            'Time'          => now()->toDateTimeString(),
        ]);

        $containers = ContainerTemp::where('Username', $user->ID)->get();

        return response()->json([
            'success'    => true,
            'message'    => 'Container added to staging.',
            'containers' => $containers,
            'total'      => $containers->count(),
        ]);
    }

    // Remove container from staging table
    public function removeContainer(Request $request)
    {
        $request->validate([
            'BOL'         => ['required', 'string'],
            'ContainerNo' => ['required', 'string'],
        ]);

        ContainerTemp::where('Username', Auth::user()->ID)
            ->where('BOL', $request->BOL)
            ->where('ContainerNo', $request->ContainerNo)
            ->delete();

        $containers = ContainerTemp::where('Username', Auth::user()->ID)->get();

        return response()->json([
            'success'    => true,
            'containers' => $containers,
            'total'      => $containers->count(),
        ]);
    }

    // Clear all staged containers
    public function clearContainers()
    {
        ContainerTemp::where('Username', Auth::user()->ID)->delete();

        return response()->json(['success' => true, 'message' => 'Staging table cleared.']);
    }

    // Save the consignment
    public function store(Request $request)
    {
        $request->validate([
            'DOT'           => ['required', 'date'],
            'ETA'           => ['required', 'date'],
            'CarrierID'     => ['required', 'integer', 'exists:ship_carrier,CarrierID'],
            'ShipperID'     => ['required', 'integer', 'exists:shipper_main,ShipperID'],
            'VesselName'    => ['required', 'string', 'max:80'],
            'VoyageNo'      => ['nullable', 'string', 'max:80'],
            'BL'            => ['required', 'string', 'max:50', 'unique:container_main,BL'],
            'POIS'          => ['required', 'string', 'max:80'],
            'DOIS'          => ['required', 'date'],
            'SOB'           => ['required', 'date'],
            'POL_ID'        => ['required', 'integer', 'exists:pol,POL_ID'],
            'POD_ID'        => ['required', 'integer', 'exists:pod,POD_ID'],
            'Rotation'      => ['nullable', 'string', 'max:30'],
            'AgentContact'  => ['nullable', 'string', 'max:20'],
            'Destination'   => ['nullable', 'string'],
            'Ownership'     => ['required', 'in:1,2'],
            'ConsigneeID'   => ['nullable', 'integer'],
            'CmdtTypeID'    => ['nullable', 'integer'],
        ]);

        $user = Auth::user();

        // Check pre-requisites
        $handlingAccount = DB::table('handling_charge')->orderBy('POrder')->first();
        $ieAccount       = DB::table('active_ie')->first();
        $vaultAccount    = DB::table('active_vault')->first();

        if (!$handlingAccount) {
            return response()->json(['success' => false, 'message' => 'Handling charges account not configured.'], 422);
        }
        if (!$ieAccount) {
            return response()->json(['success' => false, 'message' => 'Active IE account not configured.'], 422);
        }
        if (!$vaultAccount) {
            return response()->json(['success' => false, 'message' => 'Active Vault account not configured.'], 422);
        }

        // Get staged containers
        $containers = ContainerTemp::where('Username', $user->ID)
            ->where('BOL', $request->BL)
            ->get();

        if ($containers->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No containers found for this BL. Please add containers first.'], 422);
        }

        // Generate receipt number
        $receipt = ReceiptService::generate($request->DOT);

        // Get next ConsignmentID
        $consignmentID = (DB::table('container_main')->max('ConsignmentID') ?? 0) + 1;

        // 4-digit client access code for WhatsApp bot authentication
        $clientCode = app(ClientNotificationService::class)->generateClientCode();

        // Get first container for container_main fields
        $firstContainer = $containers->first();

        DB::beginTransaction();

        try {
            // 1. Insert receipt_main
            DB::table('receipt_main')->insert([
                'ID'        => $receipt['id'],
                'Date'      => $receipt['date'],
                'ReceiptNo' => $receipt['receipt_no'],
                'Username'  => $user->ID,
                'Time'      => now(),
            ]);

            // 2. Insert container_main
            DB::table('container_main')->insert([
                'ConsignmentID' => $consignmentID,
                'CarrierID'     => $request->CarrierID,
                'Rotation'      => strtoupper(trim($request->Rotation)),
                'ShipperID'     => $request->ShipperID,
                'VesselName'    => trim($request->VesselName),
                'VoyageNo'      => trim($request->VoyageNo),
                'SealNo'        => $firstContainer->SealNo,
                'ETA'           => $request->ETA,
                'BL'            => strtoupper(trim($request->BL)),
                'ContainerNo'   => $firstContainer->ContainerNo,
                'ContainerSize' => $firstContainer->ContainerSize,
                'ReceiptNo'     => $receipt['receipt_no'],
                'POIS'          => trim($request->POIS),
                'DOIS'          => $request->DOIS,
                'SOB'           => $request->SOB,
                'POL_ID'        => $request->POL_ID,
                'POD_ID'        => $request->POD_ID,
                'ContWeight'    => $containers->sum('Weight'),
                'Charges'       => $containers->sum('HandlingCost'),
                'AgentContact'  => trim($request->AgentContact ?? ''),
                'Destination'   => trim($request->Destination),
                'Username'      => $user->ID,
                'BranchID'      => $user->BranchID,
                'Date'          => $request->DOT,
                'Time'          => now(),
                'Status'        => 1,
                'CmdtTypeID'    => $request->CmdtTypeID ?? 1,
                'ConsigneeID'   => $request->ConsigneeID ?? 0,
                'ReleaseType'   => 1, // Default — updated later in Cmdts
                'Ownership'     => $request->Ownership,
                'ClientCode'    => $clientCode,
            ]);

            // 3. For each container — insert details + journal + pnl
            foreach ($containers as $container) {
                // a. container_details
                DB::table('container_details')->insert([
                    'ConsignmentID' => $consignmentID,
                    'BL'            => strtoupper(trim($request->BL)),
                    'SealNo'        => $container->SealNo,
                    'ContainerNo'   => $container->ContainerNo,
                    'ContainerSize' => $container->ContainerSize,
                    'Weight'        => $container->Weight,
                    'ItemDetails'   => '',
                    'HandlingCost'  => $container->HandlingCost,
                    'GateOutDate'   => null,
                    'ReturnDate'    => null,
                    'Username'      => $user->ID,
                    'BranchID'      => $user->BranchID,
                    'Date'          => $request->DOT,
                    'Time'          => now(),
                    'Status'        => 1,
                ]);

                // b. Journal Dr — Vault account
                DB::table('journal')->insert([
                    'AccountID'    => $vaultAccount->AccountNo,
                    'SubAccountID' => $vaultAccount->AccountNo,
                    'Mode'         => 'Dr',
                    'TType'        => 'Cash',
                    'ReceiptNo'    => $receipt['receipt_no'],
                    'Dr'           => $container->HandlingCost,
                    'Cr'           => 0,
                    'Description'  => "CONSIGNMENT PROCESSING CHARGES IFO ~ {$container->ContainerNo}~{$request->BL}",
                    'Date'         => $request->DOT,
                    'Time'         => now(),
                    'Username'     => $user->ID,
                    'Authorizer'   => 'N.Auth',
                    'BranchID'     => $user->BranchID,
                    'Status'       => 1,
                ]);

                // c. Journal Cr — IE account
                DB::table('journal')->insert([
                    'AccountID'    => $ieAccount->AccountID,
                    'SubAccountID' => $handlingAccount->AccountNo,
                    'Mode'         => 'Cr',
                    'TType'        => 'Cash',
                    'ReceiptNo'    => $receipt['receipt_no'],
                    'Dr'           => 0,
                    'Cr'           => $container->HandlingCost,
                    'Description'  => "CONSIGNMENT PROCESSING CHARGES IFO ~ {$container->ContainerNo}~{$request->BL}",
                    'Date'         => $request->DOT,
                    'Time'         => now(),
                    'Username'     => $user->ID,
                    'Authorizer'   => 'N.Auth',
                    'BranchID'     => $user->BranchID,
                    'Status'       => 1,
                ]);

                // d. PnL transaction
                DB::table('pnl_transaction')->insert([
                    'AccountID'   => $handlingAccount->AccountNo,
                    'Stamp'       => 'BL',
                    'Mode'        => 'Cr',
                    'MainBL'      => strtoupper(trim($request->BL)),
                    'HouseBL'     => $container->ContainerNo,
                    'ReceiptNo'   => $receipt['receipt_no'],
                    'Description' => "CONSIGNMENT PROCESSING CHARGES IFO ~ {$container->ContainerNo}~{$request->BL}",
                    'Dr'          => 0,
                    'Cr'          => $container->HandlingCost,
                    'Date'        => $request->DOT,
                    'Time'        => now(),
                    'BranchID'    => $user->BranchID,
                    'Username'    => $user->ID,
                    'Status'      => 1,
                ]);
            }

            // 4. Clear temp table
            ContainerTemp::where('Username', $user->ID)->delete();

            DB::commit();

            return response()->json([
                'success'       => true,
                'message'       => 'Consignment registered successfully.',
                'ConsignmentID' => $consignmentID,
                'ReceiptNo'     => $receipt['receipt_no'],
                'BL'            => strtoupper(trim($request->BL)),
                'ClientCode'    => $clientCode,
                'ConsigneeID'   => $request->ConsigneeID ?? 0,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to save consignment. Please try again.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Stage cargo lines extracted from the BL.
     *
     * Held against the user and BL until the consignment is saved, mirroring
     * how container_temp works. Abandoned extractions leave staged rows that
     * are replaced on the next attempt for the same BL.
     */
    private function stageCargoLines(array $lines, string $bl, string $username): void
    {
        $bl = strtoupper(trim($bl));

        if ($bl === '') {
            return;   // no BL yet — nothing to key against
        }

        // Replace any earlier staging for this user and BL
        DB::table('consignment_cargo_lines')
            ->where('Username', $username)
            ->where('BL', $bl)
            ->where('IsStaged', 1)
            ->delete();

        if (empty($lines)) {
            return;
        }

        $now  = now()->toDateTimeString();
        $rows = [];
        $no   = 1;

        foreach ($lines as $line) {
            $description = trim((string) ($line['Description'] ?? ''));
            $vin         = strtoupper(trim((string) ($line['VIN'] ?? '')));

            if ($description === '' && $vin === '') {
                continue;   // nothing usable
            }

            $rows[] = [
                'ConsignmentID' => 0,
                'BL'            => $bl,
                'ContainerNo'   => strtoupper(trim((string) ($line['ContainerNo'] ?? ''))) ?: null,
                'LineNo'        => $no++,
                'VIN'           => $vin !== '' ? $vin : null,
                'Description'   => $description !== '' ? mb_substr($description, 0, 255) : null,
                'Make'          => trim((string) ($line['Make'] ?? '')) ?: null,
                'Model'         => trim((string) ($line['Model'] ?? '')) ?: null,
                'Year'          => preg_match('/^\d{4}$/', trim((string) ($line['Year'] ?? ''))) ? trim($line['Year']) : null,
                'Weight'        => is_numeric($line['Weight'] ?? null) ? (float) $line['Weight'] : null,
                'ItemTypeGuess' => $this->guessItemType($description, $vin),
                'Confidence'    => null,
                'Source'        => 'ocr',
                'IsStaged'      => 1,
                'UsedInManifest' => 0,
                'Username'      => $username,
                'CreatedAt'     => $now,
                'Status'        => 1,
            ];
        }

        if ($rows) {
            DB::table('consignment_cargo_lines')->insert($rows);
        }
    }

    /**
     * Item type from the description.
     *
     * Bike words are checked first because Honda and Suzuki make both cars and
     * motorbikes, so the make alone cannot decide it. A 17-character VIN is
     * treated as vehicular evidence when the description is uninformative.
     */
    private function guessItemType(string $description, string $vin): string
    {
        $text = mb_strtoupper($description);

        if (preg_match('/\b(BIKE|MOTORBIKE|MOTORCYCLE|SCOOTER|MOPED|TRICYCLE|APSONIC|HAOJUE)\b/', $text)) {
            return 'MOTORBIKE';
        }

        $makes = 'TOYOTA|HYUNDAI|KIA|HONDA|NISSAN|FORD|MAZDA|MERCEDES|BENZ|BMW|AUDI|VOLKSWAGEN|VW|'
            . 'CHEVROLET|CHEVY|JEEP|DODGE|LEXUS|ACURA|INFINITI|SUBARU|MITSUBISHI|SUZUKI|LAND ROVER|'
            . 'RANGE ROVER|VOLVO|PEUGEOT|RENAULT|CHRYSLER|CADILLAC|GMC|BUICK|TESLA|PORSCHE|MINI|FIAT';

        if (preg_match('/\b(' . $makes . ')\b/', $text)) {
            return 'VEHICLE';
        }

        // A full-length VIN with no other signal still indicates a vehicle
        if (strlen($vin) === 17) {
            return 'VEHICLE';
        }

        return 'GOODS';
    }

    //extract from BL for container_main fields
    public function extractFromBL(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:10240'],
        ]);

        $file = $request->file('file');
        $hash = hash_file('sha256', $file->getRealPath());

        // ── Cache check ──
        $cached = DB::table('ocr_cache')
            ->where('FileHash', $hash)
            ->where('ExpiresAt', '>', now())
            ->first();

        if ($cached) {
            DB::table('ocr_cache')->where('ID', $cached->ID)->increment('HitCount');
            $result = json_decode($cached->Result, true);

            // Staging must happen on the cached path too — the user still needs
            // these rows even though no extraction ran.
            $this->stageCargoLines(
                $result['fields']['CargoLines'] ?? [],
                $result['fields']['MainBL']['value'] ?? '',
                Auth::user()->ID
            );

            return response()->json(array_merge($result, ['cached' => true]));
        }

        // ── Cache miss — compress (if possible) then extract ──
        $parser = new \App\Services\BLParserService();
        $compressed = $parser->compressForOcr($file->getRealPath(), $file->getMimeType());

        $fileForExtraction = $compressed['path'] === $file->getRealPath()
            ? $file
            : new \Illuminate\Http\UploadedFile(
                $compressed['path'],
                $file->getClientOriginalName(),
                $compressed['mime'],
                null,
                true
            );

        $result = $parser->extract($fileForExtraction);

        if (! $result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Extraction failed.',
            ], 500);
        }

        $containers  = $result['fields']['Containers'] ?? [];
        $totalWeight = $result['fields']['TotalGrossWeight']['value'] ?? '';

        $this->stageCargoLines(
            $result['fields']['CargoLines'] ?? [],
            $result['fields']['MainBL']['value'] ?? '',
            Auth::user()->ID
        );
        if (count($containers) === 1 && empty($containers[0]['Weight']['value']) && $totalWeight !== '') {
            $result['fields']['Containers'][0]['Weight'] = [
                'value'      => $totalWeight,
                'confidence' => 0.75,
                'status'     => 'review',
            ];
        }

        $optionSources = [
            'carrier' => ['table' => 'ship_carrier', 'idCol' => 'CarrierID', 'labelCol' => 'CarrierName', 'field' => 'ShippingLine', 'fallback' => 'VesselName'],
            'shipper' => ['table' => 'shipper_main',  'idCol' => 'ShipperID', 'labelCol' => 'ShipperName', 'field' => 'ShipperName'],
            'pol'     => ['table' => 'pol',           'idCol' => 'POL_ID',    'labelCol' => 'POL_Name',    'field' => 'POL'],
            'pod'     => ['table' => 'pod',           'idCol' => 'POD_ID',    'labelCol' => 'POD_Name',    'field' => 'POD'],
        ];

        $matches = [];
        foreach ($optionSources as $key => $src) {
            $options = DB::table($src['table'])
                ->select("{$src['idCol']} as id", "{$src['labelCol']} as label")
                ->get()
                ->map(fn($o) => (array) $o)
                ->toArray();

            $text = $result['fields'][$src['field']]['value'] ?? '';
            if ($text === '' && isset($src['fallback'])) {
                $text = $result['fields'][$src['fallback']]['value'] ?? '';
            }

            $matches[$key] = $parser->matchOption($text, $options);
        }

        $response = [
            'success'  => true,
            'fields'   => $result['fields'],
            'provider' => $result['provider'],
            'matches'  => $matches,
        ];

        // ── Save to cache ──
        DB::table('ocr_cache')->insert([
            'FileHash'  => $hash,
            'Result'    => json_encode($response),
            'Provider'  => $result['provider'],
            'HitCount'  => 1,
            'CreatedAt' => now(),
            'ExpiresAt' => now()->addDays(30),
        ]);

        return response()->json($response);
    }

    public function sendNotification(Request $request, ClientNotificationService $notification)
    {
        $request->validate([
            'bl'           => ['required', 'string', 'max:50'],
            'client_code'  => ['nullable', 'string', 'size:4'],
            'phone'        => ['required', 'string', 'max:20'],
            'consignee_id' => ['nullable', 'integer'],
            'event'        => ['required', 'in:registration,gate_out,invoice_payment,eta_change,manual'],
            'message'      => ['required_if:event,manual', 'nullable', 'string'],
        ]);

        $user     = Auth::user();
        $userAuth = UserAuth::where('Username', $user->ID)->first();

        if (!$userAuth || !$userAuth->hasPermission('ConsignmentRegister')) {
            return response()->json(['error' => 'Unauthorised'], 403);
        }

        $result = $notification->sendSMS(
            bl: strtoupper(trim($request->bl)),
            phone: $request->phone,
            event: $request->event,
            params: [
                'client_code' => $request->client_code,
                'message'     => $request->message,
            ],
            consigneeId: (int) ($request->consignee_id ?? 0),
            sentBy: $user->ID,
        );

        return response()->json($result, $result['success'] ? 200 : 500);
    }

    public function getClientCode(string $bl): \Illuminate\Http\JsonResponse
    {
        $bl = strtoupper(trim($bl));

        $consignment = DB::table('container_main')
            ->where('BL', $bl)
            ->select('ClientCode', 'ConsigneeID')
            ->first();

        if (!$consignment) {
            return response()->json(['success' => false], 404);
        }

        return response()->json([
            'success'      => true,
            'client_code'  => $consignment->ClientCode,
            'consignee_id' => $consignment->ConsigneeID,
        ]);
    }
}
