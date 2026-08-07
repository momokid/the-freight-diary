<?php

namespace App\Http\Controllers\EditData;

use App\Http\Controllers\Controller;
use App\Models\Carrier;
use App\Models\Consignee;
use App\Models\ContainerDetails;
use App\Models\ContainerMain;
use App\Models\Pod;
use App\Models\Pol;
use App\Models\Shipper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EditConsignmentController extends Controller
{
    // ── Page Load ──────────────────────────────────────────────────────────────

    public function index()
    {
        $carriers = Carrier::active()->orderBy('CarrierName')->get();
        $shippers = Shipper::active()->orderBy('ShipperName')->get();
        $pols = Pol::orderBy('POL_Name')->get();
        $pods = Pod::orderBy('POD_Name')->get();
        $consignees = Consignee::active()->orderBy('FullName')->get();

        return view('edit-data.consignment', compact(
            'carriers',
            'shippers',
            'pols',
            'pods',
            'consignees',
        ));
    }

    // ── Consignment Search & Load ───────────────────────────────────────────────

    // Typeahead search — returns matching BLs
    public function searchBL(Request $request)
    {
        $q = strtoupper(trim($request->q ?? ''));

        if (strlen($q) < 1) {
            return response()->json([]);
        }

        $results = DB::table('container_main as cm')
            ->join('shipper_main as s', 'cm.ShipperID', '=', 's.ShipperID')
            ->where('cm.Status', '<>', 9)
            ->where('cm.BL', 'like', "%{$q}%")
            ->select('cm.ConsignmentID', 'cm.BL', 'cm.VesselName', 's.ShipperName')
            ->orderByDesc('cm.ConsignmentID')
            ->limit(8)
            ->get();

        return response()->json($results);
    }

    // Load full consignment data by BL
    public function loadBL(Request $request)
    {
        $request->validate([
            'BL' => ['required', 'string', 'max:50'],
        ]);

        $bl = strtoupper(trim($request->BL));

        $consignment = ContainerMain::where('BL', $bl)
            ->where('Status', '<>', 9)
            ->first();

        if (! $consignment) {
            return response()->json([
                'success' => false,
                'message' => 'Consignment not found for BL# ' . $bl,
            ], 404);
        }

        // Load containers from container_details
        $containers = ContainerDetails::where('ConsignmentID', $consignment->ConsignmentID)
            ->where('Status', 1)
            ->get(['ConsignmentID', 'BL', 'ContainerNo', 'SealNo', 'ContainerSize', 'Weight', 'HandlingCost']);

        return response()->json([
            'success' => true,
            'consignment' => $consignment,
            'containers' => $containers,
            'isLCL' => $consignment->isLCL(),
        ]);
    }

    // ── Consignment Update ──────────────────────────────────────────────────────

    public function updateBL(Request $request)
    {
        $request->validate([
            'ConsignmentID' => ['required', 'integer', 'exists:container_main,ConsignmentID'],
            'ETA' => ['required', 'date'],
            'CarrierID' => ['required', 'integer', 'exists:ship_carrier,CarrierID'],
            'ShipperID' => ['required', 'integer', 'exists:shipper_main,ShipperID'],
            'VesselName' => ['required', 'string', 'max:80'],
            'VoyageNo' => ['required', 'string', 'max:80'],
            'SealNo' => ['nullable', 'string', 'max:50'],
            'BL' => ['required', 'string', 'max:50'],
            'ContainerNo' => ['nullable', 'string', 'max:30'],
            'ContainerSize' => ['nullable', 'string', 'max:15'],
            'POIS' => ['nullable', 'string', 'max:80'],
            'DOIS' => ['nullable', 'date'],
            'SOB' => ['nullable', 'date'],
            'POL_ID' => ['required', 'integer', 'exists:pol,POL_ID'],
            'POD_ID' => ['required', 'integer', 'exists:pod,POD_ID'],
            'Rotation' => ['nullable', 'string', 'max:30'],
            'AgentContact' => ['nullable', 'string', 'max:20'],
            'Destination' => ['nullable', 'string'],
            'ContWeight' => ['nullable', 'numeric', 'min:0'],
            'Charges' => ['nullable', 'numeric', 'min:0'],
        ]);

        $consignment = ContainerMain::findOrFail($request->ConsignmentID);

        // BL uniqueness — exclude current record
        $blTaken = DB::table('container_main')
            ->where('BL', strtoupper(trim($request->BL)))
            ->where('ConsignmentID', '!=', $request->ConsignmentID)
            ->exists();

        if ($blTaken) {
            return response()->json([
                'success' => false,
                'message' => 'Bill of Lading# ' . $request->BL . ' is already used by another consignment.',
            ], 409);
        }

        $oldEta = $consignment->ETA;

        $consignment->update([
            'ETA' => $request->ETA,
            'CarrierID' => $request->CarrierID,
            'ShipperID' => $request->ShipperID,
            'VesselName' => strtoupper(trim($request->VesselName)),
            'VoyageNo' => strtoupper(trim($request->VoyageNo)),
            'SealNo' => strtoupper(trim($request->SealNo ?? '')),
            'BL' => strtoupper(trim($request->BL)),
            'ContainerNo' => strtoupper(trim($request->ContainerNo ?? '')),
            'ContainerSize' => strtoupper(trim($request->ContainerSize ?? '')),
            'POIS' => trim($request->POIS ?? ''),
            'DOIS' => $request->DOIS ?? '1970-01-01',
            'SOB' => $request->SOB ?? '1970-01-01',
            'POL_ID' => $request->POL_ID,
            'POD_ID' => $request->POD_ID,
            'Rotation' => strtoupper(trim($request->Rotation ?? '')),
            'AgentContact' => trim($request->AgentContact ?? ''),
            'Destination' => trim($request->Destination ?? ''),
            'ContWeight' => $request->ContWeight ?? 0,
            'Charges' => $request->Charges ?? 0,
        ]);

        $etaChanged = $oldEta !== $request->ETA;
        $phone      = null;
        $consignee  = null;

        if ($etaChanged) {
            $cee = DB::table('consignee_main')
                ->where('ConsigneeID', $consignment->ConsigneeID)
                ->select('TelNo', 'FullName')
                ->first();
            $phone     = $cee->TelNo ?? '';
            $consignee = $cee->FullName ?? '';
        }

        return response()->json([
            'success'     => true,
            'message'     => 'Consignment updated successfully.',
            'eta_changed' => $etaChanged,
            'bl'          => strtoupper(trim($request->BL)),
            'eta'         => $request->ETA,
            'phone'       => $phone,
            'consignee'   => $consignee,
        ]);
    }

    // Update a single container_details row — identified by ConsignmentID + ContainerNo
    public function updateContainer(Request $request)
    {
        $request->validate([
            'ConsignmentID' => ['required', 'integer'],
            'OrigContainerNo' => ['required', 'string', 'max:50'], // original ContainerNo to locate the row
            'ContainerNo' => ['required', 'string', 'max:50'],
            'SealNo' => ['nullable', 'string', 'max:50'],
            'ContainerSize' => ['nullable', 'string', 'max:15'],
            'Weight' => ['nullable', 'numeric', 'min:0'],
            'HandlingCost' => ['nullable', 'numeric', 'min:0'],
        ]);

        $updated = DB::table('container_details')
            ->where('ConsignmentID', $request->ConsignmentID)
            ->where('ContainerNo', strtoupper(trim($request->OrigContainerNo)))
            ->update([
                'ContainerNo' => strtoupper(trim($request->ContainerNo)),
                'SealNo' => strtoupper(trim($request->SealNo ?? '')),
                'ContainerSize' => strtoupper(trim($request->ContainerSize ?? '')),
                'Weight' => $request->Weight ?? 0,
                'HandlingCost' => $request->HandlingCost ?? 0,
            ]);

        if (! $updated) {
            return response()->json([
                'success' => false,
                'message' => 'Container not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Container updated successfully.',
        ]);
    }

    // ── HBL Search & Load ───────────────────────────────────────────────────────

    // Typeahead search — returns matching HBLs
    // Display format: {ConsigneeName} [{HouseBL}] {ItemType}
    public function searchHBL(Request $request)
    {
        $q = strtoupper(trim($request->q ?? ''));

        if (strlen($q) < 1) {
            return response()->json([]);
        }

        $results = DB::table('manifestation_breakdown as mb')
            ->join('consignee_main as c', 'mb.ConsigneeID', '=', 'c.ConsigneeID')
            ->where(function ($query) use ($q) {
                $query->where('mb.HouseBL', 'like', "%{$q}%")
                    ->orWhere('c.FullName', 'like', "%{$q}%");
            })
            ->where('mb.Status', 1)
            ->select(
                'mb.ConsignmentID',
                'mb.MainBL',
                'mb.HouseBL',
                'mb.ItemType',
                'c.FullName as ConsigneeName',
            )
            ->orderByDesc('mb.ConsignmentID')
            ->limit(8)
            ->get([
                'mb.ConsignmentID',
                'mb.MainBL',
                'mb.HouseBL',
                'c.FullName as ConsigneeName',
                'mb.ItemType',
                DB::raw("CONCAT(c.FullName, ' [', mb.HouseBL, '] ', mb.ItemType) as label"),
            ])
            // Build display label for the dropdown
            ->map(function ($row) {
                $row->label = $row->ConsigneeName . ' [' . $row->HouseBL . '] ' . $row->ItemType;

                return $row;
            });

        return response()->json($results);
    }

    // Load full HBL data by HouseBL
    public function loadHBL(Request $request)
    {
        $request->validate([
            'HouseBL' => ['required', 'string', 'max:50'],
        ]);

        $hbl = strtoupper(trim($request->HouseBL));

        $entry = DB::table('manifestation_breakdown as mb')
            ->join('consignee_main as c', 'mb.ConsigneeID', '=', 'c.ConsigneeID')
            ->leftJoin('consignee_main as c2', 'mb.Consigenee2_ID', '=', 'c2.ConsigneeID')
            ->where('mb.HouseBL', $hbl)
            ->where('mb.Status', 1)
            ->select(
                'mb.ConsignmentID',
                'mb.MainBL',
                'mb.HouseBL',
                'mb.ConsigneeID',
                'c.FullName as ConsigneeName',
                'mb.Consigenee2_ID',
                'c2.FullName as NotifyPartyName',
                'mb.Description',
                'mb.ItemType',
                'mb.VIN',
                'mb.OtherInfo',
                'mb.Weight',
                'mb.Package',
                'mb.Unit',
            )
            ->first();

        if (! $entry) {
            return response()->json([
                'success' => false,
                'message' => 'House BL not found: ' . $hbl,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'entry' => $entry,
        ]);
    }

    // ── HBL Update ──────────────────────────────────────────────────────────────

    public function updateHBL(Request $request)
    {
        $request->validate([
            'HouseBL' => ['required', 'string', 'max:50'],
            'ConsignmentID' => ['required', 'integer'],
            'ConsigneeID' => ['required', 'integer', 'exists:consignee_main,ConsigneeID'],
            'Consigenee2_ID' => ['nullable', 'integer', 'exists:consignee_main,ConsigneeID'],
            'Package' => ['required', 'integer', 'min:0'],
            'Unit' => ['required', 'string', 'max:7'],
            'ItemType' => ['required', 'string', 'max:15'],
            'Description' => ['required', 'string'],
            'VIN' => ['nullable', 'string'],
            'OtherInfo' => ['nullable', 'string'],
        ]);

        // CHANGED: use DB::table directly — ManifestBreakdown has no primary key so Eloquent update() is unreliable
        $updated = DB::table('manifestation_breakdown')
            ->where('ConsignmentID', $request->ConsignmentID)
            ->where('HouseBL', $request->HouseBL)
            ->where('Status', 1)
            ->update([
                'ConsigneeID' => $request->ConsigneeID,
                'Consigenee2_ID' => $request->Consigenee2_ID ?? 0,
                'Package' => $request->Package,
                'Unit' => strtoupper(trim($request->Unit)),
                'ItemType' => strtoupper(trim($request->ItemType)),
                'Description' => strtoupper(trim($request->Description)),
                'VIN' => strtoupper(trim($request->VIN ?? '')),
                'OtherInfo' => strtoupper(trim($request->OtherInfo ?? '')),
            ]);

        if (! $updated) {
            return response()->json([
                'success' => false,
                'message' => 'House BL record not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'House BL updated successfully.',
        ]);
    }
}
