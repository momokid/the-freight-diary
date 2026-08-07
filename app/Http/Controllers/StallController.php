<?php

namespace App\Http\Controllers;

use App\Services\StallService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\UserAuth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

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
        $user     = Auth::user();
        $branchId = $user->BranchID;

        $counts = Cache::remember(
            "stall_counts_{$branchId}",
            now()->addMinutes(2),
            fn() => $this->stalls->counts($branchId)
        );

        $userAuth = UserAuth::where('Username', $user->ID)->first();
        $resets   = 0;

        if ($userAuth && $userAuth->hasPermission('UserPrivilege')) {
            $resets = User::where('reset_requested', 1)->count();
        }

        return response()->json($counts + [
            'resets'   => $resets,
            'stallUrl' => route('stalled.index'),
            'resetUrl' => route('settings.user-privilege.index'),
        ]);
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

    public function release(Request $request)
    {
        $data = $request->validate([
            'ConsignmentID' => ['required', 'integer'],
            'BL'            => ['required', 'string', 'max:100'],
            'Stage'         => ['required', 'in:disbursement,gateout,return'],
        ]);

        $user = Auth::user();

        $claim = DB::table('stall_claims')
            ->where('ConsignmentID', $data['ConsignmentID'])
            ->where('BL', $data['BL'])
            ->where('Stage', $data['Stage'])
            ->first();

        if (! $claim) {
            return response()->json(['success' => true]);
        }

        if ($claim->Username !== $user->ID && $user->Nature !== 'Admin-0') {
            return response()->json([
                'success' => false,
                'message' => 'Only ' . $claim->Username . ' can release this.',
            ], 403);
        }

        $this->stalls->release($data['ConsignmentID'], $data['BL'], $data['Stage']);

        Cache::forget("stall_counts_{$user->BranchID}");

        return response()->json(['success' => true]);
    }
}
