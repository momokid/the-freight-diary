<?php

namespace App\Http\Controllers;

use App\Models\Carrier;
use App\Models\CmdtsTemp;
use App\Models\CommodityCategory;
use App\Models\Consignee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\ClientNotificationService;


class CmdtsController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Check if user has pending containers in temp table
        $pendingContainers = CmdtsTemp::where('Username', $user->ID)->get();
        $pendingBOL        = $pendingContainers->first()?->BL;

        // Load dropdowns
        $categories   = CommodityCategory::with('types')->orderBy('CategoryName')->get();
        $carriers     = Carrier::active()->orderBy('CarrierName')->get();
        $consignees   = Consignee::active()->orderBy('FullName')->get();
        $releaseTypes = DB::table('container_release')->orderBy('ID')->get();

        return view('consignments.cmdts', compact(
            'pendingContainers',
            'pendingBOL',
            'categories',
            'carriers',
            'consignees',
            'releaseTypes'
        ));
    }

    // Add container to staging
    public function addContainer(Request $request)
    {
        $request->validate([
            'BL'          => ['required', 'string', 'max:50'],
            'ContainerNo' => ['required', 'string', 'max:20'],
            'SealNo'      => ['nullable', 'string', 'max:50'],
            'Size'        => ['required', 'string', 'max:15'],
            'ItemDetails' => ['required', 'string'],
        ]);

        $user = Auth::user();
        $bl   = strtoupper(trim($request->BL));

        // Check if user has containers under a different BL
        $existingBL = CmdtsTemp::where('Username', $user->ID)->value('BL');
        if ($existingBL && $existingBL !== $bl) {
            return response()->json([
                'success' => false,
                'message' => "You have pending containers under BL# {$existingBL}. Submit or clear them first.",
            ], 409);
        }

        // Check duplicate container under same BL
        $exists = CmdtsTemp::where('Username', $user->ID)
            ->where('BL', $bl)
            ->where('ContainerNo', strtoupper(trim($request->ContainerNo)))
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'This container number has already been added.',
            ], 409);
        }

        CmdtsTemp::create([
            'BL'          => $bl,
            'ContainerNo' => strtoupper(trim($request->ContainerNo)),
            'SealNo'      => strtoupper(trim($request->SealNo ?? '')),
            'Size'        => trim($request->Size),
            'ItemDetails' => trim($request->ItemDetails),
            'Username'    => $user->ID,
        ]);

        $containers = CmdtsTemp::where('Username', $user->ID)->get();

        return response()->json([
            'success'    => true,
            'message'    => 'Container added to staging.',
            'containers' => $containers,
            'total'      => $containers->count(),
        ]);
    }

    // Remove container from staging
    public function removeContainer(Request $request)
    {
        $request->validate([
            'ContainerNo' => ['required', 'string'],
        ]);

        CmdtsTemp::where('Username', Auth::user()->ID)
            ->where('ContainerNo', $request->ContainerNo)
            ->delete();

        $containers = CmdtsTemp::where('Username', Auth::user()->ID)->get();

        return response()->json([
            'success'    => true,
            'containers' => $containers,
            'total'      => $containers->count(),
        ]);
    }

    // Clear all staged containers
    public function clearContainers()
    {
        CmdtsTemp::where('Username', Auth::user()->ID)->delete();

        return response()->json(['success' => true, 'message' => 'Staging cleared.']);
    }

    public function store(Request $request, ClientNotificationService $notification)
    {
        $request->validate([
            'CmdtCategoryID' => ['required', 'integer', 'exists:commodity_category,ID'],
            'CmdtTypeID'     => ['required', 'integer', 'exists:commodity_type,TypeID'],
            'BL'             => ['required', 'string', 'max:50', 'unique:container_main,BL'],
            'ConsigneeID'    => ['required', 'integer', 'exists:consignee_main,ConsigneeID'],
            'ETA'            => ['required', 'date'],
            'CarrierID'      => ['required', 'integer', 'exists:ship_carrier,CarrierID'],
            'ReleaseType'    => ['required', 'integer', 'exists:container_release,ID'],
            'Destination'    => ['nullable', 'string'],
        ]);

        $user       = Auth::user();
        $bl         = strtoupper(trim($request->BL));
        $clientCode = $notification->generateClientCode();

        $containers = CmdtsTemp::where('Username', $user->ID)
            ->where('BL', $bl)
            ->get();

        if ($containers->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Please add at least 1 container before saving.',
            ], 422);
        }

        $consignmentID  = (DB::table('container_main')->max('ConsignmentID') ?? 0) + 1;
        $firstContainer = $containers->first();

        DB::beginTransaction();

        try {
            DB::table('container_main')->insert([
                'ConsignmentID' => $consignmentID,
                'CarrierID'     => $request->CarrierID,
                'Rotation'      => '',
                'ShipperID'     => 0,
                'VesselName'    => '',
                'VoyageNo'      => '',
                'SealNo'        => $firstContainer->SealNo ?? '',
                'ETA'           => $request->ETA,
                'BL'            => $bl,
                'ContainerNo'   => $firstContainer->ContainerNo,
                'ContainerSize' => $firstContainer->Size,
                'ReceiptNo'     => '',
                'POIS'          => '',
                'DOIS'          => '1970-01-01',
                'SOB'           => '1970-01-01',
                'POL_ID'        => 0,
                'POD_ID'        => 0,
                'ContWeight'    => 0,
                'Charges'       => 0,
                'AgentContact'  => '',
                'Destination'   => trim($request->Destination ?? ''),
                'Username'      => $user->ID,
                'BranchID'      => $user->BranchID,
                'Date'          => now()->toDateString(),
                'Time'          => now()->toDateTimeString(),
                'Status'        => 1,
                'CmdtTypeID'    => $request->CmdtTypeID,
                'ConsigneeID'   => $request->ConsigneeID,
                'ReleaseType'   => $request->ReleaseType,
                'Ownership'     => 1,
                'ClientCode'    => $clientCode,
            ]);

            foreach ($containers as $container) {
                DB::table('container_details')->insert([
                    'ConsignmentID' => $consignmentID,
                    'BL'            => $bl,
                    'SealNo'        => $container->SealNo ?? '',
                    'ContainerNo'   => $container->ContainerNo,
                    'ContainerSize' => $container->Size,
                    'Weight'        => 0,
                    'ItemDetails'   => $container->ItemDetails,
                    'HandlingCost'  => 0,
                    'GateOutDate'   => null,
                    'ReturnDate'    => null,
                    'Username'      => $user->ID,
                    'BranchID'      => $user->BranchID,
                    'Date'          => now()->toDateString(),
                    'Time'          => now()->toDateTimeString(),
                    'Status'        => 1,
                ]);
            }

            CmdtsTemp::where('Username', $user->ID)->delete();

            DB::commit();

            return response()->json([
                'success'       => true,
                'message'       => 'Consignment saved successfully.',
                'ConsignmentID' => $consignmentID,
                'BL'            => $bl,
                'ClientCode'    => $clientCode,
                'ConsigneeID'   => $request->ConsigneeID,
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

    // Search consignees for typeahead
    public function searchConsignee(Request $request)
    {
        $consignees = Consignee::active()
            ->where('FullName', 'like', '%' . $request->q . '%')
            ->orderBy('FullName')
            ->limit(10)
            ->get(['ConsigneeID', 'FullName', 'TelNo']);

        return response()->json($consignees);
    }

    // Get commodity types by category — AJAX
    public function typesByCategory(Request $request)
    {
        $request->validate([
            'category_id' => ['required', 'integer'],
        ]);

        $types = DB::table('commodity_type')
            ->where('CategoryID', $request->category_id)
            ->orderBy('TypeName')
            ->get(['TypeID', 'TypeName']);

        return response()->json($types);
    }
}
