<?php

namespace App\Http\Controllers\EditData;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReverseTransactionController extends Controller
{
    public function index()
    {
        return view('edit-data.reverse-transaction');
    }

    // ── Reverse Consignment ──────────────────────────────────────────────────

    public function loadConsignment(Request $request)
    {
        $request->validate(['BL' => ['required', 'string', 'max:50']]);

        $bl = strtoupper(trim($request->BL));

        $consignment = DB::table('container_main as cm')
            ->leftJoin('ship_carrier as c', 'cm.CarrierID', '=', 'c.CarrierID')
            ->leftJoin('consignee_main as cn', 'cm.ConsigneeID', '=', 'cn.ConsigneeID')
            ->where('cm.BL', $bl)
            ->whereNotIn('cm.Status', [9])
            ->first([
                'cm.ConsignmentID',
                'cm.BL',
                'cm.Date',
                'cm.ContainerNo',
                'cm.ContainerSize',
                'cm.Status',
                'c.CarrierName',
                'cn.FullName as ConsigneeName',
            ]);

        if (! $consignment) {
            return response()->json([
                'success' => false,
                'message' => 'Consignment not found or already reversed for BL# '.$bl,
            ], 404);
        }

        // Guard checks
        $guard = $this->checkConsignmentGuards($bl);
        if ($guard) {
            return response()->json($guard, 409);
        }

        // Get container count
        $containerCount = DB::table('container_details')
            ->where('ConsignmentID', $consignment->ConsignmentID)
            ->where('Status', 1)
            ->count();

        // Get HBL count
        $hblCount = DB::table('manifestation_breakdown')
            ->where('ConsignmentID', $consignment->ConsignmentID)
            ->where('Status', 1)
            ->count();

        return response()->json([
            'success' => true,
            'consignment' => $consignment,
            'containerCount' => $containerCount,
            'hblCount' => $hblCount,
        ]);
    }

    public function reverseConsignment(Request $request)
    {
        $request->validate([
            'ConsignmentID' => ['required', 'integer'],
            'BL' => ['required', 'string', 'max:50'],
        ]);

        $bl = strtoupper(trim($request->BL));

        // Guard checks
        $guard = $this->checkConsignmentGuards($bl);
        if ($guard) {
            return response()->json($guard, 409);
        }

        DB::beginTransaction();

        try {
            // Soft delete — Status = 9
            DB::table('container_main')
                ->where('ConsignmentID', $request->ConsignmentID)
                ->update(['Status' => 9]);

            DB::table('container_details')
                ->where('ConsignmentID', $request->ConsignmentID)
                ->update(['Status' => 9]);

            DB::table('manifestation_breakdown')
                ->where('ConsignmentID', $request->ConsignmentID)
                ->update(['Status' => 9]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Consignment BL# '.$bl.' has been reversed successfully.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to reverse consignment. Please try again.',
                'debug' => app()->environment('local') ? $e->getMessage() : null,
            ], 500);
        }
    }

    // ── Reverse Cargo Manifest ───────────────────────────────────────────────

    public function loadManifest(Request $request)
    {
        $request->validate(['BL' => ['required', 'string', 'max:50']]);

        $bl = strtoupper(trim($request->BL));

        // Verify consignment exists
        $consignment = DB::table('container_main')
            ->where('BL', $bl)
            ->whereNotIn('Status', [9])
            ->first(['ConsignmentID', 'BL', 'Date']);

        if (! $consignment) {
            return response()->json([
                'success' => false,
                'message' => 'Consignment not found for BL# '.$bl,
            ], 404);
        }

        $rows = DB::table('manifestation_breakdown as mb')
            ->join('consignee_main as c', 'mb.ConsigneeID', '=', 'c.ConsigneeID')
            ->where('mb.ConsignmentID', $consignment->ConsignmentID)
            ->where('mb.Status', 1)
            ->orderBy('mb.HouseBL')
            ->get([
                'mb.HouseBL',
                'c.FullName as ConsigneeName',
                'mb.Description',
                'mb.Weight',
                'mb.Package',
                'mb.Unit',
            ]);

        if ($rows->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No manifest entries found for BL# '.$bl,
            ], 404);
        }

        // Guard: block if any receipt exists for any HBL under this BL
        $hbls = $rows->pluck('HouseBL')->toArray();

        $guard = $this->checkManifestGuards($hbls);
        if ($guard) {
            return response()->json($guard, 409);
        }

        return response()->json([
            'success' => true,
            'consignment' => $consignment,
            'rows' => $rows,
        ]);
    }

    public function reverseManifest(Request $request)
    {
        $request->validate([
            'ConsignmentID' => ['required', 'integer'],
            'BL' => ['required', 'string', 'max:50'],
        ]);

        // Re-run guard check
        $hbls = DB::table('manifestation_breakdown')
            ->where('ConsignmentID', $request->ConsignmentID)
            ->where('Status', 1)
            ->pluck('HouseBL')
            ->toArray();

        if (empty($hbls)) {
            return response()->json([
                'success' => false,
                'message' => 'No manifest entries found.',
            ], 404);
        }

        // Guard: block if any receipt exists for any HBL under this BL
        $guard = $this->checkManifestGuards($hbls);
        if ($guard) {
            return response()->json($guard, 409);
        }

        DB::beginTransaction();

        try {
            DB::table('manifestation_breakdown')
                ->where('ConsignmentID', $request->ConsignmentID)
                ->where('Status', 1)
                ->update(['Status' => 9]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cargo manifest for BL# '.strtoupper(trim($request->BL)).' has been reversed successfully.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to reverse manifest. Please try again.',
                'debug' => app()->environment('local') ? $e->getMessage() : null,
            ], 500);
        }
    }

    // ── Reverse User Transaction ─────────────────────────────────────────────

    public function loadTransaction(Request $request)
    {
        $request->validate(['ReceiptNo' => ['required', 'string', 'max:30']]);

        $receiptNo = strtoupper(trim($request->ReceiptNo));

        $receipt = DB::table('receipt_main')
            ->where('ReceiptNo', $receiptNo)
            ->first();

        if (! $receipt) {
            return response()->json([
                'success' => false,
                'message' => 'Receipt not found: '.$receiptNo,
            ], 404);
        }

        // Get journal entries for this receipt
        $entries = DB::table('journal as j')
            ->leftJoin('ledger_account as la', 'j.AccountID', '=', 'la.AccountNo')
            ->leftJoin('ledger_account as sa', 'j.SubAccountID', '=', 'sa.AccountNo')
            ->where('j.ReceiptNo', $receiptNo)
            ->where('j.Reversed', 0)
            ->get([
                'j.AccountID',
                'la.AccountName',
                'sa.AccountName as SubAccountName',
                'j.Dr',
                'j.Cr',
                'j.Description',
                'j.Date',
            ]);

        if ($entries->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No active journal entries found for receipt: '.$receiptNo,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'receipt' => $receipt,
            'entries' => $entries,
        ]);
    }

    public function reverseTransaction(Request $request)
    {
        $request->validate([
            'ReceiptNo' => ['required', 'string', 'max:30'],
        ]);

        $receiptNo = strtoupper(trim($request->ReceiptNo));

        // Verify receipt exists
        $exists = DB::table('receipt_main')
            ->where('ReceiptNo', $receiptNo)
            ->exists();

        if (! $exists) {
            return response()->json([
                'success' => false,
                'message' => 'Receipt not found: '.$receiptNo,
            ], 404);
        }

        DB::beginTransaction();

        try {
            // Soft delete — set Reversed = 1
            DB::table('journal')
                ->where('ReceiptNo', $receiptNo)
                ->update([
                    'Reversed' => 1,
                    'ReversedBy' => Auth::user()->ID,
                    'ReversedAt' => now()->toDateTimeString(),
                ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaction '.$receiptNo.' has been reversed successfully.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to reverse transaction. Please try again.',
                'debug' => app()->environment('local') ? $e->getMessage() : null,
            ], 500);
        }
    }

    // Guard check for consignment reversal — used by loadConsignment and reverseConsignment
    private function checkConsignmentGuards(string $bl): ?array
    {
        $disbursementReceiptNos = DB::table('disbursement_analysis')
            ->where('BL', $bl)
            ->pluck('ReceiptNo')
            ->unique()
            ->filter()
            ->values()
            ->toArray();

        if (! empty($disbursementReceiptNos)) {
            $hasUnreversedJournal = DB::table('journal')
                ->whereIn('ReceiptNo', $disbursementReceiptNos)
                ->where('Reversed', 0)
                ->exists();

            if ($hasUnreversedJournal) {
                return [
                    'success' => false,
                    'message' => 'Cannot reverse — this consignment has disbursement transactions that must be reversed first. Receipt(s): '.implode(', ', $disbursementReceiptNos),
                    'blockingReceipts' => $disbursementReceiptNos,
                ];
            }
        }

        return null; // null means all clear
    }

    // Guard check for manifest reversal
    private function checkManifestGuards(array $hbls): ?array
    {
        $hasInvoices = DB::table('hbl_invoice')
            ->whereIn('HouseBL', $hbls)
            ->where('Status', 1)
            ->exists();

        if ($hasInvoices) {
            return [
                'success' => false,
                'message' => 'Cannot reverse — HBL invoices exist for one or more House BLs under this consignment.',
            ];
        }

        return null;
    }
}
