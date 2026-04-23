<?php

namespace App\Http\Controllers;

use App\Services\ReceiptService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ConsignmentRevenueController extends Controller
{
    // ──────────────────────────────────────────────────────────────────────────
    // PAGE LOAD
    // ──────────────────────────────────────────────────────────────────────────

    public function index()
    {
        // Active consignment revenue income account
        $incomeAccount = DB::table('active_consignment_revenue as acr')
            ->join('ledger_account as la', 'acr.AccountNo', '=', 'la.AccountNo')
            ->where('la.Status', 1)
            ->first(['la.AccountNo', 'la.AccountName']);

        // Cash/bank accounts
        $cashAccounts = DB::table('active_bank_cash as abc')
            ->join('ledger_account as la', 'abc.AccountID', '=', 'la.AccountNo')
            ->where('la.Status', 1)
            ->orderBy('la.AccountName')
            ->get(['la.AccountNo', 'la.AccountName']);

        return view('disbursement.consignment-revenue', compact(
            'incomeAccount',
            'cashAccounts'
        ));
    }

    // ──────────────────────────────────────────────────────────────────────────
    // SEARCH BL
    // ──────────────────────────────────────────────────────────────────────────

    public function searchBL(Request $request)
    {
        $q = strtoupper(trim($request->q ?? ''));

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $results = DB::table('active_consignment_commodities_2')
            ->where('BL', 'like', "%{$q}%")
            ->select(
                'ConsignmentID',
                'BL',
                'ConsigneeID',
                'Destination',
                'Status'
            )
            ->orderByDesc('ConsignmentID')
            ->limit(8)
            ->get();

        return response()->json($results);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // SAVE
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Save Consignment Revenue transaction.
     * Writes to: receipt_main, pnl_transaction, journal (x2), disbursement_analysis.
     * All entries marked Restricted=2, InReport=1.
     */
    public function save(Request $request)
    {
        $request->validate([
            'BL' => ['required', 'string', 'max:50'],
            'ConsigneeID' => ['required', 'integer'],
            'AccountNo' => ['required', 'integer'],
            'CashAccount' => ['required', 'integer'],
            'Amount' => ['required', 'numeric', 'min:0.01'],
            'Description' => ['required', 'string', 'max:500'],
            'PaymentDate' => ['required', 'date', 'before_or_equal:today'],
        ]);

        $user = Auth::user();
        $bl = strtoupper(trim($request->BL));

        // ── 1. Verify BL exists ───────────────────────────────────────────────
        $consignment = DB::table('active_consignment_commodities_2')
            ->where('BL', $bl)
            ->first();

        if (! $consignment) {
            return response()->json([
                'success' => false,
                'message' => "BL# {$bl} not found.",
            ], 404);
        }

        // ── 2. Active IE account ──────────────────────────────────────────────
        $ieAccount = DB::table('active_ie')->first();
        if (! $ieAccount) {
            return response()->json([
                'success' => false,
                'message' => 'Active IE account not configured. Set it up in Basic Setup.',
            ], 422);
        }

        // ── 3. Verify income account exists ──────────────────────────────────
        $incomeAccount = DB::table('active_consignment_revenue as acr')
            ->join('ledger_account as la', 'acr.AccountNo', '=', 'la.AccountNo')
            ->where('la.AccountNo', $request->AccountNo)
            ->first();

        if (! $incomeAccount) {
            return response()->json([
                'success' => false,
                'message' => 'Selected income account not found or not configured.',
            ], 422);
        }

        // ── 4. Generate receipt ───────────────────────────────────────────────
        $receipt = ReceiptService::generate($request->PaymentDate);

        DB::beginTransaction();

        try {
            // a. receipt_main
            DB::table('receipt_main')->insert([
                'ID' => $receipt['id'],
                'Date' => $request->PaymentDate,
                'ReceiptNo' => $receipt['receipt_no'],
                'Username' => $user->ID,
                'Time' => now()->toDateTimeString(),
            ]);

            // b. pnl_transaction — Cr on income account
            DB::table('pnl_transaction')->insert([
                'AccountID' => $request->AccountNo,
                'Stamp' => 'NB',
                'Mode' => 'Cr',
                'MainBL' => $bl,
                'HouseBL' => $bl,
                'ReceiptNo' => $receipt['receipt_no'],
                'Description' => $request->Description,
                'Dr' => 0,
                'Cr' => $request->Amount,
                'Date' => $request->PaymentDate,
                'Time' => now()->toDateTimeString(),
                'BranchID' => $user->BranchID,
                'Username' => $user->ID,
                'Status' => 1,
                'Restricted' => 2,
                'InReport' => 1,
            ]);

            // c. journal Dr — GL cash account
            DB::table('journal')->insert([
                'AccountID' => $request->CashAccount,
                'SubAccountID' => $request->CashAccount,
                'Mode' => 'Dr',
                'TType' => 'Cash',
                'ReceiptNo' => $receipt['receipt_no'],
                'Dr' => $request->Amount,
                'Cr' => 0,
                'Description' => $request->Description,
                'Date' => $request->PaymentDate,
                'Time' => now()->toDateTimeString(),
                'Username' => $user->ID,
                'Authorizer' => 'N.Auth',
                'BranchID' => $user->BranchID,
                'Status' => 1,
                'Restricted' => 2,
                'InReport' => 1,
            ]);

            // d. journal Cr — IE account → income account
            DB::table('journal')->insert([
                'AccountID' => $ieAccount->AccountID,
                'SubAccountID' => $request->AccountNo,
                'Mode' => 'Cr',
                'TType' => 'Cash',
                'ReceiptNo' => $receipt['receipt_no'],
                'Dr' => 0,
                'Cr' => $request->Amount,
                'Description' => $request->Description,
                'Date' => $request->PaymentDate,
                'Time' => now()->toDateTimeString(),
                'Username' => $user->ID,
                'Authorizer' => 'N.Auth',
                'BranchID' => $user->BranchID,
                'Status' => 1,
                'Restricted' => 2,
                'InReport' => 1,
            ]);

            // e. disbursement_analysis — Revenue entry
            DB::table('disbursement_analysis')->insert([
                'ConsigneeID' => $request->ConsigneeID,
                'BL' => $bl,
                'HBL' => $bl,
                'ContainerNo' => '',
                'TotalCashReceipt' => $request->Amount,
                'ReceiptNo' => $receipt['receipt_no'],
                'AccountID' => $request->AccountNo,
                'Revenue' => $request->Amount,
                'Expenditure' => 0,
                'Stamp' => 'CNS-REV',
                'Username' => $user->ID,
                'Date' => $request->PaymentDate,
                'Time' => now()->toDateTimeString(),
                'Status' => 2,
                'Type' => 'FCL',
                'Restricted' => 2,
                'InReport' => 1,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Consignment revenue for BL# {$bl} saved successfully.",
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to save transaction. Please try again.',
                'debug' => app()->environment('local') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
