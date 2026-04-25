<?php

namespace App\Http\Controllers\EditData;

use App\Http\Controllers\Controller;
use App\Services\ReceiptService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EditDisbursementController extends Controller
{
    public function index()
    {
        $expenseAccounts = DB::table('ledger_account')
            ->where('Status', 1)
            ->where('Type', 'EXPENDITURE')
            ->orderBy('AccountName')
            ->get(['AccountNo', 'AccountName']);

        return view('edit-data.disbursement', compact('expenseAccounts'));
    }

    // ── BL Typeahead — only BLs that have disbursement_analysis entries ──
    public function searchBL(Request $request)
    {
        $q = strtoupper(trim($request->q ?? ''));

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $results = DB::table('disbursement_analysis')
            ->where('BL', 'like', "%{$q}%")
            ->groupBy('BL')
            ->orderBy('BL')
            ->limit(10)
            ->pluck('BL')
            ->map(fn ($bl) => ['BL' => $bl, 'label' => $bl]);

        return response()->json($results);
    }

    // ── Load all HBLs under a BL ──
    public function loadHBLs(Request $request)
    {
        $request->validate(['BL' => ['required', 'string', 'max:50']]);

        $bl = strtoupper(trim($request->BL));
        $user = Auth::user();
        $userAuth = \App\Models\UserAuth::where('Username', $user->ID)->first();

        $hbls = DB::table('disbursement_analysis as da')
            ->leftJoin('consignee_main as c', 'da.ConsigneeID', '=', 'c.ConsigneeID')
            ->where('da.BL', $bl)
            ->groupBy('da.HBL', 'da.ConsigneeID', 'da.Status', 'da.Restricted', 'c.FullName')
            ->orderBy('da.HBL')
            ->get([
                'da.HBL',
                'da.Status',
                'da.Restricted',
                'c.FullName as ConsigneeName',
            ]);

        if ($hbls->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No disbursement entries found for BL# '.$bl,
            ], 404);
        }

        // Filter based on restriction + permission
        $filtered = $hbls->filter(function ($row) use ($userAuth) {
            if ((int) $row->Restricted === 1 && ! $userAuth->hasPermission('DisbursementOtherExpense')) {
                return false;
            }
            if ((int) $row->Restricted === 2 && ! $userAuth->hasPermission('DisbursementRevenue')) {
                return false;
            }

            return true;
        })->values();

        if ($filtered->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to edit any disbursement entries for this BL.',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'hbls' => $filtered,
        ]);
    }

    // ── Load disbursement entries for BL + HBL ──
    public function loadEntries(Request $request)
    {
        $request->validate([
            'BL' => ['required', 'string', 'max:50'],
            'HBL' => ['required', 'string', 'max:30'],
        ]);

        $bl = strtoupper(trim($request->BL));
        $hbl = strtoupper(trim($request->HBL));
        $user = Auth::user();
        $userAuth = \App\Models\UserAuth::where('Username', $user->ID)->first();

        $entries = DB::table('disbursement_analysis as da')
            ->leftJoin('ledger_account as la', 'da.AccountID', '=', 'la.AccountNo')
            ->where('da.BL', $bl)
            ->where('da.HBL', $hbl)
            ->get([
                'da.BL',
                'da.HBL',
                'da.ConsigneeID',
                'da.ContainerNo',
                'da.TotalCashReceipt',
                'da.ReceiptNo',
                'da.AccountID',
                'la.AccountName',
                'da.Revenue',
                'da.Expenditure',
                'da.Stamp',
                'da.Type',
                'da.Status',
                'da.Restricted',
                'da.Date',
            ]);

        if ($entries->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No entries found for HBL# '.$hbl,
            ], 404);
        }

        // Block if approved
        if ($entries->contains(fn ($e) => (int) $e->Status === 0)) {
            return response()->json([
                'success' => false,
                'message' => 'This disbursement has been approved and cannot be edited.',
            ], 409);
        }

        // Enforce restrictions
        foreach ($entries as $entry) {
            if ((int) $entry->Restricted === 1 && ! $userAuth->hasPermission('DisbursementOtherExpense')) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to edit restricted disbursement entries.',
                ], 403);
            }
            if ((int) $entry->Restricted === 2 && ! $userAuth->hasPermission('DisbursementRevenue')) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to edit revenue disbursement entries.',
                ], 403);
            }
        }

        return response()->json([
            'success' => true,
            'entries' => $entries,
        ]);
    }

    // ── Update — reopen + re-save atomically ──
    public function update(Request $request)
    {
        $request->validate([
            'BL' => ['required', 'string', 'max:50'],
            'HBL' => ['required', 'string', 'max:30'],
            'PaymentDate' => ['required', 'date'],
            'CashAccountNo' => ['required', 'integer'],
            'rows' => ['required', 'array', 'min:1'],
            'rows.*.AccountID' => ['required', 'integer'],
            'rows.*.Expenditure' => ['required', 'numeric', 'min:0'],
            'rows.*.TotalCashReceipt' => ['required', 'numeric', 'min:0'],
            'rows.*.Type' => ['required', 'string', 'max:15'],
            'rows.*.ConsigneeID' => ['required', 'string'],
            'rows.*.ContainerNo' => ['required', 'string'],
            'rows.*.Stamp' => ['required', 'string'],
            'rows.*.Restricted' => ['required', 'integer'],
        ]);

        $bl = strtoupper(trim($request->BL));
        $hbl = strtoupper(trim($request->HBL));
        $user = Auth::user();
        $userAuth = \App\Models\UserAuth::where('Username', $user->ID)->first();

        // Re-check approval status
        $approved = DB::table('disbursement_analysis')
            ->where('BL', $bl)
            ->where('HBL', $hbl)
            ->where('Status', 0)
            ->exists();

        if ($approved) {
            return response()->json([
                'success' => false,
                'message' => 'This disbursement has been approved and cannot be edited.',
            ], 409);
        }

        // Re-check restrictions
        foreach ($request->rows as $row) {
            if ((int) $row['Restricted'] === 1 && ! $userAuth->hasPermission('DisbursementOtherExpense')) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to edit restricted entries.',
                ], 403);
            }
            if ((int) $row['Restricted'] === 2 && ! $userAuth->hasPermission('DisbursementRevenue')) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to edit revenue entries.',
                ], 403);
            }
        }

        // Get existing receipt and IE account
        $existingReceipt = DB::table('disbursement_analysis')
            ->where('BL', $bl)
            ->where('HBL', $hbl)
            ->value('ReceiptNo');

        $ieAccount = DB::table('active_ie')->first();

        if (! $ieAccount) {
            return response()->json([
                'success' => false,
                'message' => 'IE control account not configured.',
            ], 500);
        }

        DB::beginTransaction();

        try {
            // ── Step 1: Reopen — reverse all existing entries ──
            DB::table('journal')
                ->where('ReceiptNo', $existingReceipt)
                ->delete();

            DB::table('pnl_transaction')
                ->where('ReceiptNo', $existingReceipt)
                ->where('MainBL', $bl)
                ->delete();

            DB::table('receipt_main')
                ->where('ReceiptNo', $existingReceipt)
                ->delete();

            DB::table('disbursement_analysis')
                ->where('BL', $bl)
                ->where('HBL', $hbl)
                ->delete();

            // ── Step 2: Re-save with updated values ──
            $receipt = ReceiptService::generate($request->PaymentDate);
            $totalExpenditure = round(array_sum(array_column($request->rows, 'Expenditure')), 2);

            // receipt_main
            DB::table('receipt_main')->insert([
                'ID' => $receipt['id'],
                'Date' => $request->PaymentDate,
                'ReceiptNo' => $receipt['receipt_no'],
                'Username' => $user->ID,
                'Time' => now()->toDateTimeString(),
            ]);

            foreach ($request->rows as $row) {
                $amount = round((float) $row['Expenditure'], 2);

                // pnl_transaction Dr per row
                DB::table('pnl_transaction')->insert([
                    'AccountID' => $row['AccountID'],
                    'Stamp' => $row['Stamp'],
                    'Mode' => 'Dr',
                    'MainBL' => $bl,
                    'HouseBL' => $hbl,
                    'ReceiptNo' => $receipt['receipt_no'],
                    'Description' => "DISBURSEMENT IFO {$row['AccountID']}-{$bl}",
                    'Dr' => $amount,
                    'Cr' => 0,
                    'Date' => $request->PaymentDate,
                    'Time' => now()->toDateTimeString(),
                    'BranchID' => $user->BranchID,
                    'Username' => $user->ID,
                    'Status' => 2,
                ]);

                // journal Dr per row — IE account → expense account
                DB::table('journal')->insert([
                    'AccountID' => $ieAccount->AccountID,
                    'SubAccountID' => $row['AccountID'],
                    'Mode' => 'Dr',
                    'TType' => 'Cash',
                    'ReceiptNo' => $receipt['receipt_no'],
                    'Dr' => $amount,
                    'Cr' => 0,
                    'Description' => "EXPENDITURE PAYMENT ON - {$bl}",
                    'Date' => $request->PaymentDate,
                    'Time' => now()->toDateTimeString(),
                    'Username' => $user->ID,
                    'Authorizer' => 'N.Auth',
                    'BranchID' => $user->BranchID,
                    'Status' => 1,
                ]);

                // disbursement_analysis row
                DB::table('disbursement_analysis')->insert([
                    'ConsigneeID' => $row['ConsigneeID'],
                    'BL' => $bl,
                    'HBL' => $hbl,
                    'ContainerNo' => $row['ContainerNo'],
                    'TotalCashReceipt' => $row['TotalCashReceipt'],
                    'ReceiptNo' => $receipt['receipt_no'],
                    'AccountID' => $row['AccountID'],
                    'Revenue' => 0,
                    'Expenditure' => $amount,
                    'Stamp' => $row['Stamp'],
                    'Username' => $user->ID,
                    'Date' => $request->PaymentDate,
                    'Time' => now()->toDateTimeString(),
                    'Status' => 2,
                    'Type' => $row['Type'],
                    'Restricted' => $row['Restricted'],
                ]);
            }

            // journal Cr — GL cash account, total expenditure
            DB::table('journal')->insert([
                'AccountID' => $request->CashAccountNo,
                'SubAccountID' => $request->CashAccountNo,
                'Mode' => 'Cr',
                'TType' => 'Cash',
                'ReceiptNo' => $receipt['receipt_no'],
                'Dr' => 0,
                'Cr' => $totalExpenditure,
                'Description' => "TOTAL CASH DISBURSEMENT EXPENDITURE - {$bl}",
                'Date' => $request->PaymentDate,
                'Time' => now()->toDateTimeString(),
                'Username' => $user->ID,
                'Authorizer' => 'N.Auth',
                'BranchID' => $user->BranchID,
                'Status' => 1,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Disbursement for HBL# {$hbl} updated successfully.",
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to update disbursement. Please try again.',
                'debug' => app()->environment('local') ? $e->getMessage() : null,
            ], 500);
        }
    }

    // ADDED: cash accounts endpoint
    public function cashAccounts()
    {
        $accounts = DB::table('active_bank_cash as abc')
            ->join('ledger_account as la', 'abc.AccountID', '=', 'la.AccountNo')
            ->where('la.Status', 1)
            ->orderBy('la.AccountName')
            ->get(['la.AccountNo', 'la.AccountName']);

        return response()->json(['accounts' => $accounts]);
    }
}
