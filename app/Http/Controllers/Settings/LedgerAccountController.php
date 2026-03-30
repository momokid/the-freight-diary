<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\LedgerAccount;
use App\Models\LedgerControl;
use App\Models\LedgerCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LedgerAccountController extends Controller
{
    public function index(Request $request)
    {
        $accounts = LedgerAccount::active()
            ->with(['control', 'category'])
            ->orderBy('AccountName')
            ->get();

        $inactiveAccounts = LedgerAccount::inactive()
            ->with(['control', 'category'])
            ->orderBy('ControlID')
            ->orderBy('AccountNo')
            ->get();

        $controls   = LedgerControl::active()->orderBy('ControlName')->get();
        $categories = LedgerCategory::active()->orderBy('CategoryID')->orderBy('SubCategoryID')->get();

        return view('settings.ledger-account', compact(
            'accounts',
            'inactiveAccounts',
            'controls',
            'categories'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ControlID'   => ['required', 'integer', 'exists:ledger_control,ControlID'],
            'CategoryID'  => ['required', 'integer', 'exists:ledger_category,SubCategoryID'],
            'AccountName' => ['required', 'string', 'max:150'],
            'Nature'      => ['required', 'in:BL,NB'],
            'Class'       => ['required', 'in:Dr,Cr'],
            'Type'        => ['required', 'in:GL,INCOME,EXPENDITURE'],
        ]);

        // Check for duplicate account name under same control
        $exists = LedgerAccount::where('ControlID', $request->ControlID)
            ->where('AccountName', ucwords(strtolower(trim($request->AccountName))))
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'An account with this name already exists under the selected control.',
            ], 409);
        }

        LedgerAccount::create([
            'ControlID'   => $request->ControlID,
            'CategoryID'  => $request->CategoryID,
            'Class'       => $request->Class,
            'Nature'      => $request->Nature,
            'Type'        => $request->Type,
            'AccountName' => ucwords(strtolower(trim($request->AccountName))),
            'Date'        => now()->toDateString(),
            'Time'        => now()->toDateTimeString(),
            'Status'      => 1,
            'Visible'     => 1,
            'Username'    => Auth::user()->ID,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ledger account added successfully.',
        ]);
    }

    // Inline edit account name
    public function update(Request $request, int $id)
    {
        $request->validate([
            'AccountName' => ['required', 'string', 'max:150'],
        ]);

        $account = LedgerAccount::findOrFail($id);

        // Check for duplicate under same control excluding current
        $exists = LedgerAccount::where('ControlID', $account->ControlID)
            ->where('AccountName', ucwords(strtolower(trim($request->AccountName))))
            ->where('AccountNo', '!=', $id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'An account with this name already exists under the same control.',
            ], 409);
        }

        $account->AccountName = ucwords(strtolower(trim($request->AccountName)));
        $account->save();

        return response()->json([
            'success'     => true,
            'message'     => 'Account name updated successfully.',
            'AccountName' => $account->AccountName,
        ]);
    }

    // Toggle visibility
    public function toggleVisible(int $id)
    {
        $account          = LedgerAccount::findOrFail($id);
        $account->Visible = $account->Visible ? 0 : 1;
        $account->save();

        return response()->json([
            'success' => true,
            'visible' => $account->Visible,
        ]);
    }

    // Soft delete
    public function deactivate(int $id)
    {
        $account         = LedgerAccount::findOrFail($id);
        $account->Status = 0;
        $account->save();

        return response()->json([
            'success' => true,
            'message' => 'Ledger account deactivated successfully.',
        ]);
    }

    // Restore
    public function restore(int $id)
    {
        $account         = LedgerAccount::findOrFail($id);
        $account->Status = 1;
        $account->save();

        return response()->json([
            'success' => true,
            'message' => 'Ledger account restored successfully.',
        ]);
    }

    // AJAX — get categories filtered by type
    public function categoriesByType(Request $request)
    {
        $request->validate([
            'type' => ['required', 'in:GL,INCOME,EXPENDITURE'],
        ]);

        $categories = LedgerCategory::active()
            ->where('Type', $request->type)
            ->orderBy('CategoryID')
            ->orderBy('SubCategoryID')
            ->get(['SubCategoryID', 'SubCategoryName', 'CategoryName', 'Class']);

        return response()->json($categories);
    }
}
