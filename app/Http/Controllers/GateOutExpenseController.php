<?php

namespace App\Http\Controllers;

use App\Services\ReceiptService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GateOutExpenseController extends Controller
{
    // ──────────────────────────────────────────────────────────────────────────
    // PAGE LOAD
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Show the Gate-Out Expense page.
     * Pre-loads all gated-out consignments and disbursement accounts.
     */
    public function index()
    {
        // Load all gated-out consignments for the dropdown
        // Distinct BLs from container_gate_out_view (Status=3)
        $consignments = DB::table('container_gate_out_view')
            ->select('BL')
            ->distinct()
            ->orderBy('BL')
            ->get();

        // All active expenditure accounts
        $expenseAccounts = DB::table('ledger_account')
            ->where('Status', 1)
            ->where('Type', 'EXPENDITURE')
            ->orderBy('AccountName')
            ->get(['AccountNo', 'AccountName']);

        // Cash/bank accounts
        $cashAccounts = DB::table('active_bank_cash as abc')
            ->join('ledger_account as la', 'abc.AccountID', '=', 'la.AccountNo')
            ->where('la.Status', 1)
            ->orderBy('la.AccountName')
            ->get(['la.AccountNo', 'la.AccountName']);

        return view('disbursement.gate-out', compact(
            'consignments',
            'expenseAccounts',
            'cashAccounts'
        ));
    }

    // ──────────────────────────────────────────────────────────────────────────
    // GET CONSIGNMENTS
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Return all gated-out consignments as JSON for dynamic dropdown refresh.
     */
    public function getConsignments()
    {
        $consignments = DB::table('container_gate_out_view')
            ->select('BL')
            ->distinct()
            ->orderBy('BL')
            ->get();

        return response()->json($consignments);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // SAVE
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Save the Gate-Out expense transaction.
     * Writes to: receipt_main, pnl_transaction, disbursement_analysis,
     *            journal (x2), disbursment_gateout_truck_details (if truck provided)
     */
    public function save(Request $request)
    {
        $request->validate([
            'BL' => ['required', 'string', 'max:50'],
            'AccountNo' => ['required', 'integer'],
            'CashAccount' => ['required', 'integer'],
            'Amount' => ['required', 'numeric', 'min:0.01'],
            'Description' => ['required', 'string', 'max:500'],
            'PaymentDate' => ['required', 'date', 'before_or_equal:today'],
            'TruckNo' => ['nullable', 'string', 'max:50'],
            'DriverContact' => ['nullable', 'string', 'max:50'],
        ]);

        $user = Auth::user();
        $bl = strtoupper(trim($request->BL));

        // ── 1. Verify consignment exists in container_gate_out_view ───────────
        $consignment = DB::table('container_gate_out_view')
            ->where('BL', $bl)
            ->first();

        if (! $consignment) {
            return response()->json([
                'success' => false,
                'message' => "Consignment BL# {$bl} not found or not gated out.",
            ], 404);
        }

        // ── 2. Block if Gate-Out disbursement already exists for this BL ──────
        $alreadyExists = DB::table('disbursement_analysis')
            ->where('BL', $bl)
            ->where('Stamp', 'GATE-OUT')
            ->where('AccountID', $request->AccountNo)
            ->exists();

        if ($alreadyExists) {
            return response()->json([
                'success' => false,
                'message' => "A Gate-Out expense for this account already exists for BL# {$bl}.",
            ], 409);
        }

        // ── 3. Active IE account ──────────────────────────────────────────────
        $ieAccount = DB::table('active_ie')->first();
        if (! $ieAccount) {
            return response()->json([
                'success' => false,
                'message' => 'Active IE account not configured. Set it up in Basic Setup.',
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

            // b. pnl_transaction — Dr on expense account
            DB::table('pnl_transaction')->insert([
                'AccountID' => $request->AccountNo,
                'Stamp' => 'NB',
                'Mode' => 'Dr',
                'MainBL' => $bl,
                'HouseBL' => $bl,
                'ReceiptNo' => $receipt['receipt_no'],
                'Description' => $request->Description,
                'Dr' => $request->Amount,
                'Cr' => 0,
                'Date' => $request->PaymentDate,
                'Time' => now()->toDateTimeString(),
                'BranchID' => $user->BranchID,
                'Username' => $user->ID,
                'Status' => 1,
            ]);

            // c. disbursement_analysis — Status=3 for Gate-Out
            DB::table('disbursement_analysis')->insert([
                'ConsigneeID' => $consignment->ConsigneeID,
                'BL' => $bl,
                'HBL' => $bl,
                'ContainerNo' => $consignment->ContainerNo,
                'TotalCashReceipt' => $request->Amount,
                'ReceiptNo' => $receipt['receipt_no'],
                'AccountID' => $request->AccountNo,
                'Revenue' => 0,
                'Expenditure' => $request->Amount,
                'Stamp' => 'GATE-OUT',
                'Username' => $user->ID,
                'Date' => $request->PaymentDate,
                'Time' => now()->toDateTimeString(),
                'Status' => 3,
                'Type' => 'FCL',
            ]);

            // d. journal Dr — IE account → expense account
            DB::table('journal')->insert([
                'AccountID' => $ieAccount->AccountID,
                'SubAccountID' => $request->AccountNo,
                'Mode' => 'Dr',
                'TType' => 'Ncash',
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
            ]);

            // e. journal Cr — GL cash account
            DB::table('journal')->insert([
                'AccountID' => $request->CashAccount,
                'SubAccountID' => $request->CashAccount,
                'Mode' => 'Cr',
                'TType' => 'Ncash',
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
            ]);

            // f. Truck details — only if truck number provided
            if (! empty($request->TruckNo)) {
                $lastID = DB::table('disbursment_gateout_truck_details')->max('ID') ?? 0;

                DB::table('disbursment_gateout_truck_details')->insert([
                    'ID' => $lastID + 1,
                    'ConsignmentID' => $consignment->ConsignmentID,
                    'BL' => $bl,
                    'ReceiptNo' => $receipt['receipt_no'],
                    'TruckNumber' => $request->TruckNo,
                    'DriverContact' => $request->DriverContact ?? '',
                    'Username' => $user->ID,
                    'TIme' => now()->toDateTimeString(), // preserving typo from DB
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Gate-Out expense for BL# {$bl} saved successfully.",
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
