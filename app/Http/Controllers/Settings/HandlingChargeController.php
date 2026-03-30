<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\HandlingCharge;
use App\Models\LedgerAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HandlingChargeController extends Controller
{
    public function index()
    {
        // Load existing handling charges ordered by priority
        $charges = HandlingCharge::with('account')
            ->orderBy('POrder')
            ->get();

        // Only BL (Billing) accounts that are not already in handling_charge
        $existingAccountNos = $charges->pluck('AccountNo')->toArray();

        $accounts = LedgerAccount::active()
            ->where('Nature', 'BL')
            ->whereNotIn('AccountNo', $existingAccountNos)
            ->orderBy('AccountName')
            ->get();

        return view('settings.handling-charge', compact('charges', 'accounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'AccountNo' => ['required', 'integer', 'exists:ledger_account,AccountNo', 'unique:handling_charge,AccountNo'],
            'Amount'    => ['required', 'numeric', 'min:0.01'],
            'POrder'    => ['required', 'integer', 'min:1'],
        ]);

        HandlingCharge::create([
            'AccountNo' => $request->AccountNo,
            'Amount'    => $request->Amount,
            'POrder'    => $request->POrder,
            'Username'  => Auth::user()->ID,
            'Time'      => now()->toDateTimeString(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Handling charge added successfully.',
        ]);
    }

    // Inline edit amount
    public function update(Request $request, int $id)
    {
        $request->validate([
            'Amount' => ['required', 'numeric', 'min:0.01'],
        ]);

        $charge = HandlingCharge::findOrFail($id);
        $charge->Amount = $request->Amount;
        $charge->save();

        return response()->json([
            'success' => true,
            'message' => 'Amount updated successfully.',
            'Amount'  => number_format($charge->Amount, 2),
        ]);
    }

    // Hard delete
    public function destroy(int $id)
    {
        $charge = HandlingCharge::findOrFail($id);
        $charge->delete();

        return response()->json([
            'success' => true,
            'message' => 'Handling charge removed successfully.',
        ]);
    }
}