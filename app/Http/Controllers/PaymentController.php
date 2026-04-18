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
        if (strlen($q) < 1) {
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

    // ──────────────────────────────────────────────
    // RECEIVE HANDLING CHARGE
    // ──────────────────────────────────────────────

    /**
     * Show the Receive Handling Charge form.
     */
    public function handlCharge()
    {
        $cashAccounts = DB::table('active_bank_cash as abc')
            ->join('ledger_account as la', 'abc.AccountID', '=', 'la.AccountNo')
            ->where('la.Status', 1)
            ->orderBy('la.AccountName')
            ->get(['la.AccountNo', 'la.AccountName']);

        $receipt = ReceiptService::generate(now()->toDateString());

        return view('payments.handl-charge', compact('cashAccounts', 'receipt'));
    }

    /**
     * Typeahead search — HBLs that have an outstanding balance.
     * Searches by HouseBL, MainBL, or consignee name.
     */
    public function searchHBLForPayment(Request $request)
    {
        $q = trim($request->q ?? '');
        if (strlen($q) < 1) {
            return response()->json([]);
        }

        // Mirrors old query on student_fee_outstading_view_1:
        // search FullName (starts with), SubClassID/HouseBL (starts with),
        // CouponID/MainBL (contains) — only where Balance > 0
        $results = DB::table('student_fee as sf')
            ->join('consignee_main as c', 'sf.StudentID', '=', 'c.ConsigneeID')
            ->where('sf.Status', 1)
            ->where('sf.Stamp', 'BL')
            ->where(function ($query) use ($q) {
                $query->where('c.FullName', 'like', "{$q}%")
                    ->orWhere('sf.SubClassID', 'like', "{$q}%")
                    ->orWhere('sf.CouponID', 'like', "%{$q}%");
            })
            ->groupBy('sf.StudentID', 'sf.SubClassID', 'sf.CouponID', 'c.FullName')
            ->havingRaw('ROUND(SUM(sf.Dr) - SUM(sf.Cr), 2) > 0')
            ->orderBy('sf.SubClassID')
            ->limit(8)
            ->get([
                DB::raw('sf.StudentID as ConsigneeID'),
                DB::raw('sf.SubClassID as HouseBL'),
                DB::raw('sf.CouponID as MainBL'),
                'c.FullName',
            ]);

        return response()->json($results->map(fn ($r) => [
            'HouseBL' => $r->HouseBL,
            'MainBL' => $r->MainBL,
            'ConsigneeID' => $r->ConsigneeID,
            'FullName' => $r->FullName,
            'label' => $r->FullName.' · '.$r->MainBL.' · '.$r->HouseBL,
        ]));
    }

    /**
     * AJAX — return outstanding balance summary for a given HBL + consignee.
     * Called when user selects a result from the BL search typeahead.
     */
    public function getHBLBalance(Request $request)
    {
        $hbl = strtoupper(trim($request->hbl ?? ''));
        $consigneeId = trim($request->consignee_id ?? '');

        if (! $hbl || ! $consigneeId) {
            return response()->json(['success' => false, 'message' => 'Invalid request.'], 422);
        }

        $balance = DB::table('student_fee')
            ->where('StudentID', $consigneeId)
            ->where('SubClassID', $hbl)
            ->where('Stamp', 'BL')
            ->where('Status', 1)
            ->selectRaw('
            ROUND(SUM(Dr), 2)             AS TotalCharges,
            ROUND(SUM(Cr), 2)             AS AmountPaid,
            ROUND(SUM(Dr) - SUM(Cr), 2)  AS Balance
        ')
            ->first();

        if (! $balance || $balance->Balance <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'No outstanding balance found for this HBL.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'TotalCharges' => $balance->TotalCharges,
            'AmountPaid' => $balance->AmountPaid,
            'Balance' => $balance->Balance,
        ]);
    }

    /**
     * Save the handling charge payment and all related accounting entries.
     * Writes to: receipt_main, journal (Dr x1 + Cr x N), student_fee (Cr x N)
     */
    public function storeHandlCharge(Request $request)
    {
        $request->validate([
            'HouseBL' => ['required', 'string', 'max:50'],
            'MainBL' => ['required', 'string', 'max:50'],
            'ConsigneeID' => ['required'],
            'AccountNo' => ['required', 'integer'],
            'PaymentDate' => ['required', 'date', 'before_or_equal:today'],
            'Description' => ['required', 'string', 'max:255'],
            'ReceiptID' => ['required', 'string'],
            'ReceiptNo' => ['required', 'string'],
        ]);

        $user = Auth::user();
        $hbl = strtoupper(trim($request->HouseBL));
        $mainBL = strtoupper(trim($request->MainBL));

        // ── Pre-requisite checks ──

        // 1. Active IE account
        $ieAccount = DB::table('active_ie')->first();
        if (! $ieAccount) {
            return response()->json([
                'success' => false,
                'message' => 'Active IE account not configured. Set it up in Basic Setup.',
            ], 422);
        }

        // 2. Receipt number must be unique
        $receiptExists = DB::table('receipt_main')
            ->where('ReceiptNo', $request->ReceiptNo)
            ->exists();
        if ($receiptExists) {
            return response()->json([
                'success' => false,
                'message' => 'Receipt number already exists. Please refresh and try again.',
            ], 409);
        }

        // 3. Confirm outstanding balance still exists (prevents double-payment race condition)
        // 3. Fetch outstanding fee lines ordered by payment priority
        //    student_fee LEFT JOIN handling_charge for POrder (NULL → 0)
        $feeLines = DB::table('student_fee as sf')
            ->leftJoin('handling_charge as hc', 'sf.AccountNo', '=', 'hc.AccountNo')
            ->where('sf.StudentID', $request->ConsigneeID)
            ->where('sf.SubClassID', $hbl)
            ->where('sf.Stamp', 'BL')
            ->where('sf.Status', 1)
            ->groupBy('sf.AccountNo', 'hc.POrder')
            ->havingRaw('ROUND(SUM(sf.Dr) - SUM(sf.Cr), 2) > 0')
            ->orderByRaw('COALESCE(hc.POrder, 0)')
            ->get([
                'sf.AccountNo',
                DB::raw('COALESCE(hc.POrder, 0) as PmtOrder'),
                DB::raw('ROUND(SUM(sf.Dr) - SUM(sf.Cr), 2) AS Balance'),
            ]);

        if ($feeLines->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No outstanding balance found for HBL# '.$hbl.'. It may have already been paid.',
            ], 409);
        }

        // Total amount = sum of all line balances
        $totalAmount = round($feeLines->sum('Balance'), 2);
        $description = strtoupper(trim($request->Description));

        DB::beginTransaction();

        try {
            // a. Receipt reference
            DB::table('receipt_main')->insert([
                'ID' => $request->ReceiptID,
                'Date' => $request->PaymentDate,
                'ReceiptNo' => $request->ReceiptNo,
                'Username' => $user->ID,
                'Time' => now()->toDateTimeString(),
            ]);

            // b. Journal Dr — cash/bank account (total payment received)
            DB::table('journal')->insert([
                'AccountID' => $request->AccountNo,
                'SubAccountID' => $request->AccountNo,
                'Mode' => 'Dr',
                'TType' => 'Cash',
                'ReceiptNo' => $request->ReceiptNo,
                'Dr' => $totalAmount,
                'Cr' => 0,
                'Description' => $description,
                'Date' => $request->PaymentDate,
                'Username' => $user->ID,
                'Authorizer' => 'N.Auth',
                'BranchID' => $user->BranchID,
                'Status' => 1,
            ]);

            // c. Per fee line — Journal Cr (IE → fee account) + student_fee Cr row
            foreach ($feeLines as $line) {
                $lineAmount = round($line->Balance, 2);

                // Journal Cr — IE control → individual fee income account
                DB::table('journal')->insert([
                    'AccountID' => $ieAccount->AccountID,
                    'SubAccountID' => $line->AccountNo,
                    'Mode' => 'Cr',
                    'TType' => 'Cash',
                    'ReceiptNo' => $request->ReceiptNo,
                    'Dr' => 0,
                    'Cr' => $lineAmount,
                    'Description' => $description,
                    'Date' => $request->PaymentDate,
                    'Username' => $user->ID,
                    'Authorizer' => 'N.Auth',
                    'BranchID' => $user->BranchID,
                    'Status' => 1,
                ]);

                // student_fee Cr — clears the outstanding on the client ledger
                DB::table('student_fee')->insert([
                    'StudentID' => $request->ConsigneeID,
                    'SubClassID' => $hbl,
                    'CouponID' => $mainBL,
                    'AccountNo' => $line->AccountNo,
                    'Stamp' => 'BL',
                    'Description' => $description,
                    'ReceiptNo' => $request->ReceiptNo,
                    'Dr' => 0,
                    'Cr' => $lineAmount,
                    'Date' => $request->PaymentDate,
                    'Time' => now()->toDateTimeString(),
                    'Username' => $user->ID,
                    'Status' => 1,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment saved successfully for HBL# '.$hbl.'.',
                'ReceiptNo' => $request->ReceiptNo,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to save payment. Please try again.',
                'debug' => app()->environment('local') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Print receipt for a saved handling charge payment.
     */
    public function handlChargeReport(string $receiptNo)
    {
        // Get payment Cr rows from student_fee (these are the payment lines, not invoice lines)
        $lines = DB::table('student_fee as sf')
            ->join('ledger_account as la', 'sf.AccountNo', '=', 'la.AccountNo')
            ->where('sf.ReceiptNo', $receiptNo)
            ->where('sf.Stamp', 'BL')
            ->where('sf.Cr', '>', 0)
            ->orderBy('la.AccountName')
            ->get(['sf.*', 'la.AccountName']);

        if ($lines->isEmpty()) {
            abort(404, 'Receipt not found: '.$receiptNo);
        }

        $first = $lines->first();

        $consignee = DB::table('consignee_main')
            ->where('ConsigneeID', $first->StudentID)
            ->first();

        // Left join so receipt renders even if manifest data is missing
        $manifest = DB::table('manifestation_breakdown as mb')
            ->leftJoin('container_main as cm', 'mb.ConsignmentID', '=', 'cm.ConsignmentID')
            ->where('mb.HouseBL', $first->SubClassID)
            ->select('mb.MainBL', 'mb.HouseBL', 'cm.VesselName', 'cm.ETA')
            ->first();

        $bankDetails = DB::table('bank_details')->where('is_active', 1)->first();

        $total = round($lines->sum('Cr'), 2);

        return view('payments.handl-charge-report', compact(
            'lines', 'first', 'consignee', 'manifest', 'bankDetails', 'total', 'receiptNo'
        ));
    }

    // ──────────────────────────────────────────────
    // RECEIVE SERVICE CHARGE
    // ──────────────────────────────────────────────

    /**
     * Show the Receive Service Charge form.
     */
    public function servCharge()
    {
        $cashAccounts = DB::table('active_bank_cash as abc')
            ->join('ledger_account as la', 'abc.AccountID', '=', 'la.AccountNo')
            ->where('la.Status', 1)
            ->orderBy('la.AccountName')
            ->get(['la.AccountNo', 'la.AccountName']);

        $receipt = ReceiptService::generate(now()->toDateString());

        return view('payments.serv-charge', compact('cashAccounts', 'receipt'));
    }

    /**
     * Typeahead search — declarations that have not yet had a service charge raised.
     * Uses declaration_main_view_0 as instructed.
     */
    public function searchDclForServCharge(Request $request)
    {
        $q = trim($request->q ?? '');
        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $results = DB::table('declaration_main_view_0 as d')
            ->where(function ($query) use ($q) {
                $query->where('d.ConsigneeName', 'like', "%{$q}%")
                    ->orWhere('d.HBL', 'like', "%{$q}%");
            })
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('service_charge_main')
                    ->whereColumn('service_charge_main.BL', 'd.HBL');
            })
            ->orderBy('d.HBL')
            ->limit(8)
            ->get([
                'd.DeclarationID',
                'd.MainBL',
                'd.HBL',
                'd.ConsigneeID',
                'd.ConsigneeName',
                'd.DeclarationNo',
                'd.ItemDescription',
            ]);

        return response()->json($results->map(fn ($r) => [
            'DeclarationID' => $r->DeclarationID,
            'MainBL' => $r->MainBL,
            'HBL' => $r->HBL,
            'ConsigneeID' => $r->ConsigneeID,
            'ConsigneeName' => $r->ConsigneeName,
            'DeclarationNo' => $r->DeclarationNo,
            'ItemDescription' => $r->ItemDescription,
            'label' => $r->ConsigneeName.' · '.$r->HBL.' · '.$r->DeclarationNo,
        ]));
    }

    /**
     * Save the service charge payment and all related accounting entries.
     * Writes to: receipt_main, journal (x2), pnl_transaction, service_charge_main
     */
    public function storeServCharge(Request $request)
    {
        $request->validate([
            'HBL' => ['required', 'string', 'max:50'],
            'MainBL' => ['required', 'string', 'max:50'],
            'DeclarationID' => ['required', 'integer'],
            'DeclarationNo' => ['required', 'string', 'max:50'],
            'ConsigneeID' => ['required'],
            'ConsigneeName' => ['required', 'string', 'max:255'],
            'Description' => ['required', 'string', 'max:255'],
            'Amount' => ['required', 'numeric', 'min:0.01'],
            'AccountNo' => ['required', 'integer'],
            'PaymentDate' => ['required', 'date', 'before_or_equal:today'],
            'ReceiptID' => ['required', 'string'],
            'ReceiptNo' => ['required', 'string'],
        ]);

        $user = Auth::user();
        $hbl = strtoupper(trim($request->HBL));

        // ── Pre-requisite checks ──

        // 1. Active IE account
        $ieAccount = DB::table('active_ie')->first();
        if (! $ieAccount) {
            return response()->json([
                'success' => false,
                'message' => 'Active IE account not configured. Set it up in Basic Setup.',
            ], 422);
        }

        // 2. Active service charge income account
        $servChargeAccount = DB::table('active_service_charge')->first();
        if (! $servChargeAccount) {
            return response()->json([
                'success' => false,
                'message' => 'Active service charge income account not configured. Set it up in Basic Setup.',
            ], 422);
        }

        // 3. DeclarationNo must be unique in service_charge_main
        $dclExists = DB::table('service_charge_main')
            ->where('DeclarationNo', $request->DeclarationNo)
            ->exists();
        if ($dclExists) {
            return response()->json([
                'success' => false,
                'message' => 'Declaration No. already has a service charge recorded.',
            ], 409);
        }

        // 4. BL must be unique in service_charge_main
        $blExists = DB::table('service_charge_main')
            ->where('BL', $hbl)
            ->exists();
        if ($blExists) {
            return response()->json([
                'success' => false,
                'message' => 'HBL# '.$hbl.' already has a service charge recorded.',
            ], 409);
        }

        // 5. Receipt number must be unique
        $receiptExists = DB::table('receipt_main')
            ->where('ReceiptNo', $request->ReceiptNo)
            ->exists();
        if ($receiptExists) {
            return response()->json([
                'success' => false,
                'message' => 'Receipt number already exists. Please refresh and try again.',
            ], 409);
        }

        $description = 'SERVICE CHARGE IFO ~ '.$request->DeclarationNo;

        DB::beginTransaction();

        try {
            // a. Receipt reference
            DB::table('receipt_main')->insert([
                'ID' => $request->ReceiptID,
                'Date' => $request->PaymentDate,
                'ReceiptNo' => $request->ReceiptNo,
                'Username' => $user->ID,
                'Time' => now()->toDateTimeString(),
            ]);

            // b. Journal Dr — cash/bank account
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

            // c. Journal Cr — IE control → service charge income account
            DB::table('journal')->insert([
                'AccountID' => $ieAccount->AccountID,
                'SubAccountID' => $servChargeAccount->AccountNo,
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

            // d. P&L transaction
            DB::table('pnl_transaction')->insert([
                'AccountID' => $servChargeAccount->AccountNo,
                'Stamp' => 'NB',
                'Mode' => 'Cr',
                'MainBL' => $hbl,
                'HouseBL' => $hbl,
                'ReceiptNo' => $request->ReceiptNo,
                'Description' => $description,
                'Dr' => 0,
                'Cr' => $request->Amount,
                'Date' => $request->PaymentDate,
                'Time' => now()->toDateTimeString(),
                'BranchID' => $user->BranchID,
                'Username' => $user->ID,
                'Status' => 1,
            ]);

            // e. Service charge main record
            DB::table('service_charge_main')->insert([
                'BL' => $hbl,
                'DeclarationID' => $request->DeclarationID,
                'DeclarationNo' => trim($request->DeclarationNo),
                'ConsigneeID' => $request->ConsigneeID,
                'ConsigneeName' => trim($request->ConsigneeName),
                'Description' => trim($request->Description),
                'Amount' => $request->Amount,
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
                'message' => 'Service charge saved successfully for HBL# '.$hbl.'.',
                'ReceiptNo' => $request->ReceiptNo,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to save service charge. Please try again.',
                'debug' => app()->environment('local') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Print receipt for a saved service charge.
     */
    public function servChargeReport(string $receiptNo)
    {
        $charge = DB::table('service_charge_main as s')
            ->leftJoin('manifestation_breakdown as mb', 's.BL', '=', 'mb.HouseBL')
            ->leftJoin('container_main as cm', 'mb.ConsignmentID', '=', 'cm.ConsignmentID')
            ->where('s.ReceiptNo', $receiptNo)
            ->select(
                's.*',
                'mb.MainBL',
                'cm.VesselName',
                'cm.ETA',
            )
            ->first();

        if (! $charge) {
            abort(404, 'Service charge receipt not found: '.$receiptNo);
        }

        $bankDetails = DB::table('bank_details')->where('is_active', 1)->first();

        return view('payments.serv-charge-report', compact('charge', 'bankDetails', 'receiptNo'));
    }

    // ──────────────────────────────────────────────
    // HANDLING CHARGE EXPENSE
    // ──────────────────────────────────────────────

    /**
     * Show the Handling Charge Expense form.
     */
    public function handlingChargeExpense()
    {
        $cashAccounts = DB::table('active_bank_cash as abc')
            ->join('ledger_account as la', 'abc.AccountID', '=', 'la.AccountNo')
            ->where('la.Status', 1)
            ->orderBy('la.AccountName')
            ->get(['la.AccountNo', 'la.AccountName']);

        $expenditureAccounts = DB::table('ledger_account')
            ->where('Type', 'Expenditure')
            ->where('Status', 1)
            ->orderBy('AccountName')
            ->get(['AccountNo', 'AccountName']);

        $receipt = ReceiptService::generate(now()->toDateString());

        return view('payments.handling-charge-expense', compact(
            'cashAccounts',
            'expenditureAccounts',
            'receipt'
        ));
    }

    /**
     * Typeahead search — MainBLs that have an outstanding handling charge balance.
     * Uses container_exp_pmt_3 view as instructed.
     */
    public function searchMainBLForExpense(Request $request)
    {
        $q = trim($request->q ?? '');
        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $results = DB::table('container_exp_pmt_3')
            ->where('MainBL', 'like', "%{$q}%")
            ->orderBy('MainBL')
            ->limit(8)
            ->get([
                'ConsignmentID',
                'MainBL',
                'VesselName',
                'ShipperName',
                'TFee',
                'TDr',
                'Balance',
                'TDate',
            ]);

        return response()->json($results->map(fn ($r) => [
            'ConsignmentID' => $r->ConsignmentID,
            'MainBL' => $r->MainBL,
            'VesselName' => $r->VesselName,
            'ShipperName' => $r->ShipperName,
            'TFee' => $r->TFee,
            'TDr' => $r->TDr,
            'Balance' => $r->Balance,
            'TDate' => $r->TDate,
            'label' => $r->ShipperName.' '.$r->MainBL,
        ]));
    }

    /**
     * AJAX — return consignment balance details for a selected MainBL.
     */
    public function getConsignmentForExpense(Request $request)
    {
        $mainBL = strtoupper(trim($request->main_bl ?? ''));

        if (! $mainBL) {
            return response()->json(['success' => false, 'message' => 'Invalid request.'], 422);
        }

        $consignment = DB::table('container_exp_pmt_3')
            ->where('MainBL', $mainBL)
            ->first();

        if (! $consignment) {
            return response()->json([
                'success' => false,
                'message' => 'No outstanding balance found for Main BL# '.$mainBL,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'ConsignmentID' => $consignment->ConsignmentID,
            'MainBL' => $consignment->MainBL,
            'VesselName' => $consignment->VesselName,
            'ShipperName' => $consignment->ShipperName,
            'TFee' => $consignment->TFee,
            'TDr' => $consignment->TDr,
            'Balance' => $consignment->Balance,
            'TDate' => $consignment->TDate,
        ]);
    }

    /**
     * Save the handling charge expense and all related accounting entries.
     * Writes to: receipt_main, journal (Dr x1 + Cr x1), pnl_transaction
     */
    public function storeHandlingChargeExpense(Request $request)
    {
        $request->validate([
            'MainBL' => ['required', 'string', 'max:50'],
            'ConsignmentID' => ['required', 'integer'],
            'SourceAccountNo' => ['required', 'integer'],
            'ExpenditureAccountNo' => ['required', 'integer'],
            'Amount' => ['required', 'numeric', 'min:0.01'],
            'PaymentDate' => ['required', 'date', 'before_or_equal:today'],
            'Description' => ['required', 'string', 'max:255'],
            'ReceiptID' => ['required', 'string'],
            'ReceiptNo' => ['required', 'string'],
        ]);

        $user = Auth::user();
        $mainBL = strtoupper(trim($request->MainBL));

        // ── Pre-requisite checks ──

        // 1. Active IE account
        $ieAccount = DB::table('active_ie')->first();
        if (! $ieAccount) {
            return response()->json([
                'success' => false,
                'message' => 'Active IE account not configured. Set it up in Basic Setup.',
            ], 422);
        }

        // 2. Source and Expenditure accounts must be different
        if ($request->SourceAccountNo == $request->ExpenditureAccountNo) {
            return response()->json([
                'success' => false,
                'message' => 'Source account and expenditure account cannot be the same.',
            ], 422);
        }

        // 3. Confirm MainBL exists and get its details
        $consignment = DB::table('container_exp_pmt_3')
            ->where('MainBL', $mainBL)
            ->first();

        if (! $consignment) {
            return response()->json([
                'success' => false,
                'message' => 'Main BL# '.$mainBL.' not found or has no outstanding balance.',
            ], 404);
        }

        // 4. Amount cannot exceed balance
        if ($request->Amount > $consignment->Balance) {
            return response()->json([
                'success' => false,
                'message' => 'Expenditure amount cannot exceed outstanding balance of '.
                    number_format($consignment->Balance, 2),
            ], 422);
        }

        // 5. Payment date cannot be before TDate
        if ($request->PaymentDate < $consignment->TDate) {
            return response()->json([
                'success' => false,
                'message' => 'Payment date cannot be before '.
                    \Carbon\Carbon::parse($consignment->TDate)->format('M d, Y'),
            ], 422);
        }

        // 6. Receipt number must be unique
        $receiptExists = DB::table('receipt_main')
            ->where('ReceiptNo', $request->ReceiptNo)
            ->exists();
        if ($receiptExists) {
            return response()->json([
                'success' => false,
                'message' => 'Receipt number already exists. Please refresh and try again.',
            ], 409);
        }

        $description = strtoupper(trim($request->Description));

        DB::beginTransaction();

        try {
            // a. Receipt reference
            DB::table('receipt_main')->insert([
                'ID' => $request->ReceiptID,
                'Date' => $request->PaymentDate,
                'ReceiptNo' => $request->ReceiptNo,
                'Username' => $user->ID,
                'Time' => now()->toDateTimeString(),
            ]);

            // b. Journal Dr — IE control → Source Account (bank/cash)
            DB::table('journal')->insert([
                'AccountID' => $ieAccount->AccountID,
                'SubAccountID' => $request->SourceAccountNo,
                'Mode' => 'Dr',
                'TType' => 'NCash',
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

            // c. Journal Cr — Expenditure Account
            DB::table('journal')->insert([
                'AccountID' => $request->ExpenditureAccountNo,
                'SubAccountID' => $request->ExpenditureAccountNo,
                'Mode' => 'Cr',
                'TType' => 'NCash',
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

            // d. P&L transaction — Dr against Source Account
            DB::table('pnl_transaction')->insert([
                'AccountID' => $request->SourceAccountNo,
                'Stamp' => 'NB',
                'Mode' => 'Dr',
                'MainBL' => $mainBL,
                'HouseBL' => (string) $request->ConsignmentID,
                'ReceiptNo' => $request->ReceiptNo,
                'Description' => $description,
                'Dr' => $request->Amount,
                'Cr' => 0,
                'Date' => $request->PaymentDate,
                'Time' => now()->toDateTimeString(),
                'BranchID' => $user->BranchID,
                'Username' => $user->ID,
                'Status' => 1,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Handling charge expense saved successfully for Main BL# '.$mainBL.'.',
                'ReceiptNo' => $request->ReceiptNo,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to save expense. Please try again.',
                'debug' => app()->environment('local') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Print receipt for a saved handling charge expense.
     */
    public function handlingChargeExpenseReport(string $receiptNo)
    {
        // Get the pnl_transaction Dr row for this receipt
        $expense = DB::table('pnl_transaction as p')
            ->join('ledger_account as la', 'p.AccountID', '=', 'la.AccountNo')
            ->leftJoin('container_main as cm', 'p.HouseBL', '=', DB::raw('CAST(cm.ConsignmentID AS CHAR)'))
            ->where('p.ReceiptNo', $receiptNo)
            ->where('p.Mode', 'Dr')
            ->where('p.Stamp', 'NB')
            ->select(
                'p.*',
                'la.AccountName as SourceAccountName',
                'cm.VesselName',
                'cm.ETA',
            )
            ->first();

        if (! $expense) {
            abort(404, 'Expense receipt not found: '.$receiptNo);
        }

        // Get the Cr journal entry to show expenditure account
        $crEntry = DB::table('journal as j')
            ->join('ledger_account as la', 'j.AccountID', '=', 'la.AccountNo')
            ->where('j.ReceiptNo', $receiptNo)
            ->where('j.Mode', 'Cr')
            ->select('j.*', 'la.AccountName')
            ->first();

        $bankDetails = DB::table('bank_details')->where('is_active', 1)->first();

        return view('payments.handling-charge-expense-report', compact(
            'expense',
            'crEntry',
            'bankDetails',
            'receiptNo'
        ));
    }
}
