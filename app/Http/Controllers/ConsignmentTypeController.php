<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ConsignmentTypeController extends Controller
{
    /** Sets IsLCL on a consignment nobody has typed yet. */
    public function confirm(Request $request)
    {
        $data = $request->validate([
            'ConsignmentID' => ['required', 'integer'],
            'BL'            => ['required', 'string', 'max:100'],
            'Type'          => ['required', 'in:LCL,FCL'],
        ]);

        $bl = strtoupper(trim($data['BL']));

        $consignment = DB::table('container_main')
            ->where('ConsignmentID', $data['ConsignmentID'])
            ->where('BL', $bl)
            ->first(['IsLCL']);

        if (! $consignment) {
            return response()->json([
                'success' => false,
                'message' => "BL# {$bl} not found.",
            ], 404);
        }

        if ($consignment->IsLCL !== null) {
            return response()->json([
                'success' => false,
                'message' => 'This consignment has already been typed.',
            ], 409);
        }

        // Breakdown rows are proof of LCL — they cannot be contradicted.
        $hasBreakdown = DB::table('manifestation_breakdown')
            ->where('ConsignmentID', $data['ConsignmentID'])
            ->where('MainBL', $bl)
            ->where('Status', 1)
            ->exists();

        if ($hasBreakdown && $data['Type'] === 'FCL') {
            return response()->json([
                'success' => false,
                'message' => "BL# {$bl} already has manifest breakdown rows, so it is LCL.",
            ], 422);
        }

        DB::table('container_main')
            ->where('ConsignmentID', $data['ConsignmentID'])
            ->where('BL', $bl)
            ->update(['IsLCL' => $data['Type'] === 'LCL' ? 1 : 0]);

        Cache::forget('stall_counts_' . Auth::user()->BranchID);

        return response()->json([
            'success'   => true,
            'Type'      => $data['Type'],
            'BL'        => $bl,
            'message'   => "BL# {$bl} confirmed as {$data['Type']}.",
            'NextStage' => $data['Type'] === 'LCL' ? 'manifest' : 'disbursement',
        ]);
    }
}
