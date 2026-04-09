<?php

namespace App\Http\Controllers;

use App\Models\ManifestBreakdown;
use App\Services\ReceiptService;
use App\Services\TaxService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HblInvoiceController extends Controller
{
    public function index()
    {
        \Illuminate\Support\Facades\Log::info('HblInvoiceController@index called');
        $user = Auth::user();

        // Load income accounts for additional charges
        $incomeAccounts = DB::table('ledger_account')
            ->where('Type', 'INCOME')
            ->where('Status', 1)
            ->orderBy('AccountName')
            ->get(['AccountNo', 'AccountName']);

        // Load handling charges configured under Basic Setup
        $handlingCharges = DB::table('handling_charge as hc')
            ->join('ledger_account as la', 'hc.AccountNo', '=', 'la.AccountNo')
            ->orderBy('hc.POrder')
            ->get(['hc.AccountNo', 'la.AccountName', 'hc.Amount']);

        // Load any pending staged entries for this user
        $pendingEntries = DB::table('hbl_invoice_consignee_temp as t')
            ->join('ledger_account as la', 't.AccountNo', '=', 'la.AccountNo')
            ->where('t.Username', $user->ID)
            ->get();

        $pendingHouseBL = $pendingEntries->first()?->HouseBL;

        // Tax components for JS calculation
        $taxComponents = TaxService::componentsForJS();

        // Generate receipt number
        $receipt = ReceiptService::generate(now()->toDateString());

        // TEMP DEBUG — remove after testing
        Log::info('Income accounts count: ' . $incomeAccounts->count());
        Log::info('First account: ' . json_encode($incomeAccounts->first()));

        return view('invoices.house-bl', compact(
            'incomeAccounts',
            'handlingCharges',
            'pendingEntries',
            'pendingHouseBL',
            'taxComponents',
            'receipt'
        ));
    }

    // Search consignee by name or House BL
    public function search(Request $request)
    {
        $request->validate(['q' => ['required', 'string', 'min:2']]);

        $q = trim($request->q);

        $results = DB::table('manifestation_breakdown as mb')
            ->join('consignee_main as c', 'mb.ConsigneeID', '=', 'c.ConsigneeID')
            ->join('container_main as cm', 'mb.ConsignmentID', '=', 'cm.ConsignmentID')
            ->where('mb.Status', 1)
            ->where(function ($query) use ($q) {
                $query->where('c.FullName', 'like', "%{$q}%")
                    ->orWhere('mb.HouseBL', 'like', "%{$q}%");
            })
            ->select(
                'mb.ConsignmentID',
                'mb.MainBL',
                'mb.HouseBL',
                'mb.ConsigneeID',
                'c.FullName',
                'c.Address1',
                'mb.Description',
                'mb.Weight',
                'mb.Package',
                'mb.Unit',
                'cm.ETA',
            )
            ->limit(10)
            ->get();

        return response()->json($results);
    }

    // Add charge to staging
    public function addCharge(Request $request)
    {
        $request->validate([
            'ConsignmentID' => ['required', 'integer'],
            'MainBL'        => ['required', 'string'],
            'HouseBL'       => ['required', 'string'],
            'ConsigneeID'   => ['required', 'integer'],
            'AccountNo'     => ['required', 'integer'],
            'Amount'        => ['required', 'numeric', 'min:0.01'],
            'Taxable'       => ['required', 'boolean'],
        ]);

        $user = Auth::user();

        // Check user doesn't have entries under a different HouseBL
        $existingHBL = DB::table('hbl_invoice_consignee_temp')
            ->where('Username', $user->ID)
            ->value('HouseBL');

        if ($existingHBL && $existingHBL !== $request->HouseBL) {
            return response()->json([
                'success' => false,
                'message' => "You have pending charges for HBL# {$existingHBL}. Submit or clear them first.",
            ], 409);
        }

        // Check invoice doesn't already exist for this HouseBL
        $invoiceExists = DB::table('hbl_invoice')
            ->where('HouseBL', $request->HouseBL)
            ->where('Status', 1)
            ->exists();

        if ($invoiceExists) {
            return response()->json([
                'success' => false,
                'message' => "An invoice already exists for HBL# {$request->HouseBL}.",
            ], 409);
        }

        // Calculate tax
        $tax = TaxService::calculate((float) $request->Amount, (bool) $request->Taxable);

        // Find existing entry for this account — update if exists
        $existing = DB::table('hbl_invoice_consignee_temp')
            ->where('Username', $user->ID)
            ->where('HouseBL', $request->HouseBL)
            ->where('AccountNo', $request->AccountNo)
            ->first();

        // Extract tax line amounts
        $getfundNHIL = 0;
        $covid       = 0;
        $vat         = 0;

        foreach ($tax['lines'] as $line) {
            if (in_array($line['name'], ['GetFund', 'NHIL'])) {
                $getfundNHIL += $line['tax'];
            } elseif ($line['name'] === 'Covid') {
                $covid += $line['tax'];
            } elseif ($line['name'] === 'VAT') {
                $vat = $line['tax'];
            }
        }

        $data = [
            'ConsignmentID' => $request->ConsignmentID,
            'MainBL'        => strtoupper($request->MainBL),
            'HouseBL'       => strtoupper($request->HouseBL),
            'ConsigneeID'   => $request->ConsigneeID,
            'AccountNo'     => $request->AccountNo,
            'GetFundNHIL'   => round($getfundNHIL, 2),
            'Covid'         => round($covid, 2),
            'VAT'           => round($vat, 2),
            'Amount'        => round((float) $request->Amount, 2),
            'Date'          => now()->toDateString(),
            'Time'          => now()->toDateTimeString(),
            'Username'      => $user->ID,
        ];

        if ($existing) {
            DB::table('hbl_invoice_consignee_temp')
                ->where('Username', $user->ID)
                ->where('HouseBL', $request->HouseBL)
                ->where('AccountNo', $request->AccountNo)
                ->update($data);
        } else {
            DB::table('hbl_invoice_consignee_temp')->insert($data);
        }

        $entries = $this->getStagedEntries($user->ID, $request->HouseBL);

        return response()->json([
            'success' => true,
            'message' => 'Charge added to staging.',
            'entries' => $entries,
            'tax'     => $tax,
        ]);
    }

    // Remove charge from staging
    public function removeCharge(Request $request)
    {
        $request->validate([
            'HouseBL'   => ['required', 'string'],
            'AccountNo' => ['required', 'integer'],
        ]);

        DB::table('hbl_invoice_consignee_temp')
            ->where('Username', Auth::user()->ID)
            ->where('HouseBL', $request->HouseBL)
            ->where('AccountNo', $request->AccountNo)
            ->delete();

        $entries = $this->getStagedEntries(Auth::user()->ID, $request->HouseBL);

        return response()->json([
            'success' => true,
            'entries' => $entries,
        ]);
    }

    // Clear all staged charges
    public function clearCharges()
    {
        DB::table('hbl_invoice_consignee_temp')
            ->where('Username', Auth::user()->ID)
            ->delete();

        return response()->json(['success' => true]);
    }

    // Save invoice
    public function store(Request $request)
    {
        $request->validate([
            'ReceiptID' => ['required', 'string'],
            'ReceiptNo' => ['required', 'string'],
            'HouseBL'   => ['required', 'string'],
        ]);

        $user = Auth::user();
        $hbl  = strtoupper(trim($request->HouseBL));

        // Get staged entries
        $entries = DB::table('hbl_invoice_consignee_temp')
            ->where('Username', $user->ID)
            ->where('HouseBL', $hbl)
            ->where('Amount', '>', 0)
            ->get();

        if ($entries->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No charges found. Please add at least one charge.',
            ], 422);
        }

        // Check only one HouseBL in staging
        $distinctHBLs = DB::table('hbl_invoice_consignee_temp')
            ->where('Username', $user->ID)
            ->distinct()
            ->pluck('HouseBL');

        if ($distinctHBLs->count() > 1) {
            return response()->json([
                'success' => false,
                'message' => 'Multiple House BLs detected. Please reset.',
            ], 422);
        }

        // Check receipt not duplicate
        $receiptExists = DB::table('receipt_main')
            ->where('ReceiptNo', $request->ReceiptNo)
            ->exists();

        if ($receiptExists) {
            return response()->json([
                'success' => false,
                'message' => 'Receipt number already exists. Please refresh and try again.',
            ], 409);
        }

        // Check invoice not already generated
        $invoiceExists = DB::table('hbl_invoice')
            ->where('HouseBL', $hbl)
            ->where('Status', 1)
            ->exists();

        if ($invoiceExists) {
            return response()->json([
                'success' => false,
                'message' => "An invoice already exists for HBL# {$hbl}.",
            ], 409);
        }

        // Get customer control account
        $stc = DB::table('active_account_receivable')
            ->value('AccountNo');

        if (!$stc) {
            return response()->json([
                'success' => false,
                'message' => 'Customer Control Account not configured. Please set it up in Basic Setup.',
            ], 422);
        }

        $first = $entries->first();

        DB::beginTransaction();

        try {
            // Insert receipt_main
            DB::table('receipt_main')->insert([
                'ID'        => $request->ReceiptID,
                'Date'      => now()->toDateString(),
                'ReceiptNo' => $request->ReceiptNo,
                'Username'  => $user->ID,
                'Time'      => now()->toDateTimeString(),
            ]);

            foreach ($entries as $entry) {
                // Get account name for description
                $accountName = DB::table('ledger_account')
                    ->where('AccountNo', $entry->AccountNo)
                    ->value('AccountName');

                // Insert hbl_invoice
                DB::table('hbl_invoice')->insert([
                    'ConsignmentID' => $entry->ConsignmentID,
                    'MainBL'        => $entry->MainBL,
                    'HouseBL'       => $entry->HouseBL,
                    'ConsigneeID'   => $entry->ConsigneeID,
                    'ReceiptNo'     => $request->ReceiptNo,
                    'AccountNo'     => $entry->AccountNo,
                    'Fee'           => $entry->Amount,
                    'GetFundNHIL'   => $entry->GetFundNHIL,
                    'VAT'           => $entry->VAT,
                    'Date'          => now()->toDateString(),
                    'Time'          => now()->toDateTimeString(),
                    'Username'      => $user->ID,
                    'Status'        => 1,
                ]);

                // Insert student_fee (receivables ledger)
                $subtotal = $entry->Amount + $entry->GetFundNHIL + $entry->Covid + $entry->VAT;

                DB::table('student_fee')->insert([
                    'StudentID'  => $entry->ConsigneeID,
                    'SubClassID' => $entry->HouseBL,
                    'CouponID'   => $entry->MainBL,
                    'AccountNo'  => $entry->AccountNo,
                    'Stamp'      => 'BL',
                    'Description' => "Cost of {$accountName} ifo {$entry->HouseBL}",
                    'ReceiptNo'  => $request->ReceiptNo,
                    'Dr'         => round($subtotal, 2),
                    'Cr'         => 0,
                    'Date'       => now()->toDateString(),
                    'Time'       => now()->toDateTimeString(),
                    'Username'   => $user->ID,
                    'Status'     => 1,
                ]);
            }

            // Clear staging
            DB::table('hbl_invoice_consignee_temp')
                ->where('Username', $user->ID)
                ->delete();

            DB::commit();

            return response()->json([
                'success'   => true,
                'message'   => "Invoice saved successfully for HBL# {$hbl}.",
                'HouseBL'   => $hbl,
                'ReceiptNo' => $request->ReceiptNo,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to save invoice. Please try again.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // Get staged entries helper
    private function getStagedEntries(string $username, string $houseBL): \Illuminate\Support\Collection
    {
        return DB::table('hbl_invoice_consignee_temp as t')
            ->join('ledger_account as la', 't.AccountNo', '=', 'la.AccountNo')
            ->where('t.Username', $username)
            ->where('t.HouseBL', $houseBL)
            ->select(
                't.*',
                'la.AccountName',
                DB::raw('(t.Amount + t.GetFundNHIL + t.Covid + t.VAT) as SubTotal')
            )
            ->get();
    }

    // Invoice report
    public function report(string $hbl)
    {
        $hbl = strtoupper($hbl);

        $entries = DB::table('hbl_invoice as i')
            ->join('ledger_account as la', 'i.AccountNo', '=', 'la.AccountNo')
            ->join('consignee_main as c', 'i.ConsigneeID', '=', 'c.ConsigneeID')
            ->where('i.HouseBL', $hbl)
            ->where('i.Status', 1)
            ->select(
                'i.*',
                'la.AccountName',
                'c.FullName',
                'c.Address1',
                'c.Address2',
                'c.Address3',
                DB::raw('(i.Fee + i.GetFundNHIL + i.VAT) as SubTotal')
            )
            ->get();

        if ($entries->isEmpty()) {
            abort(404, 'Invoice not found for HBL# ' . $hbl);
        }

        $first = $entries->first();

        // Get manifest details
        $manifest = DB::table('manifestation_breakdown as mb')
            ->join('container_main as cm', 'mb.ConsignmentID', '=', 'cm.ConsignmentID')
            ->join('pol', 'cm.POL_ID', '=', 'pol.POL_ID')
            ->join('pod', 'cm.POD_ID', '=', 'pod.POD_ID')
            ->where('mb.HouseBL', $hbl)
            ->select(
                'mb.*',
                'cm.VesselName',
                'cm.ETA',
                'cm.BL as MainBL',
                'pol.POL_Name',
                'pod.POD_Name',
            )
            ->first();

        // Tax components for breakdown display
        $taxComponents = TaxService::componentsForJS();

        // Get bank details
        $bankDetails = DB::table('bank_details')->where('is_active', 1)->first();

        return view('invoices.house-bl-report', compact(
            'entries',
            'first',
            'manifest',
            'taxComponents',
            'hbl',
            'bankDetails'
        ));
    }
}
