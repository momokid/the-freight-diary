<?php

namespace App\Http\Controllers;

use App\Models\DisbursementAnalysis;
use App\Services\ReceiptService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DisbursementAnalysisController extends Controller
{
    // ──────────────────────────────────────────────────────────────────────────
    // PAGE LOAD
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Show the Disbursement Analysis page.
     * Auto-restores any existing temp session for the current user.
     */
    public function index()
    {
        $user = Auth::user();

        // Cash/bank accounts for the Payment Account dropdown
        $cashAccounts = DB::table('active_bank_cash as abc')
            ->join('ledger_account as la', 'abc.AccountID', '=', 'la.AccountNo')
            ->where('la.Status', 1)
            ->orderBy('la.AccountName')
            ->get(['la.AccountNo', 'la.AccountName']);

        // Check for an existing temp session
        $tempRows = $this->getTempRows($user->ID);
        $workingBL = null;
        $workingHBL = null;
        $workingType = null;
        $consignment = null;
        $containers = collect();
        $hblList = collect();

        if (! empty($tempRows)) {
            $first = $tempRows[0];
            $workingBL = $first->BL;
            $workingHBL = $first->HouseBL;
            $workingType = $first->Type;

            $consignment = DB::table('container_main as cm')
                ->join('consignee_main as c', 'cm.ConsigneeID', '=', 'c.ConsigneeID')
                ->where('cm.BL', $workingBL)
                ->select('cm.*', 'c.FullName as ConsigneeName')
                ->first();

            if ($workingType === 'FCL') {
                $containers = DB::table('container_details')
                    ->where('BL', $workingBL)
                    ->get(['ContainerNo', 'ContainerSize', 'HandlingCost']);
            } else {
                $hblList = $this->getHBLList($workingBL);
            }
        }

        return view('disbursement.analysis', compact(
            'cashAccounts',
            'tempRows',
            'workingBL',
            'workingHBL',
            'workingType',
            'consignment',
            'containers',
            'hblList'
        ));
    }

    /**
     * Typeahead search — Main BL only.
     * Returns BLs that are not cleared (Status != 0).
     */
    public function searchBL(Request $request)
    {
        $q = strtoupper(trim($request->q ?? ''));

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $results = DB::table('inharbor_pending')
            ->where('BL', 'like', "%{$q}%")
            ->select(
                'ConsignmentID',
                'BL',
                'VesselName',
                'ETA',
                'ConsigneeName'
            )
            ->orderByDesc('ConsignmentID')
            ->limit(8)
            ->get();

        return response()->json($results);
    }


    /**
     * Load a Main BL — runs block checks and returns consignment context.
     * FCL → returns containers + initialises temp accounts.
     * LCL → returns HBL list only (user must click Load on a specific HBL).
     */
    public function loadBL(Request $request)
    {
        $request->validate([
            'BL' => ['required', 'string', 'max:50'],
            'Type' => ['required', 'in:FCL,LCL'],
        ]);

        $user = Auth::user();
        $bl = strtoupper(trim($request->BL));
        $type = $request->Type;

        // ── 1. User lock check ───────────────────────────────────────────────
        $existingTemp = DB::table('disbursement_temp_analysis')
            ->where('Username', $user->ID)
            ->first();

        if ($existingTemp && $existingTemp->BL !== $bl) {
            return response()->json([
                'success' => false,
                'code' => 'HAS_TEMP',
                'existingBL' => $existingTemp->BL,
                'message' => "You have an unsaved disbursement for {$existingTemp->BL}. Clear it or continue working on it.",
            ], 409);
        }

        $consignment = DB::table('inharbor_pending')
            ->where('BL', $bl)
            ->first();

        if (! $consignment) {
            return response()->json([
                'success' => false,
                'message' => "BL# {$bl} not found or not eligible for disbursement. Consignment must be in-harbor.",
            ], 404);
        }

        // ── 4. FCL block: MainBL already in disbursement_analysis? ──────────
        if ($type === 'FCL') {
            $existing = DisbursementAnalysis::where('BL', $bl)->first();

            if ($existing) {
                if ((int) $existing->Status === 0) {
                    return response()->json([
                        'success' => false,
                        'message' => "Disbursement for BL# {$bl} has been approved and is locked.",
                    ], 422);
                }

                // Status=2 — pending, can reopen
                return response()->json([
                    'success' => false,
                    'code' => 'CAN_REOPEN',
                    'BL' => $bl,
                    'HBL' => $bl,
                    'message' => "Disbursement for BL# {$bl} is pending approval.",
                ], 409);
            }

            // Load containers for reference panel
            $containers = DB::table('container_details')
                ->where('BL', $bl)
                ->get(['ContainerNo', 'ContainerSize', 'HandlingCost']);

            // Initialise temp accounts — first container drives ContainerNo
            $this->initTempAccounts(
                $user->ID,
                $bl,
                $bl, // FCL: HouseBL = MainBL
                $containers->first()?->ContainerNo ?? '',
                $consignment->ConsigneeID,
                'FCL'
            );

            return response()->json([
                'success' => true,
                'consignment' => $consignment,
                'containers' => $containers,
                'tempRows' => $this->getTempRows($user->ID),
                'savedExpenditure' => $this->getSavedExpenditure($bl),  // ADD
            ]);
        }

        // ── 5. LCL: return HBL list only ────────────────────────────────────
        return response()->json([
            'success' => true,
            'consignment' => $consignment,
            'hblList' => $this->getHBLList($bl),
            'savedExpenditure' => $this->getSavedExpenditure($bl),  // ADD
        ]);
    }

    public function hblList(Request $request)
    {
        $request->validate(['BL' => ['required', 'string', 'max:50']]);
        $bl = strtoupper(trim($request->BL));

        return response()->json([
            'success' => true,
            'hblList' => $this->getHBLList($bl),
        ]);
    }

    /**
     * Load a specific House BL for LCL disbursement entry.
     * Does NOT clear other HBL rows under the same MainBL.
     */
    public function loadHBL(Request $request)
    {
        $request->validate([
            'BL'  => ['required', 'string', 'max:50'],
            'HBL' => ['required', 'string', 'max:30'],
        ]);

        $user = Auth::user();
        $bl  = strtoupper(trim($request->BL));
        $hbl = strtoupper(trim($request->HBL));

        // ── 1. Lock check: block only if temp belongs to a different MainBL ──
        $existingTemp = DB::table('disbursement_temp_analysis')
            ->where('Username', $user->ID)
            ->first();

        if ($existingTemp && $existingTemp->BL !== $bl) {
            return response()->json([
                'success'     => false,
                'code'        => 'HAS_TEMP',
                'existingBL'  => $existingTemp->BL,
                'existingHBL' => $existingTemp->HouseBL,
                'message'     => "You have an unsaved disbursement for BL# {$existingTemp->BL}. Clear it or continue working on it.",
            ], 409);
        }

        // ── 2. Block check for this HBL ──────────────────────────────────────
        $existing = DisbursementAnalysis::where('BL', $bl)->where('HBL', $hbl)->first();

        if ($existing) {
            if ((int) $existing->Status === 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Disbursement for HBL# {$hbl} has been approved and is locked.",
                ], 422);
            }

            return response()->json([
                'success' => false,
                'code'    => 'CAN_REOPEN',
                'BL'      => $bl,
                'HBL'     => $hbl,
                'message' => "Disbursement for HBL# {$hbl} is pending approval.",
            ], 409);
        }

        // ── 3. Get HBL details ───────────────────────────────────────────────
        $hblDetails = DB::table('manifestation_breakdown as mb')
            ->join('consignee_main as c', 'mb.ConsigneeID', '=', 'c.ConsigneeID')
            ->where('mb.MainBL', $bl)
            ->where('mb.HouseBL', $hbl)
            ->select('mb.*', 'c.FullName as ConsigneeName')
            ->first();

        if (! $hblDetails) {
            return response()->json([
                'success' => false,
                'message' => "HBL# {$hbl} not found under BL# {$bl}.",
            ], 404);
        }

        // ── 4. Init accounts for this HBL only if not already in temp ────────
        $alreadyLoaded = DB::table('disbursement_temp_analysis')
            ->where('Username', $user->ID)
            ->where('HouseBL', $hbl)
            ->exists();

        if (! $alreadyLoaded) {
            $this->initTempAccounts(
                $user->ID,
                $bl,
                $hbl,
                $hblDetails->ContainerNo ?? '',
                $hblDetails->ConsigneeID,
                'LCL'
            );
        }

        return response()->json([
            'success'         => true,
            'hblDetails'      => $hblDetails,
            'tempRows'        => $this->getTempRows($user->ID),
            'savedExpenditure' => $this->getSavedExpenditure($bl),
        ]);
    }


    /**
     * Clear all temp rows for the current user.
     */
    public function clearTemp()
    {
        DB::table('disbursement_temp_analysis')
            ->where('Username', Auth::user()->ID)
            ->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Update the Amount for a specific account row in temp.
     * Called on input blur for each account row.
     */
    public function saveTempRow(Request $request)
    {
        $request->validate([
            'AccountNo' => ['required', 'integer'],
            'BL'        => ['required', 'string', 'max:50'],
            'HBL'       => ['required', 'string', 'max:30'],
            'Amount'    => ['required', 'numeric', 'min:0'],
        ]);

        DB::table('disbursement_temp_analysis')
            ->where('Username', Auth::user()->ID)
            ->where('BL', $request->BL)
            ->where('HouseBL', $request->HBL)
            ->where('AccountNo', $request->AccountNo)
            ->update([
                'Amount' => $request->Amount,
                'Time'   => now()->toDateTimeString(),
            ]);

        return response()->json(['success' => true]);
    }

    /**
     * Add an extra disbursement account row to temp.
     * Only allows accounts that exist in disbursement_accounts.
     */
    public function addTempRow(Request $request)
    {
        $request->validate([
            'AccountNo' => ['required', 'integer', 'exists:disbursement_accounts,AccountNo'],
        ]);

        $user = Auth::user();

        // Must have an active session
        $existing = DB::table('disbursement_temp_analysis')
            ->where('Username', $user->ID)
            ->first();

        if (! $existing) {
            return response()->json([
                'success' => false,
                'message' => 'No active disbursement session. Please load a BL first.',
            ], 422);
        }

        // Not already in temp
        $alreadyInTemp = DB::table('disbursement_temp_analysis')
            ->where('Username', $user->ID)
            ->where('AccountNo', $request->AccountNo)
            ->exists();

        if ($alreadyInTemp) {
            return response()->json([
                'success' => false,
                'message' => 'This account is already in the list.',
            ], 409);
        }

        $account = DB::table('ledger_account')
            ->where('AccountNo', $request->AccountNo)
            ->first(['AccountNo', 'AccountName']);

        DB::table('disbursement_temp_analysis')->insert([
            'AccountNo' => $request->AccountNo,
            'BL' => $existing->BL,
            'HouseBL' => $existing->HouseBL,
            'ContainerNo' => $existing->ContainerNo,
            'ConsigneeID' => $existing->ConsigneeID,
            'Amount' => 0,
            'Type' => $existing->Type,
            'Status' => '2',
            'Username' => $user->ID,
            'Time' => now()->toDateTimeString(),
        ]);

        return response()->json([
            'success' => true,
            'AccountNo' => $account->AccountNo,
            'AccountName' => $account->AccountName,
        ]);
    }

    /**
     * Remove a specific account row from temp.
     */
    public function deleteTempRow(Request $request, int $accountNo)
    {
        DB::table('disbursement_temp_analysis')
            ->where('Username', Auth::user()->ID)
            ->where('AccountNo', $accountNo)
            ->delete();

        return response()->json(['success' => true]);
    }


    /**
     * Process and save the disbursement.
     * Moves temp rows → disbursement_analysis + journal + pnl_transaction.
     */
    public function save(Request $request)
    {
        $request->validate([
            'BL'               => ['required', 'string', 'max:50'],
            'Type'             => ['required', 'in:FCL,LCL'],
            'AccountNo'        => ['required', 'integer'],
            'PaymentDate'      => ['required', 'date', 'before_or_equal:today'],
            'BudgetedExpenses' => ['nullable', 'numeric', 'min:0'],
        ]);

        $user             = Auth::user();
        $bl               = strtoupper(trim($request->BL));
        $type             = $request->Type;
        $budgetedExpenses = $request->BudgetedExpenses ?? 0;

        $tempRows = DB::table('disbursement_temp_analysis')
            ->where('Username', $user->ID)
            ->where('Status', '2')
            ->where('Amount', '>', 0)
            ->get();

        if ($tempRows->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No expense amounts entered. Please enter at least one amount.',
            ], 422);
        }

        $ieAccount = DB::table('active_ie')->first();
        if (! $ieAccount) {
            return response()->json([
                'success' => false,
                'message' => 'Active IE account not configured. Set it up in Basic Setup.',
            ], 422);
        }

        $cashAccount = DB::table('ledger_account')
            ->where('AccountNo', $request->AccountNo)
            ->where('Status', 1)
            ->first();

        if (! $cashAccount) {
            return response()->json([
                'success' => false,
                'message' => 'Selected payment account not found or inactive.',
            ], 422);
        }

        // Block check — before transaction begins
        $grouped = $tempRows->groupBy('HouseBL');

        foreach ($grouped as $hbl => $rows) {
            $alreadyExists = DisbursementAnalysis::where('BL', $bl)
                ->where('HBL', $hbl)
                ->exists();

            if ($alreadyExists) {
                return response()->json([
                    'success' => false,
                    'message' => "Disbursement already captured for HBL# {$hbl}.",
                ], 409);
            }
        }

        // Single receipt for all HBLs under this BL
        $receipt    = ReceiptService::generate($request->PaymentDate);
        $grandTotal = round($tempRows->sum('Amount'), 2);

        DB::beginTransaction();

        try {
            // One receipt_main entry for the whole BL save
            DB::table('receipt_main')->insert([
                'ID'        => $receipt['id'],
                'Date'      => $request->PaymentDate,
                'ReceiptNo' => $receipt['receipt_no'],
                'Username'  => $user->ID,
                'Time'      => now()->toDateTimeString(),
            ]);

            foreach ($grouped as $hbl => $rows) {
                foreach ($rows as $row) {
                    DB::table('pnl_transaction')->insert([
                        'AccountID'   => $row->AccountNo,
                        'Stamp'       => 'BL',
                        'Mode'        => 'Dr',
                        'MainBL'      => $bl,
                        'HouseBL'     => $hbl,
                        'ReceiptNo'   => $receipt['receipt_no'],
                        'Description' => "DISBURSEMENT IFO {$row->AccountNo}-{$bl}",
                        'Dr'          => $row->Amount,
                        'Cr'          => 0,
                        'Date'        => $request->PaymentDate,
                        'Time'        => now()->toDateTimeString(),
                        'BranchID'    => $user->BranchID,
                        'Username'    => $user->ID,
                        'Status'      => 2,
                    ]);

                    DB::table('journal')->insert([
                        'AccountID'    => $ieAccount->AccountID,
                        'SubAccountID' => $row->AccountNo,
                        'Mode'         => 'Dr',
                        'TType'        => 'Cash',
                        'ReceiptNo'    => $receipt['receipt_no'],
                        'Dr'           => $row->Amount,
                        'Cr'           => 0,
                        'Description'  => "EXPENDITURE PAYMENT ON - {$bl}",
                        'Date'         => $request->PaymentDate,
                        'Time'         => now()->toDateTimeString(),
                        'Username'     => $user->ID,
                        'Authorizer'   => 'N.Auth',
                        'BranchID'     => $user->BranchID,
                        'Status'       => 1,
                    ]);

                    DB::table('disbursement_analysis')->insert([
                        'ConsigneeID'      => $row->ConsigneeID,
                        'BL'               => $bl,
                        'HBL'              => $hbl,
                        'ContainerNo'      => $row->ContainerNo,
                        'TotalCashReceipt' => $budgetedExpenses,
                        'ReceiptNo'        => $receipt['receipt_no'],
                        'AccountID'        => $row->AccountNo,
                        'Revenue'          => 0,
                        'Expenditure'      => $row->Amount,
                        'Stamp'            => 'IN-HARBOR',
                        'Username'         => $user->ID,
                        'Date'             => $request->PaymentDate,
                        'Time'             => now()->toDateTimeString(),
                        'Status'           => 2,
                        'Type'             => $type,
                    ]);
                }
            }

            // Single Cr entry for the grand total across all HBLs
            DB::table('journal')->insert([
                'AccountID'    => $request->AccountNo,
                'SubAccountID' => $request->AccountNo,
                'Mode'         => 'Cr',
                'TType'        => 'Cash',
                'ReceiptNo'    => $receipt['receipt_no'],
                'Dr'           => 0,
                'Cr'           => $grandTotal,
                'Description'  => "TOTAL CASH DISBURSEMENT EXPENDITURE - {$bl}",
                'Date'         => $request->PaymentDate,
                'Time'         => now()->toDateTimeString(),
                'Username'     => $user->ID,
                'Authorizer'   => 'N.Auth',
                'BranchID'     => $user->BranchID,
                'Status'       => 1,
            ]);

            DB::table('disbursement_temp_analysis')
                ->where('Username', $user->ID)
                ->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Disbursement saved successfully for {$grouped->count()} HBL(s).",
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to save disbursement. Please try again.',
                'debug'   => app()->environment('local') ? $e->getMessage() : null,
            ], 500);
        }
    }

    // ──────────────────────────────────────────────────────────────────────────
    // REOPEN
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Reopen a pending disbursement (Status=2 only).
     * Reverses all accounting entries and restores data to temp.
     */
    public function reopen(Request $request)
    {
        $request->validate([
            'BL' => ['required', 'string', 'max:50'],
            'HBL' => ['required', 'string', 'max:30'],
            'Type' => ['required', 'in:FCL,LCL'],
        ]);

        $user = Auth::user();
        $bl = strtoupper(trim($request->BL));
        $hbl = strtoupper(trim($request->HBL));

        $entries = DisbursementAnalysis::where('BL', $bl)->where('HBL', $hbl)->get();

        if ($entries->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => "No disbursement found for {$hbl}.",
            ], 404);
        }

        // Block if any row is approved
        if ($entries->contains(fn($e) => (int) $e->Status === 0)) {
            return response()->json([
                'success' => false,
                'message' => "Disbursement for {$hbl} has been approved and cannot be reopened.",
            ], 422);
        }

        $receiptNo = $entries->first()->ReceiptNo;

        DB::beginTransaction();

        try {
            // Reverse all accounting entries for this receipt
            DB::table('pnl_transaction')->where('ReceiptNo', $receiptNo)->where('MainBL', $bl)->delete();
            DB::table('journal')->where('ReceiptNo', $receiptNo)->delete();
            DB::table('receipt_main')->where('ReceiptNo', $receiptNo)->delete();

            // Clear user's existing temp
            DB::table('disbursement_temp_analysis')
                ->where('Username', $user->ID)
                ->delete();

            // Re-insert into temp from analysis entries
            foreach ($entries as $entry) {
                DB::table('disbursement_temp_analysis')->insert([
                    'AccountNo' => $entry->AccountID,
                    'BL' => $entry->BL,
                    'HouseBL' => $entry->HBL,
                    'ContainerNo' => $entry->ContainerNo,
                    'ConsigneeID' => $entry->ConsigneeID,
                    'Amount' => $entry->Expenditure,
                    'Type' => $entry->Type,
                    'Status' => '2',
                    'Username' => $user->ID,
                    'Time' => now()->toDateTimeString(),
                ]);
            }

            // Delete from disbursement_analysis
            DisbursementAnalysis::where('BL', $bl)->where('HBL', $hbl)->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Disbursement for {$hbl} reopened successfully.",
                'tempRows' => $this->getTempRows($user->ID),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to reopen disbursement. Please try again.',
                'debug' => app()->environment('local') ? $e->getMessage() : null,
            ], 500);
        }
    }

    // ──────────────────────────────────────────────────────────────────────────
    // PRIVATE HELPERS
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Seed temp table with all disbursement_accounts for a BL/HBL session.
     * Skips accounts already in temp to avoid duplicates.
     */
    private function initTempAccounts(
        string $username,
        string $bl,
        string $hbl,
        string $containerNo,
        $consigneeID,
        string $type
    ): void {
        $accounts = DB::table('disbursement_accounts')->pluck('AccountNo');

        // Scope duplicate check to this HBL only
        $existing = DB::table('disbursement_temp_analysis')
            ->where('Username', $username)
            ->where('HouseBL', $hbl)
            ->pluck('AccountNo')
            ->toArray();

        $rows = [];
        foreach ($accounts as $accountNo) {
            if (! in_array($accountNo, $existing)) {
                $rows[] = [
                    'AccountNo'   => $accountNo,
                    'BL'          => $bl,
                    'HouseBL'     => $hbl,
                    'ContainerNo' => $containerNo,
                    'ConsigneeID' => $consigneeID,
                    'Amount'      => 0,
                    'Type'        => $type,
                    'Status'      => '2',
                    'Username'    => $username,
                    'Time'        => now()->toDateTimeString(),
                ];
            }
        }

        if (! empty($rows)) {
            DB::table('disbursement_temp_analysis')->insert($rows);
        }
    }

    /**
     * Get current user's temp rows joined with account names.
     */
    private function getTempRows(string $username): array
    {
        return DB::table('disbursement_temp_analysis as t')
            ->join('ledger_account as la', 't.AccountNo', '=', 'la.AccountNo')
            ->where('t.Username', $username)
            ->orderBy('la.AccountName')
            ->get([
                't.AccountNo',
                'la.AccountName',
                't.Amount',
                't.BL',
                't.HouseBL',
                't.Type',
            ])
            ->toArray();
    }

    /**
     * Get all House BLs under a Main BL with their disbursement status.
     * Status: null = no entry, 2 = pending approval, 0 = approved/locked.
     */
    private function getHBLList(string $bl): \Illuminate\Support\Collection
    {
        return DB::table('manifestation_breakdown as mb')
            ->join('consignee_main as c', 'mb.ConsigneeID', '=', 'c.ConsigneeID')
            ->where('mb.MainBL', $bl)
            ->where('mb.Status', 1)
            ->select(
                'mb.HouseBL',
                'mb.ConsigneeID',
                'mb.ContainerNo',
                'mb.Weight',
                'mb.Description',
                'c.FullName as ConsigneeName',
                DB::raw('(SELECT COUNT(*) FROM disbursement_analysis da WHERE da.BL = mb.MainBL AND da.HBL = mb.HouseBL) as HasDisbursement'),
                DB::raw('(SELECT MAX(da.Status) FROM disbursement_analysis da WHERE da.BL = mb.MainBL AND da.HBL = mb.HouseBL) as DisbursementStatus')
            )
            ->get();
    }

    /**
     * Get total expenditure already saved in disbursement_analysis for a MainBL.
     * Used to calculate variance against budgeted expenses.
     */
    private function getSavedExpenditure(string $bl): float
    {
        return (float) DB::table('disbursement_analysis')
            ->where('BL', $bl)
            ->sum('Expenditure');
    }
}
