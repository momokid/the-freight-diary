<?php

namespace App\Http\Controllers;

use App\Services\StallService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class StallController extends Controller
{
    public function __construct(
        private StallService $stalls
    ) {}

    public function index()
    {
        $branchId = Auth::user()->BranchID;

        $groups = $this->stalls->stalled($branchId);
        $total  = array_sum(array_map('count', $groups));

        return view('stalled.index', compact('groups', 'total'));
    }

    public function counts()
    {
        $branchId = Auth::user()->BranchID;

        $counts = Cache::remember(
            "stall_counts_{$branchId}",
            now()->addMinutes(2),
            fn() => $this->stalls->counts($branchId)
        );

        return response()->json($counts);
    }

    public function claim(Request $request)
    {
        $data = $request->validate([
            'ConsignmentID' => ['required', 'integer'],
            'BL'            => ['required', 'string', 'max:100'],
            'Stage'         => ['required', 'in:disbursement,gateout,return'],
        ]);

        $user = Auth::user();

        $this->stalls->claim(
            $data['ConsignmentID'],
            $data['BL'],
            $data['Stage'],
            $user->ID
        );

        Cache::forget("stall_counts_{$user->BranchID}");

        return response()->json([
            'success'   => true,
            'ClaimedBy' => $user->ID,
        ]);
    }
}
