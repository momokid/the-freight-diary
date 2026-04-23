<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DisbursementApprovalController extends Controller
{
    // ──────────────────────────────────────────────────────────────────────────
    // PAGE LOAD
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Load all pending In-Harbor disbursements grouped by MainBL.
     * FCL: one card per BL with container details + expenditure rows.
     * LCL: one card per BL with account totals + View HBLs button per account.
     */
    public function index()
    {
        // Get all pending IN-HARBOR entries grouped by BL
        $pendingBLs = DB::table('disbursement_analysis as da')
            ->join('ledger_account as la', 'da.AccountID', '=', 'la.AccountNo')
            ->join('container_main as cm', 'da.BL', '=', 'cm.BL')
            ->join('consignee_main as c', 'cm.ConsigneeID', '=', 'c.ConsigneeID')
            ->where('da.Stamp', 'IN-HARBOR')
            ->where('da.Status', 2)
            ->select(
                'da.BL',
                'da.HBL',
                'da.ContainerNo',
                'da.ReceiptNo',
                'da.AccountID',
                'la.AccountName',
                'da.Expenditure',
                'da.Date',
                'da.Username',
                'da.Type',
                'cm.ConsignmentID',
                'cm.Ownership',
                'c.FullName as ConsigneeName',
                'c.ConsigneeID'
            )
            ->orderBy('da.BL')
            ->orderBy('da.Date')
            ->get();

        // Group by BL
        $grouped = $pendingBLs->groupBy('BL');

        // Build structured data per BL
        $disbursements = [];

        foreach ($grouped as $bl => $entries) {
            $first = $entries->first();
            $type = $first->Type;

            // Get containers for this BL
            $containers = DB::table('container_details')
                ->where('BL', $bl)
                ->get(['ContainerNo', 'ContainerSize', 'Weight', 'HandlingCost']);

            if ($type === 'FCL') {
                $disbursements[] = [
                    'BL' => $bl,
                    'Type' => 'FCL',
                    'ConsigneeID' => $first->ConsigneeID,
                    'ConsigneeName' => $first->ConsigneeName,
                    'ConsignmentID' => $first->ConsignmentID,
                    'containers' => $containers,
                    'entries' => $entries,
                    'receiptNos' => $entries->pluck('ReceiptNo')->unique()->values(),
                    'total' => round($entries->sum('Expenditure'), 2),
                ];
            } else {
                // LCL — group entries by AccountID for totals
                $accountTotals = $entries->groupBy('AccountID')->map(function ($rows) {
                    return [
                        'AccountID' => $rows->first()->AccountID,
                        'AccountName' => $rows->first()->AccountName,
                        'Total' => round($rows->sum('Expenditure'), 2),
                    ];
                })->values();

                $disbursements[] = [
                    'BL' => $bl,
                    'Type' => 'LCL',
                    'ConsigneeID' => $first->ConsigneeID,
                    'ConsigneeName' => $first->ConsigneeName,
                    'ConsignmentID' => $first->ConsignmentID,
                    'containers' => $containers,
                    'accountTotals' => $accountTotals,
                    'entries' => $entries,
                    'receiptNos' => $entries->pluck('ReceiptNo')->unique()->values(),
                    'total' => round($entries->sum('Expenditure'), 2),
                ];
            }
        }

        return view('disbursement.approval', compact('disbursements'));
    }

    // ──────────────────────────────────────────────────────────────────────────
    // GET HBLs (LCL Modal)
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Return HBL breakdown for a specific BL + AccountID.
     * Used to populate the LCL modal.
     */
    public function getHBLs(Request $request)
    {
        $request->validate([
            'BL' => ['required', 'string'],
            'AccountID' => ['required', 'integer'],
        ]);

        $rows = DB::table('disbursement_analysis as da')
            ->join('consignee_main as c', 'da.ConsigneeID', '=', 'c.ConsigneeID')
            ->where('da.BL', $request->BL)
            ->where('da.AccountID', $request->AccountID)
            ->where('da.Stamp', 'IN-HARBOR')
            ->where('da.Status', 2)
            ->select(
                'da.HBL',
                'da.Expenditure',
                'da.Date',
                'da.Username',
                'c.FullName as ConsigneeName'
            )
            ->orderBy('da.HBL')
            ->get();

        return response()->json($rows);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // APPROVE
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Approve all pending entries for a MainBL.
     * Sets Status = 0 on all matching disbursement_analysis rows.
     */
    public function approve(Request $request)
    {
        $request->validate([
            'BL' => ['required', 'string', 'max:50'],
        ]);

        $bl = strtoupper(trim($request->BL));
        $user = Auth::user();

        $affected = DB::table('disbursement_analysis')
            ->where('BL', $bl)
            ->where('Stamp', 'IN-HARBOR')
            ->where('Status', 2)
            ->update([
                'Status' => 0,
            ]);

        if ($affected === 0) {
            return response()->json([
                'success' => false,
                'message' => "No pending entries found for BL# {$bl}.",
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => "BL# {$bl} approved successfully.",
            'BL' => $bl,
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // DECLINE
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Decline all pending entries for a MainBL.
     * Reverses all accounting entries and restores to temp table.
     * Multiple ReceiptNos handled — each may have a different date.
     */
    public function decline(Request $request)
    {
        $request->validate([
            'BL' => ['required', 'string', 'max:50'],
        ]);

        $bl = strtoupper(trim($request->BL));
        $user = Auth::user();

        // Get all pending entries for this BL
        $entries = DB::table('disbursement_analysis')
            ->where('BL', $bl)
            ->where('Stamp', 'IN-HARBOR')
            ->where('Status', 2)
            ->get();

        if ($entries->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => "No pending entries found for BL# {$bl}.",
            ], 404);
        }

        // ── Check if any username on this BL has an active temp session ──────────
        // If the user who submitted this disbursement is currently working on
        // another session, we cannot restore to temp — it would create a conflict.
        $submitterUsernames = $entries->pluck('Username')->unique()->values()->toArray();

        $blockedUser = DB::table('disbursement_temp_analysis')
            ->whereIn('Username', $submitterUsernames)
            ->first();

        if ($blockedUser) {
            return response()->json([
                'success' => false,
                'message' => "User '{$blockedUser->Username}' is currently working on an unsaved disbursement for BL# {$blockedUser->BL}. Ask them to complete or clear it before this transaction can be declined.",
            ], 409);
        }

        // Collect all distinct ReceiptNos
        $receiptNos = $entries->pluck('ReceiptNo')->unique()->values()->toArray();

        DB::beginTransaction();

        try {
            // 1. Reverse journal entries for all ReceiptNos
            DB::table('journal')
                ->whereIn('ReceiptNo', $receiptNos)
                ->delete();

            // 2. Reverse pnl_transaction entries for all ReceiptNos
            DB::table('pnl_transaction')
                ->whereIn('ReceiptNo', $receiptNos)
                ->delete();

            // 3. Delete from receipt_main for all ReceiptNos
            DB::table('receipt_main')
                ->whereIn('ReceiptNo', $receiptNos)
                ->delete();

            // 4. Clear existing temp rows for this user (safety)
            DB::table('disbursement_temp_analysis')
                ->where('Username', $user->ID)
                ->where('BL', $bl)
                ->delete();

            // 5. Re-insert into disbursement_temp_analysis
            $tempRows = [];
            foreach ($entries as $entry) {
                $tempRows[] = [
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
                ];
            }

            DB::table('disbursement_temp_analysis')->insert($tempRows);

            // 6. Delete from disbursement_analysis
            DB::table('disbursement_analysis')
                ->where('BL', $bl)
                ->where('Stamp', 'IN-HARBOR')
                ->where('Status', 2)
                ->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "BL# {$bl} declined and restored to draft.",
                'BL' => $bl,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to decline transaction. Please try again.',
                'debug' => app()->environment('local') ? $e->getMessage() : null,
            ], 500);
        }
    }

    // ──────────────────────────────────────────────────────────────────────────
    // APPROVE ALL
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Approve all pending IN-HARBOR disbursements across all BLs.
     */
    public function approveAll()
    {
        $affected = DB::table('disbursement_analysis')
            ->where('Stamp', 'IN-HARBOR')
            ->where('Status', 2)
            ->update(['Status' => 0]);

        return response()->json([
            'success' => true,
            'message' => "{$affected} entries approved successfully.",
            'affected' => $affected,
        ]);
    }
}
