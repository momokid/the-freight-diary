<?php

namespace App\Http\Controllers\EditData;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EditWeightController extends Controller
{
    public function index()
    {
        return view('edit-data.weight');
    }

    // Typeahead — only BLs that have manifestation_breakdown entries
    public function searchBL(Request $request)
    {
        $q = strtoupper(trim($request->q ?? ''));

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $results = DB::table('container_main as cm')
            ->join('manifestation_breakdown as mb', 'cm.ConsignmentID', '=', 'mb.ConsignmentID')
            ->where('cm.BL', 'like', "%{$q}%")
            ->where('mb.Status', 1)
            ->groupBy('cm.ConsignmentID', 'cm.BL', 'cm.ContainerNo')
            ->orderByDesc('cm.ConsignmentID')
            ->limit(10)
            ->get([
                'cm.ConsignmentID',
                'cm.BL',
                'cm.ContainerNo',
                DB::raw("CONCAT(cm.BL, ' [', COALESCE(cm.ContainerNo, ''), ']') as label"),
            ]);

        return response()->json($results);
    }

    // Load consignment header + all HBL weight rows
    public function loadBL(Request $request)
    {
        $request->validate(['BL' => ['required', 'string', 'max:50']]);

        $bl = strtoupper(trim($request->BL));

        $consignment = DB::table('container_main')
            ->where('BL', $bl)
            ->first(['ConsignmentID', 'BL', 'Date']);

        if (! $consignment) {
            return response()->json([
                'success' => false,
                'message' => 'Consignment not found for BL# '.$bl,
            ], 404);
        }

        $containers = DB::table('container_details')
            ->where('ConsignmentID', $consignment->ConsignmentID)
            ->where('Status', 1)
            ->get(['ContainerNo', 'ContainerSize', 'Weight']);

        $grossWeight = DB::table('manifestation_breakdown')
            ->where('ConsignmentID', $consignment->ConsignmentID)
            ->where('Status', 1)
            ->sum('Weight');

        $rows = DB::table('manifestation_breakdown as mb')
            ->join('consignee_main as c', 'mb.ConsigneeID', '=', 'c.ConsigneeID')
            ->where('mb.ConsignmentID', $consignment->ConsignmentID)
            ->where('mb.Status', 1)
            ->orderBy('mb.HouseBL')
            ->get([
                'mb.HouseBL',
                'c.FullName as ConsigneeName',
                'mb.Description',
                'mb.Package',
                'mb.Unit',
                'mb.Weight',
            ]);

        if ($rows->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No manifest breakdown entries found for BL# '.$bl,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'consignment' => $consignment,
            'containers' => $containers,
            'grossWeight' => round($grossWeight, 3),
            'rows' => $rows,
        ]);
    }

    // Save all HBL weights + update container_main.ContWeight
    public function update(Request $request)
    {
        $request->validate([
            'ConsignmentID' => ['required', 'integer', 'exists:container_main,ConsignmentID'],
            'weights' => ['required', 'array', 'min:1'],
            'weights.*.HouseBL' => ['required', 'string', 'max:30'],
            'weights.*.Weight' => ['required', 'numeric', 'min:0'],
        ]);

        DB::beginTransaction();

        try {
            foreach ($request->weights as $entry) {
                DB::table('manifestation_breakdown')
                    ->where('ConsignmentID', $request->ConsignmentID)
                    ->where('HouseBL', $entry['HouseBL'])
                    ->where('Status', 1)
                    ->update(['Weight' => $entry['Weight']]);
            }

            // Recalculate and update ContWeight
            $total = DB::table('manifestation_breakdown')
                ->where('ConsignmentID', $request->ConsignmentID)
                ->where('Status', 1)
                ->sum('Weight');

            DB::table('container_main')
                ->where('ConsignmentID', $request->ConsignmentID)
                ->update(['ContWeight' => round($total, 3)]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Weights updated successfully.',
                'ContWeight' => round($total, 3),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to update weights. Please try again.',
                'debug' => app()->environment('local') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
