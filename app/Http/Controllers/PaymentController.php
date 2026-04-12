<?php

namespace App\Http\Controllers;

use App\Services\ReceiptService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    // ──────────────────────────────────────────────
    // PROCESS DECLARATION
    // ──────────────────────────────────────────────

    /**
     * Show the Process Declaration form.
     * Loads cash accounts from active_bank_cash joined with ledger_account.
     */
    public function declaration()
    {
        // Load active cash accounts for the "Select Account" dropdown
        // These are bank/cash accounts configured in Basic Setup → Active Accounts
        $cashAccounts = DB::table('active_bank_cash as abc')
            ->join('ledger_account as la', 'abc.AccountID', '=', 'la.AccountNo')
            ->where('la.Status', 1)
            ->orderBy('la.AccountName')
            ->get(['la.AccountNo', 'la.AccountName']);

        // Generate receipt number for this transaction
        $receipt = ReceiptService::generate(now()->toDateString());

        return view('payments.declaration', compact('cashAccounts', 'receipt'));
    }

    /**
     * Search BL numbers for the typeahead dropdown.
     * Only shows consignments that haven't been declared yet (Status = 1).
     * Rate limited — see routes/web.php.
     */
    public function searchBL(Request $request)
    {
        $q = trim($request->q ?? '');
        if (strlen($q) < 2) {
            return response()->json([]);
        }

        // Fetch from manifestation_breakdown joined with container_main
        // Search by either MainBL or HouseBL
        // Show results as "MainBL - HouseBL" format
        $results = DB::table('manifestation_breakdown as mb')
            ->join('container_main as cm', 'mb.ConsignmentID', '=', 'cm.ConsignmentID')
            ->where(function ($query) use ($q) {
                $query->where('mb.MainBL', 'like', "%{$q}%")
                    ->orWhere('mb.HouseBL', 'like', "%{$q}%");
            })
            ->where('mb.Status', 1)
            ->whereNotExists(function ($query) {
                // Exclude House BLs already declared
                $query->select(DB::raw(1))
                    ->from('declaration_main')
                    ->whereColumn('declaration_main.BL', 'mb.HouseBL');
            })
            ->limit(8)
            ->orderBy('mb.MainBL')
            ->get([
                'mb.MainBL',
                'mb.HouseBL',
                'mb.Description',
                'cm.ContainerSize',
                'cm.ETA',
            ]);

        // Format label as "MainBL - HouseBL" for display
        return response()->json($results->map(fn ($r) => [
            'BL' => $r->HouseBL, // value stored is HouseBL
            'label' => $r->MainBL.' - '.$r->HouseBL, // display label
            'Description' => $r->Description,
            'ContainerSize' => $r->ContainerSize,
            'ETA' => $r->ETA,
        ]));
    }

    /**
     * Save the declaration and all related accounting entries.
     * Writes to: receipt_main, journal (x2), pnl_transaction, declaration_main
     */
    public function storeDeclaration(Request $request)
    {
        // Validate all inputs — strict rules for financial data
        $request->validate([
            'BL' => ['required', 'string', 'max:50'],
            'DeclarationNo' => ['required', 'string', 'max:50'],
            'Description' => ['required', 'string'],
            'DutyPaid' => ['required', 'numeric', 'min:0.01'],
            'Amount' => ['required', 'numeric', 'min:0.01'],
            'AgentName' => ['required', 'string', 'max:200'],
            'AgentContact' => ['required', 'string', 'max:30'],
            'ContainerSize' => ['required', 'string', 'max:50'],
            'AccountNo' => ['required', 'integer'],
            'PaymentDate' => ['required', 'date', 'before_or_equal:today'],
            'ReceiptID' => ['required', 'string'],
            'ReceiptNo' => ['required', 'string'],
        ]);

        $user = Auth::user();
        $bl = strtoupper(trim($request->BL));

        // ── Pre-requisite checks ──

        // 1. Check active IE account is configured
        $ieAccount = DB::table('active_ie')->first();
        if (! $ieAccount) {
            return response()->json([
                'success' => false,
                'message' => 'Active IE account not configured. Set it up in Basic Setup.',
            ], 422);
        }

        // 2. Check active declaration income account is configured
        $dclIncomeAccount = DB::table('active_declaration_income')->first();
        if (! $dclIncomeAccount) {
            return response()->json([
                'success' => false,
                'message' => 'Active Declaration Income account not configured. Set it up in Basic Setup.',
            ], 422);
        }

        // 3. Check Declaration No. is unique
        $dclExists = DB::table('declaration_main')
            ->where('DeclarationNo', $request->DeclarationNo)
            ->exists();
        if ($dclExists) {
            return response()->json([
                'success' => false,
                'message' => 'Declaration No. already exists.',
            ], 409);
        }

        // 4. Check BL not already declared
        $blExists = DB::table('declaration_main')
            ->where('BL', $bl)
            ->exists();
        if ($blExists) {
            return response()->json([
                'success' => false,
                'message' => 'BL# '.$bl.' has already been declared.',
            ], 409);
        }

        // 5. Check receipt number is unique
        $receiptExists = DB::table('receipt_main')
            ->where('ReceiptNo', $request->ReceiptNo)
            ->exists();
        if ($receiptExists) {
            return response()->json([
                'success' => false,
                'message' => 'Receipt number already exists. Please refresh and try again.',
            ], 409);
        }

        // 6. Get next DeclarationID
        $lastID = DB::table('declaration_main')->max('DeclarationID') ?? 100000;
        $declarationID = $lastID + 1;

        // Build journal description
        $description = 'DECLARATION CHARGE IFO ~ '.$request->DeclarationNo;

        DB::beginTransaction();

        try {
            // a. Insert receipt reference
            DB::table('receipt_main')->insert([
                'ID' => $request->ReceiptID,
                'Date' => $request->PaymentDate,
                'ReceiptNo' => $request->ReceiptNo,
                'Username' => $user->ID,
                'Time' => now()->toDateTimeString(),
            ]);

            // b. Journal entry — Dr (cash received into bank/cash account)
            DB::table('journal')->insert([
                'AccountID' => $request->AccountNo,
                'SubAccountID' => $request->AccountNo,
                'Mode' => 'Dr',
                'TType' => 'Cash',
                'ReceiptNo' => $request->ReceiptNo,
                'Dr' => $request->Amount,
                'Cr' => 0,
                'Description' => $description,
                'Date' => $request->PaymentDate,
                'Username' => $user->ID,
                'Authorizer' => 'N.Auth',
                'BranchID' => $user->BranchID,
                'Status' => 1,
            ]);

            // c. Journal entry — Cr (income recognised in IE/Declaration income account)
            DB::table('journal')->insert([
                'AccountID' => $ieAccount->AccountID,
                'SubAccountID' => $dclIncomeAccount->AccountNo,
                'Mode' => 'Cr',
                'TType' => 'Cash',
                'ReceiptNo' => $request->ReceiptNo,
                'Dr' => 0,
                'Cr' => $request->Amount,
                'Description' => $description,
                'Date' => $request->PaymentDate,
                'Username' => $user->ID,
                'Authorizer' => 'N.Auth',
                'BranchID' => $user->BranchID,
                'Status' => 1,
            ]);

            // d. P&L transaction — records income against the declaration income account
            DB::table('pnl_transaction')->insert([
                'AccountID' => $dclIncomeAccount->AccountNo,
                'Stamp' => 'NB',
                'Mode' => 'Cr',
                'MainBL' => $bl,
                'HouseBL' => $bl,
                'ReceiptNo' => $request->ReceiptNo,
                'Description' => $description,
                'Dr' => 0,
                'Cr' => $request->Amount,
                'Date' => $request->PaymentDate,
                'BranchID' => $user->BranchID,
                'Username' => $user->ID,
                'Status' => 1,
            ]);

            // e. Insert declaration record
            DB::table('declaration_main')->insert([
                'DeclarationID' => $declarationID,
                'BL' => $bl,
                'DeclarationNo' => trim($request->DeclarationNo),
                'ItemDescription' => trim($request->Description),
                'DutyPaid' => $request->DutyPaid,
                'Amount' => $request->Amount,
                'AgentName' => trim($request->AgentName),
                'AgentContact' => trim($request->AgentContact),
                'ContainerSize' => trim($request->ContainerSize),
                'ReceiptNo' => $request->ReceiptNo,
                'Date' => $request->PaymentDate,
                'Time' => now()->toDateTimeString(),
                'Username' => $user->ID,
                'BranchID' => $user->BranchID,
                'Status' => 1,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Declaration saved successfully.',
                'ReceiptNo' => $request->ReceiptNo,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            // Never expose internal error details to the client
            return response()->json([
                'success' => false,
                'message' => 'Failed to save declaration. Please try again.',
                // Debug info only visible in local environment
                'debug' => app()->environment('local') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Print receipt for a saved declaration.
     */
    public function declarationReport(string $receiptNo)
    {
        // Fetch declaration — BL stored is the HouseBL from manifestation_breakdown
        // Use left join with manifestation_breakdown then container_main
        // so the report still renders even if consignment info is missing
        $declaration = DB::table('declaration_main as d')
            ->leftJoin('manifestation_breakdown as mb', 'd.BL', '=', 'mb.HouseBL')
            ->leftJoin('container_main as cm', 'mb.ConsignmentID', '=', 'cm.ConsignmentID')
            ->where('d.ReceiptNo', $receiptNo)
            ->select(
                'd.*',
                'mb.MainBL',
                'cm.VesselName',
                'cm.ETA',
                'cm.ContainerNo',
            )
            ->first();

        if (! $declaration) {
            abort(404, 'Declaration not found for receipt# '.$receiptNo);
        }

        $bankDetails = DB::table('bank_details')->where('is_active', 1)->first();

        return view('payments.declaration-report', compact('declaration', 'bankDetails'));
    }
}
