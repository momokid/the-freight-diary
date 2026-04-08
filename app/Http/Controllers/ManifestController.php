<?php

namespace App\Http\Controllers;

use App\Models\Consignee;
use App\Models\ManifestBreakdown;
use App\Models\ManifestTemp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ManifestController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Check if user has a pending manifest in temp table
        $pendingItems = ManifestTemp::where('Username', $user->ID)->get();
        $pendingBOL   = $pendingItems->first()?->MainBL;

        // Load pending consignment info if exists
        $pendingConsignment = null;
        if ($pendingBOL) {
            $pendingConsignment = DB::table('container_main as cm')
                ->join('shipper_main as s', 'cm.ShipperID', '=', 's.ShipperID')
                ->where('cm.BL', $pendingBOL)
                ->select(
                    'cm.ConsignmentID',
                    'cm.BL as MainBL',
                    'cm.VesselName',
                    'cm.ContWeight',
                    's.ShipperName',
                )
                ->first();
        }

        return view('consignments.manifest', compact(
            'pendingItems',
            'pendingBOL',
            'pendingConsignment'
        ));
    }

    // Search consignment by Main BL
    public function search(Request $request)
    {
        $request->validate([
            'BL' => ['required', 'string', 'max:50'],
        ]);

        $bl = strtoupper(trim($request->BL));

        $consignment = DB::table('container_main as cm')
            ->join('shipper_main as s', 'cm.ShipperID', '=', 's.ShipperID')
            ->where('cm.BL', $bl)
            ->where('cm.Status', 1)
            ->select(
                'cm.ConsignmentID',
                'cm.BL as MainBL',
                'cm.VesselName',
                'cm.ContWeight',
                'cm.ETA',
                's.ShipperName',
            )
            ->first();

        if (!$consignment) {
            return response()->json([
                'success' => false,
                'message' => 'Consignment not found for BL# ' . $bl,
            ], 404);
        }

        // Get all container details for this consignment
        $containers = DB::table('container_details')
            ->where('ConsignmentID', $consignment->ConsignmentID)
            ->where('Status', 1)
            ->get(['ContainerNo', 'ContainerSize', 'Weight']);

        // Check if manifest already exists
        $manifestExists = ManifestBreakdown::where('MainBL', $bl)->exists();
        if ($manifestExists) {
            return response()->json([
                'success' => false,
                'message' => 'A manifest already exists for BL# ' . $bl . '. Check the existing manifest.',
            ], 409);
        }

        // Check if another user has this BL in temp
        $tempExists = ManifestTemp::where('MainBL', $bl)
            ->where('Username', '!=', Auth::user()->ID)
            ->exists();
        if ($tempExists) {
            return response()->json([
                'success' => false,
                'message' => 'This BL is currently being processed by another user.',
            ], 409);
        }

        // Check if current user has a different BL in temp
        $userTemp = ManifestTemp::where('Username', Auth::user()->ID)
            ->where('MainBL', '!=', $bl)
            ->first();
        if ($userTemp) {
            return response()->json([
                'success' => false,
                'message' => 'You have a pending manifest for BL# ' . $userTemp->MainBL . '. Complete or clear it first.',
            ], 409);
        }

        // Total weight of all containers
        $totalWeight = $containers->sum('Weight');

        // Staged weight so far
        $stagedWeight = ManifestTemp::where('Username', Auth::user()->ID)
            ->where('MainBL', $bl)
            ->sum('Weight');

        // Get staged items for this user and BL
        $stagedItems = ManifestTemp::where('Username', Auth::user()->ID)
            ->where('MainBL', $bl)
            ->get()
            ->load('consignee', 'notifyParty');

        return response()->json([
            'success'      => true,
            'consignment'  => $consignment,
            'containers'   => $containers,
            'totalWeight'  => round($totalWeight, 3),
            'stagedWeight' => round($stagedWeight, 3),
            'remaining'    => round($totalWeight - $stagedWeight, 3),
            'items'        => $stagedItems,
        ]);
    }

    // Generate next House BL number
    public function generateHouseBL(Request $request)
    {
        $request->validate([
            'MainBL' => ['required', 'string'],
        ]);

        $bl       = strtoupper(trim($request->MainBL));
        $prefix   = config('services.manifest.hbl_prefix', 'PSIL');
        $last4    = substr($bl, -4);

        // Count existing house BLs in both temp and manifestation_breakdown
        $countTemp = ManifestTemp::where('MainBL', $bl)->count();
        $countMain = ManifestBreakdown::where('MainBL', $bl)->count();
        $increment = $countTemp + $countMain + 1;

        $houseBL = $prefix . $last4 . $increment;

        // Make sure it doesn't already exist
        while (
            ManifestTemp::where('HouseBL', $houseBL)->exists() ||
            ManifestBreakdown::where('HouseBL', $houseBL)->exists()
        ) {
            $increment++;
            $houseBL = $prefix . $last4 . $increment;
        }

        return response()->json(['success' => true, 'HouseBL' => $houseBL]);
    }

    // Add entry to temp table
    public function addEntry(Request $request)
    {
        $request->validate([
            'ConsignmentID' => ['required', 'integer'],
            'MainBL'        => ['required', 'string', 'max:30'],
            'ContainerNo'   => ['required', 'string', 'max:50'],
            'HouseBL'       => ['required', 'string', 'max:30'],
            'CosigneeID'    => ['required', 'integer', 'exists:consignee_main,ConsigneeID'],
            'Cosignee2_ID'  => ['required', 'integer', 'exists:consignee_main,ConsigneeID'],
            'Description'   => ['required', 'string'],
            'ItemType'      => ['required', 'in:GOODS,VEHICLE,MOTORBIKE'],
            'VIN'           => ['nullable', 'string'],
            'OtherInfo'     => ['nullable', 'string'],
            'Weight'        => ['required', 'numeric', 'min:0.001'],
            'Package'       => ['required', 'integer', 'min:1'],
            'Unit'          => ['required', 'in:LOT,PLT,PKG,UNIT'],
        ]);

        $user = Auth::user();
        $bl   = strtoupper(trim($request->MainBL));

        // VIN validation
        if ($request->ItemType === 'GOODS' && $request->VIN) {
            return response()->json(['success' => false, 'message' => 'VIN is only for VEHICLE item type.'], 422);
        }
        if ($request->ItemType === 'VEHICLE' && !$request->VIN) {
            return response()->json(['success' => false, 'message' => 'VIN is required for VEHICLE item type.'], 422);
        }

        // Package > 1 requires OtherInfo (except MOTORBIKE)
        if ($request->Package > 1 && !$request->OtherInfo && $request->ItemType !== 'MOTORBIKE') {
            return response()->json(['success' => false, 'message' => 'Package is more than 1. Other Information is required.'], 422);
        }

        // Check House BL not already used
        if (ManifestTemp::where('HouseBL', $request->HouseBL)->exists()) {
            return response()->json(['success' => false, 'message' => 'House BL# ' . $request->HouseBL . ' is already in use.'], 409);
        }
        if (ManifestBreakdown::where('HouseBL', $request->HouseBL)->exists()) {
            return response()->json(['success' => false, 'message' => 'House BL# ' . $request->HouseBL . ' has already been registered.'], 409);
        }

        // Get total container weight for this consignment
        $totalWeight = DB::table('container_details')
            ->where('ConsignmentID', $request->ConsignmentID)
            ->where('Status', 1)
            ->sum('Weight');

        // Get current staged weight
        $stagedWeight = ManifestTemp::where('Username', $user->ID)
            ->where('MainBL', $bl)
            ->sum('Weight');

        $remaining = round($totalWeight - $stagedWeight, 3);

        if ($request->Weight > $remaining) {
            return response()->json([
                'success' => false,
                'message' => 'Weight exceeds remaining capacity. Maximum allowed: ' . number_format($remaining, 3) . ' KG',
            ], 422);
        }

        ManifestTemp::create([
            'ConsignmentID' => $request->ConsignmentID,
            'MainBL'        => $bl,
            'ContainerNo'   => strtoupper(trim($request->ContainerNo)),
            'HouseBL'       => strtoupper(trim($request->HouseBL)),
            'CosigneeID'    => $request->CosigneeID,
            'Cosignee2_ID'  => $request->Cosignee2_ID,
            'Description'   => trim($request->Description),
            'ItemType'      => $request->ItemType,
            'VIN'           => trim($request->VIN ?? ''),
            'OtherInfo'     => trim($request->OtherInfo ?? ''),
            'Weight'        => $request->Weight,
            'Package'       => $request->Package,
            'Unit'          => $request->Unit,
            'Username'      => $user->ID,
            'Time'          => now()->toDateTimeString(),
        ]);

        // Return updated staged items
        $items        = ManifestTemp::where('Username', $user->ID)->where('MainBL', $bl)->get();
        $newStaged    = $items->sum('Weight');
        $newRemaining = round($totalWeight - $newStaged, 3);

        return response()->json([
            'success'   => true,
            'message'   => 'Entry added to staging.',
            'items'     => $items->load('consignee', 'notifyParty'),
            'staged'    => round($newStaged, 3),
            'remaining' => $newRemaining,
            'total'     => round($totalWeight, 3),
        ]);
    }

    // Remove entry from temp table
    public function removeEntry(Request $request)
    {
        $request->validate([
            'HouseBL' => ['required', 'string'],
        ]);

        $user = Auth::user();

        $entry = ManifestTemp::where('Username', $user->ID)
            ->where('HouseBL', $request->HouseBL)
            ->first();

        if (!$entry) {
            return response()->json(['success' => false, 'message' => 'Entry not found.'], 404);
        }

        $bl = $entry->MainBL;
        //use query delete instead of model instance delete — no primary key on this table
        ManifestTemp::where('Username', $user->ID)
            ->where('HouseBL', $request->HouseBL)
            ->delete();

        // Get total weight
        $totalWeight  = DB::table('container_details')
            ->where('ConsignmentID', ManifestTemp::where('Username', $user->ID)->where('MainBL', $bl)->value('ConsignmentID') ?? 0)
            ->where('Status', 1)
            ->sum('Weight');

        $items     = ManifestTemp::where('Username', $user->ID)->where('MainBL', $bl)->get();
        $staged    = $items->sum('Weight');
        $remaining = round($totalWeight - $staged, 3);

        return response()->json([
            'success'   => true,
            'items'     => $items->load('consignee', 'notifyParty'),
            'staged'    => round($staged, 3),
            'remaining' => $remaining,
            'total'     => round($totalWeight, 3),
        ]);
    }

    // Clear all temp entries
    public function clearEntries()
    {
        ManifestTemp::where('Username', Auth::user()->ID)->delete();
        return response()->json(['success' => true, 'message' => 'Staging cleared.']);
    }

    // Save manifest to manifestation_breakdown
    public function store(Request $request)
    {
        $request->validate([
            'MainBL' => ['required', 'string'],
        ]);

        $user = Auth::user();
        $bl   = strtoupper(trim($request->MainBL));

        $items = ManifestTemp::where('Username', $user->ID)
            ->where('MainBL', $bl)
            ->get();

        if ($items->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No staged entries found.'], 422);
        }

        // Get ConsignmentID
        $consignmentID = $items->first()->ConsignmentID;

        // Get total container weight
        $totalWeight = DB::table('container_details')
            ->where('ConsignmentID', $consignmentID)
            ->where('Status', 1)
            ->sum('Weight');

        // Get total staged weight
        $stagedWeight = round($items->sum('Weight'), 3);

        // Weights must match exactly
        if (round($totalWeight, 3) !== $stagedWeight) {
            return response()->json([
                'success' => false,
                'message' => 'Total staged weight (' . $stagedWeight . ' KG) must equal total container weight (' . round($totalWeight, 3) . ' KG).',
            ], 422);
        }

        // Check all items are under same BL
        $distinctBLs = $items->pluck('MainBL')->unique()->count();
        if ($distinctBLs > 1) {
            return response()->json(['success' => false, 'message' => 'Multiple BLs detected in staging. Please reset.'], 422);
        }

        DB::beginTransaction();

        try {
            foreach ($items as $item) {
                ManifestBreakdown::create([
                    'ConsignmentID'  => $item->ConsignmentID,
                    'MainBL'         => $item->MainBL,
                    'ContainerNo'    => $item->ContainerNo,
                    'HouseBL'        => $item->HouseBL,
                    'ConsigneeID'    => $item->CosigneeID,
                    'Consigenee2_ID' => $item->Cosignee2_ID,
                    'Description'    => $item->Description,
                    'ItemType'       => $item->ItemType,
                    'VIN'            => $item->VIN,
                    'OtherInfo'      => $item->OtherInfo,
                    'Weight'         => $item->Weight,
                    'Package'        => $item->Package,
                    'Unit'           => $item->Unit,
                    'Username'       => $user->ID,
                    'Date'           => now()->toDateString(),
                    'Time'           => now()->toDateTimeString(),
                    'Status'         => 1,
                ]);
            }

            // Insert into eta_web_track
            DB::table('eta_web_track')->insert([
                'ConsignmentID' => $consignmentID,
                'MainBL'        => $bl,
                'ETA'           => DB::table('container_main')->where('BL', $bl)->value('ETA'),
                'ArrivalStatus' => 'ON THE WAY',
                'Status'        => '1',
                'Username'      => $user->ID,
                'Time'          => now()->toDateTimeString(),
            ]);

            // Clear temp table
            ManifestTemp::where('Username', $user->ID)->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Manifest saved successfully for BL# ' . $bl,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to save manifest. Please try again.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // Search consignees for dropdown
    public function searchConsignee(Request $request)
    {
        $consignees = Consignee::active()
            ->where('FullName', 'like', '%' . $request->q . '%')
            ->orderBy('FullName')
            ->limit(10)
            ->get(['ConsigneeID', 'FullName', 'TelNo']);

        return response()->json($consignees);
    }

    public function report(string $bl)
    {
        $bl = strtoupper($bl);

        // Get consignment details
        $consignment = DB::table('container_main as cm')
            ->join('shipper_main as s', 'cm.ShipperID', '=', 's.ShipperID')
            ->join('pol', 'cm.POL_ID', '=', 'pol.POL_ID')
            ->join('pod', 'cm.POD_ID', '=', 'pod.POD_ID')
            ->where('cm.BL', $bl)
            ->select(
                'cm.ConsignmentID',
                'cm.BL as MainBL',
                'cm.VesselName',
                'cm.ETA',
                'cm.ContWeight',
                's.ShipperName',
                'pol.POL_Name',
                'pod.POD_Name',
            )
            ->first();

        if (!$consignment) {
            abort(404, 'Consignment not found for BL# ' . $bl);
        }

        // Get container details
        $containers = DB::table('container_details')
            ->where('ConsignmentID', $consignment->ConsignmentID)
            ->where('Status', 1)
            ->get(['ContainerNo', 'ContainerSize', 'Weight']);

        // Get manifest breakdown entries
        $entries = ManifestBreakdown::with(['consignee', 'notifyParty'])
            ->where('MainBL', $bl)
            ->where('Status', 1)
            ->get();

        if ($entries->isEmpty()) {
            abort(404, 'No manifest entries found for BL# ' . $bl);
        }

        return view('consignments.manifest-report', compact(
            'consignment',
            'containers',
            'entries'
        ));
    }
}
