<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\DisbursementAccount;
use App\Models\LedgerAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DisbursementAccountController extends Controller
{
    public function index()
    {
        // Load existing disbursement accounts ordered by account name
        $disbursements = DisbursementAccount::with('account')
            ->get()
            ->sortBy('account.AccountName');

        // All active ledger accounts not already in disbursement_accounts
        $existingAccountNos = $disbursements->pluck('AccountNo')->toArray();

        $accounts = LedgerAccount::active()
            ->where('Type', 'EXPENDITURE')
            ->whereNotIn('AccountNo', $existingAccountNos)
            ->orderBy('AccountName')
            ->get();

        return view('settings.disbursement-account', compact('disbursements', 'accounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'AccountNo' => ['required', 'integer', 'exists:ledger_account,AccountNo', 'unique:disbursement_accounts,AccountNo'],
        ]);

        DisbursementAccount::create([
            'AccountNo' => $request->AccountNo,
            'Username'  => Auth::user()->ID,
            'Date'      => now()->toDateTimeString(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Disbursement account added successfully.',
        ]);
    }

    // Hard delete
    public function destroy(int $id)
    {
        $disbursement = DisbursementAccount::findOrFail($id);
        $disbursement->delete();

        return response()->json([
            'success' => true,
            'message' => 'Disbursement account removed successfully.',
        ]);
    }
}
