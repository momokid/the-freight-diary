<?php

namespace App\Http\Controllers;

use App\Services\ReceiptService;
use App\Services\TaxService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OtherInvoiceController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $incomeAccounts = DB::select(
            "SELECT AccountNo, AccountName FROM ledger_account WHERE Type = 'INCOME' AND Status = 1 ORDER BY AccountName"
        );
        $incomeAccounts = collect($incomeAccounts);

        $pendingEntries = DB::table('temp_other_invoice as t')
            ->join('ledger_account as la', 't.AccountNo', '=', 'la.AccountNo')
            ->where('t.Username', $user->ID)
            ->select('t.*', 'la.AccountName',
                DB::raw('(t.Amount + t.GetFund + t.VAT) as SubTotal'))
            ->get();

        $pendingClientID = $pendingEntries->first()?->ClientID;
        $pendingClient   = null;

        if ($pendingClientID) {
            $pendingClient = DB::table('consignee_main')
                ->where('ConsigneeID', $pendingClientID)
                ->first(['ConsigneeID', 'FullName', 'TelNo', 'Address1', 'Address2', 'Address3']);
        }

        $receipt       = ReceiptService::generate(now()->toDateString());
        $taxComponents = TaxService::componentsForJS();

        return view('invoices.other-invoice', compact(
            'incomeAccounts',
            'pendingEntries',
            'pendingClientID',
            'pendingClient',
            'receipt',
            'taxComponents'
        ));
    }

    public function searchClient(Request $request)
    {
        $q = trim($request->q);

        $results = DB::table('consignee_main')
            ->where('Status', 1)
            ->where(function ($query) use ($q) {
                $query->where('FullName', 'like', "%{$q}%")
                      ->orWhere('TelNo', 'like', "%{$q}%");
            })
            ->orderBy('FullName')
            ->limit(10)
            ->get(['ConsigneeID', 'FullName', 'TelNo', 'Address1', 'Address2', 'Address3']);

        return response()->json($results);
    }

    public function addCharge(Request $request)
    {
        $request->validate([
            'ClientID'    => ['required'],
            'AccountNo'   => ['required', 'integer'],
            'Amount'      => ['required', 'numeric', 'min:0.01'],
            'Description' => ['required', 'string'],
        ]);

        $user = Auth::user();

        // Check user doesn't have entries for a different client
        $existingClient = DB::table('temp_other_invoice')
            ->where('Username', $user->ID)
            ->value('ClientID');

        if ($existingClient && $existingClient != $request->ClientID) {
            return response()->json([
                'success' => false,
                'message' => "You have pending charges for another client. Submit or clear them first.",
            ], 409);
        }

        // Tax is always applied for Other Serv. Invoice
        $tax = TaxService::calculate((float) $request->Amount, true);

        $getFund = 0;
        $vat     = 0;

        foreach ($tax['lines'] as $line) {
            if (in_array($line['name'], ['GetFund', 'NHIL'])) {
                $getFund += $line['tax'];
            } elseif ($line['name'] === 'VAT') {
                $vat = $line['tax'];
            }
        }

        // Update if same account exists for this client
        $existing = DB::table('temp_other_invoice')
            ->where('Username', $user->ID)
            ->where('ClientID', $request->ClientID)
            ->where('AccountNo', $request->AccountNo)
            ->first();

        $data = [
            'ClientID'    => $request->ClientID,
            'AccountNo'   => $request->AccountNo,
            'Amount'      => round((float) $request->Amount, 2),
            'Description' => trim($request->Description),
            'GetFund'     => round($getFund, 2),
            'VAT'         => round($vat, 2),
            'Username'    => $user->ID,
            'Time'        => now()->toDateTimeString(),
        ];

        if ($existing) {
            DB::table('temp_other_invoice')
                ->where('Username', $user->ID)
                ->where('ClientID', $request->ClientID)
                ->where('AccountNo', $request->AccountNo)
                ->update($data);
        } else {
            DB::table('temp_other_invoice')->insert($data);
        }

        $entries = $this->getStagedEntries($user->ID);

        return response()->json([
            'success' => true,
            'entries' => $entries,
        ]);
    }

    public function removeCharge(Request $request)
    {
        $request->validate([
            'AccountNo' => ['required', 'integer'],
        ]);

        DB::table('temp_other_invoice')
            ->where('Username', Auth::user()->ID)
            ->where('AccountNo', $request->AccountNo)
            ->delete();

        return response()->json([
            'success' => true,
            'entries' => $this->getStagedEntries(Auth::user()->ID),
        ]);
    }

    public function clearCharges()
    {
        DB::table('temp_other_invoice')
            ->where('Username', Auth::user()->ID)
            ->delete();

        return response()->json(['success' => true]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'ReceiptID' => ['required', 'string'],
            'ReceiptNo' => ['required', 'string'],
            'ClientID'  => ['required'],
            'DOT'       => ['required', 'date'],
            'MainBL'    => ['nullable', 'string'],
            'HouseBL'   => ['nullable', 'string'],
            'Description' => ['required', 'string'],
        ]);

        $user = Auth::user();

        $entries = $this->getStagedEntries($user->ID);

        if ($entries->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No charges found. Please add at least one charge.',
            ], 422);
        }

        // Check receipt not duplicate
        $receiptExists = DB::table('receipt_main')
            ->where('ReceiptNo', $request->ReceiptNo)
            ->exists();

        if ($receiptExists) {
            return response()->json([
                'success' => false,
                'message' => 'Receipt number already exists. Please refresh.',
            ], 409);
        }

        // Get receivable account
        $stc = DB::table('active_account_receivable')->value('AccountNo');
        if (!$stc) {
            return response()->json([
                'success' => false,
                'message' => 'Receivable account not configured. Set it up in Basic Setup.',
            ], 422);
        }

        DB::beginTransaction();

        try {
            DB::table('receipt_main')->insert([
                'ID'        => $request->ReceiptID,
                'Date'      => $request->DOT,
                'ReceiptNo' => $request->ReceiptNo,
                'Username'  => $user->ID,
                'Time'      => now()->toDateTimeString(),
            ]);

            foreach ($entries as $entry) {
                $subTotal = $entry->Amount + $entry->GetFund + $entry->VAT;

                DB::table('other_invoice')->insert([
                    'ClientID'    => $request->ClientID,
                    'Description' => trim($request->Description),
                    'MainBL'      => $request->MainBL ?? '',
                    'HouseBL'     => $request->HouseBL ?? '',
                    'Schedules'   => $request->ReceiptNo,
                    'AccountNo'   => $entry->AccountNo,
                    'Stamp'       => 'BILL',
                    'ReceiptNo'   => $request->ReceiptNo,
                    'Amount'      => $entry->Amount,
                    'GetFundNHIL' => $entry->GetFund,
                    'VAT'         => $entry->VAT,
                    'Date'        => $request->DOT,
                    'Time'        => now()->toDateTimeString(),
                    'Username'    => $user->ID,
                    'Status'      => 2,
                ]);

                DB::table('student_fee')->insert([
                    'StudentID'   => $request->ClientID,
                    'SubClassID'  => $request->MainBL ?? '',
                    'CouponID'    => $request->ReceiptNo,
                    'AccountNo'   => $entry->AccountNo,
                    'Stamp'       => 'BL_NONBL',
                    'Description' => $entry->Description,
                    'ReceiptNo'   => $request->ReceiptNo,
                    'Dr'          => round($subTotal, 2),
                    'Cr'          => 0,
                    'Date'        => $request->DOT,
                    'Time'        => now()->toDateTimeString(),
                    'Username'    => $user->ID,
                    'Status'      => 1,
                ]);
            }

            DB::table('temp_other_invoice')
                ->where('Username', $user->ID)
                ->delete();

            DB::commit();

            return response()->json([
                'success'   => true,
                'message'   => 'Invoice saved successfully.',
                'ReceiptNo' => $request->ReceiptNo,
                'ClientID'  => $request->ClientID,
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

    public function report(string $receiptNo)
    {
        $entries = DB::table('other_invoice as i')
            ->join('ledger_account as la', 'i.AccountNo', '=', 'la.AccountNo')
            ->join('consignee_main as c', 'i.ClientID', '=', 'c.ConsigneeID')
            ->where('i.ReceiptNo', $receiptNo)
            ->select(
                'i.*',
                'la.AccountName',
                'c.FullName',
                'c.Address1',
                'c.Address2',
                'c.Address3',
                DB::raw('(i.Amount + i.GetFundNHIL + i.VAT) as SubTotal')
            )
            ->get();

        if ($entries->isEmpty()) {
            abort(404, 'Invoice not found for receipt# ' . $receiptNo);
        }

        $first         = $entries->first();
        $taxComponents = TaxService::componentsForJS();
        $bankDetails   = DB::table('bank_details')->where('is_active', 1)->first();

        // Get TIN from inst_reg
        $tin = DB::table('inst_reg')->value('Initial');

        return view('invoices.other-invoice-report', compact(
            'entries',
            'first',
            'taxComponents',
            'bankDetails',
            'receiptNo',
            'tin'
        ));
    }

    private function getStagedEntries(string $username): \Illuminate\Support\Collection
    {
        return DB::table('temp_other_invoice as t')
            ->join('ledger_account as la', 't.AccountNo', '=', 'la.AccountNo')
            ->where('t.Username', $username)
            ->select(
                't.*',
                'la.AccountName',
                DB::raw('(t.Amount + t.GetFund + t.VAT) as SubTotal')
            )
            ->get();
    }
}