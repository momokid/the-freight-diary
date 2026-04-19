<?php

namespace App\Http\Controllers;

use App\Services\ReceiptService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AccountingController extends Controller
{
    // ──────────────────────────────────────────────
    // ACCOUNTING TRANSACTION
    // ──────────────────────────────────────────────

    /**
     * Show the Accounting Transaction form.
     */
    public function transaction()
    {
        $user = Auth::user();

        // GL accounts
        $glAccounts = DB::table('ledger_account')
            ->where('Type', 'GL')
            ->where('Status', 1)
            ->orderBy('AccountName')
            ->get(['AccountNo', 'AccountName']);

        // Income accounts
        $incomeAccounts = DB::table('ledger_account')
            ->where('Type', 'INCOME')
            ->where('Status', 1)
            ->orderBy('AccountName')
            ->get(['AccountNo', 'AccountName']);

        // Expenditure accounts
        $expenditureAccounts = DB::table('ledger_account')
            ->where('Type', 'Expenditure')
            ->where('Status', 1)
            ->orderBy('AccountName')
            ->get(['AccountNo', 'AccountName']);

        $receipt = ReceiptService::generate(now()->toDateString());

        return view('accounting.transaction', compact(
            'glAccounts',
            'incomeAccounts',
            'expenditureAccounts',
            'receipt'
        ));
    }

    /**
     * Save the accounting transaction.
     *
     * Transaction types and their journal/pnl rules:
     *
     * GL_SINGLE       — 1 journal line (Dr or Cr), no pnl
     * GL_DOUBLE       — 2 journal lines (Dr + Cr), no pnl
     * DR_GL_CR_INC    — Dr GL + Cr IE→Income,    pnl Cr 'DrGLCrInc'
     * CR_GL_DR_EXP    — Cr GL + Dr IE→Expense,   pnl Dr 'DrExpCrGL'
     * CR_GL_DR_INC    — Cr GL + Dr IE→Income,    pnl Dr 'CrGLDrInc'
     * DR_GL_CR_EXP    — Dr GL + Cr IE→Expense,   pnl Cr 'CrExpDrGL'  (wait - confirm)
     * SINGLE_DR_INC   — 1 journal Dr IE→Income,  pnl Dr 'SDrInc'
     * SINGLE_CR_INC   — 1 journal Cr IE→Income,  pnl Cr 'SCrInc'
     * SINGLE_DR_EXP   — 1 journal Dr IE→Expense, pnl Dr 'SDrExp'
     * SINGLE_CR_EXP   — 1 journal Cr IE→Expense, pnl Cr 'SCrExp'
     */
    public function storeTransaction(Request $request)
    {
        $request->validate([
            'TransactionType' => ['required', 'string', 'in:GL_SINGLE,GL_DOUBLE,DR_GL_CR_INC,CR_GL_DR_EXP,CR_GL_DR_INC,DR_GL_CR_EXP,SINGLE_DR_INC,SINGLE_CR_INC,SINGLE_DR_EXP,SINGLE_CR_EXP'],
            'Amount' => ['required', 'numeric', 'min:0.01'],
            'PaymentDate' => ['required', 'date', 'before_or_equal:today'],
            'Description' => ['required', 'string', 'max:255'],
            'ReceiptID' => ['required', 'string'],
            'ReceiptNo' => ['required', 'string'],
        ]);

        $user = Auth::user();
        $type = $request->TransactionType;

        // ── Pre-requisite checks ──

        // 1. Active IE account — required for all income/expense types
        $ieAccount = null;
        $needsIE = ! in_array($type, ['GL_SINGLE', 'GL_DOUBLE']);
        if ($needsIE) {
            $ieAccount = DB::table('active_ie')->first();
            if (! $ieAccount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Active IE account not configured. Set it up in Basic Setup.',
                ], 422);
            }
        }

        // 2. Validate required account fields per type
        $validationError = $this->validateAccountFields($request, $type);
        if ($validationError) {
            return response()->json([
                'success' => false,
                'message' => $validationError,
            ], 422);
        }

        // 3. Block same account on both sides for double entry
        if (in_array($type, ['GL_DOUBLE', 'DR_GL_CR_INC', 'CR_GL_DR_EXP', 'CR_GL_DR_INC', 'DR_GL_CR_EXP'])) {
            $drAccount = $request->DrAccountNo;
            $crAccount = $request->CrAccountNo;
            if ($drAccount == $crAccount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Debit and credit accounts cannot be the same.',
                ], 422);
            }
        }

        // 4. Receipt number must be unique
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
        $amount = (float) $request->Amount;

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

            // b. Post journal + pnl based on type
            $this->postJournalEntries(
                $type,
                $request,
                $ieAccount,
                $amount,
                $description,
                $user
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaction saved successfully.',
                'ReceiptNo' => $request->ReceiptNo,
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

    /**
     * Validate that the correct account fields are present for the given type.
     */
    private function validateAccountFields(Request $request, string $type): ?string
    {
        switch ($type) {
            case 'GL_SINGLE':
                if (! $request->SingleAccountNo) {
                    return 'Please select an account.';
                }
                if (! $request->SingleMode) {
                    return 'Please select Dr or Cr.';
                }
                break;

            case 'GL_DOUBLE':
                if (! $request->DrAccountNo) {
                    return 'Please select a debit account.';
                }
                if (! $request->CrAccountNo) {
                    return 'Please select a credit account.';
                }
                break;

            case 'DR_GL_CR_INC':
            case 'DR_GL_CR_EXP':
                if (! $request->DrAccountNo) {
                    return 'Please select a debit GL account.';
                }
                if (! $request->CrAccountNo) {
                    return 'Please select a credit account.';
                }
                break;

            case 'CR_GL_DR_EXP':
            case 'CR_GL_DR_INC':
                if (! $request->CrAccountNo) {
                    return 'Please select a credit GL account.';
                }
                if (! $request->DrAccountNo) {
                    return 'Please select a debit account.';
                }
                break;

            case 'SINGLE_DR_INC':
            case 'SINGLE_DR_EXP':
                if (! $request->SingleAccountNo) {
                    return 'Please select an account.';
                }
                break;

            case 'SINGLE_CR_INC':
            case 'SINGLE_CR_EXP':
                if (! $request->SingleAccountNo) {
                    return 'Please select an account.';
                }
                break;
        }

        return null;
    }

    /**
     * Post all journal and pnl entries based on transaction type.
     */
    private function postJournalEntries(
        string $type,
        Request $request,
        ?object $ieAccount,
        float $amount,
        string $description,
        object $user
    ): void {
        $receiptNo = $request->ReceiptNo;
        $date = $request->PaymentDate;
        $branchID = $user->BranchID;
        $username = $user->ID;

        switch ($type) {

            // ── GL Single Entry ──
            case 'GL_SINGLE':
                $mode = $request->SingleMode; // 'Dr' or 'Cr'
                DB::table('journal')->insert([
                    'AccountID' => $request->SingleAccountNo,
                    'SubAccountID' => $request->SingleAccountNo,
                    'Mode' => $mode,
                    'TType' => 'NCash',
                    'ReceiptNo' => $receiptNo,
                    'Dr' => $mode === 'Dr' ? $amount : 0,
                    'Cr' => $mode === 'Cr' ? $amount : 0,
                    'Description' => $description,
                    'Date' => $date,
                    'Username' => $username,
                    'Authorizer' => 'N.Auth',
                    'BranchID' => $branchID,
                    'Status' => 1,
                ]);
                break;

                // ── GL Double Entry ──
            case 'GL_DOUBLE':
                // Dr line
                DB::table('journal')->insert([
                    'AccountID' => $request->DrAccountNo,
                    'SubAccountID' => $request->DrAccountNo,
                    'Mode' => 'Dr',
                    'TType' => 'NCash',
                    'ReceiptNo' => $receiptNo,
                    'Dr' => $amount,
                    'Cr' => 0,
                    'Description' => $description,
                    'Date' => $date,
                    'Username' => $username,
                    'Authorizer' => 'N.Auth',
                    'BranchID' => $branchID,
                    'Status' => 1,
                ]);
                // Cr line
                DB::table('journal')->insert([
                    'AccountID' => $request->CrAccountNo,
                    'SubAccountID' => $request->CrAccountNo,
                    'Mode' => 'Cr',
                    'TType' => 'NCash',
                    'ReceiptNo' => $receiptNo,
                    'Dr' => 0,
                    'Cr' => $amount,
                    'Description' => $description,
                    'Date' => $date,
                    'Username' => $username,
                    'Authorizer' => 'N.Auth',
                    'BranchID' => $branchID,
                    'Status' => 1,
                ]);
                break;

                // ── Dr GL – Cr Income ──
            case 'DR_GL_CR_INC':
                // Dr GL line
                DB::table('journal')->insert([
                    'AccountID' => $request->DrAccountNo,
                    'SubAccountID' => $request->DrAccountNo,
                    'Mode' => 'Dr',
                    'TType' => 'NCash',
                    'ReceiptNo' => $receiptNo,
                    'Dr' => $amount,
                    'Cr' => 0,
                    'Description' => $description,
                    'Date' => $date,
                    'Username' => $username,
                    'Authorizer' => 'N.Auth',
                    'BranchID' => $branchID,
                    'Status' => 1,
                ]);
                // Cr IE → Income line
                DB::table('journal')->insert([
                    'AccountID' => $ieAccount->AccountID,
                    'SubAccountID' => $request->CrAccountNo,
                    'Mode' => 'Cr',
                    'TType' => 'NCash',
                    'ReceiptNo' => $receiptNo,
                    'Dr' => 0,
                    'Cr' => $amount,
                    'Description' => $description,
                    'Date' => $date,
                    'Username' => $username,
                    'Authorizer' => 'N.Auth',
                    'BranchID' => $branchID,
                    'Status' => 1,
                ]);
                // PnL — Cr
                DB::table('pnl_transaction')->insert([
                    'AccountID' => $request->CrAccountNo,
                    'Stamp' => 'NB',
                    'Mode' => 'Cr',
                    'MainBL' => 'DrGLCrInc',
                    'HouseBL' => 'DrGLCrInc',
                    'ReceiptNo' => $receiptNo,
                    'Description' => $description,
                    'Dr' => 0,
                    'Cr' => $amount,
                    'Date' => $date,
                    'Time' => now()->toDateTimeString(),
                    'BranchID' => $branchID,
                    'Username' => $username,
                    'Status' => 1,
                ]);
                break;

                // ── Dr Expense – Cr GL ──
            case 'CR_GL_DR_EXP':
                // Cr GL line
                DB::table('journal')->insert([
                    'AccountID' => $request->CrAccountNo,
                    'SubAccountID' => $request->CrAccountNo,
                    'Mode' => 'Cr',
                    'TType' => 'NCash',
                    'ReceiptNo' => $receiptNo,
                    'Dr' => 0,
                    'Cr' => $amount,
                    'Description' => $description,
                    'Date' => $date,
                    'Username' => $username,
                    'Authorizer' => 'N.Auth',
                    'BranchID' => $branchID,
                    'Status' => 1,
                ]);
                // Dr IE → Expense line
                DB::table('journal')->insert([
                    'AccountID' => $ieAccount->AccountID,
                    'SubAccountID' => $request->DrAccountNo,
                    'Mode' => 'Dr',
                    'TType' => 'NCash',
                    'ReceiptNo' => $receiptNo,
                    'Dr' => $amount,
                    'Cr' => 0,
                    'Description' => $description,
                    'Date' => $date,
                    'Username' => $username,
                    'Authorizer' => 'N.Auth',
                    'BranchID' => $branchID,
                    'Status' => 1,
                ]);
                // PnL — Dr
                DB::table('pnl_transaction')->insert([
                    'AccountID' => $request->DrAccountNo,
                    'Stamp' => 'NB',
                    'Mode' => 'Dr',
                    'MainBL' => 'DrExpCrGL',
                    'HouseBL' => 'DrExpCrGL',
                    'ReceiptNo' => $receiptNo,
                    'Description' => $description,
                    'Dr' => $amount,
                    'Cr' => 0,
                    'Date' => $date,
                    'Time' => now()->toDateTimeString(),
                    'BranchID' => $branchID,
                    'Username' => $username,
                    'Status' => 1,
                ]);
                break;

                // ── Cr GL – Dr Income ──
            case 'CR_GL_DR_INC':
                // Cr GL line
                DB::table('journal')->insert([
                    'AccountID' => $request->CrAccountNo,
                    'SubAccountID' => $request->CrAccountNo,
                    'Mode' => 'Cr',
                    'TType' => 'NCash',
                    'ReceiptNo' => $receiptNo,
                    'Dr' => 0,
                    'Cr' => $amount,
                    'Description' => $description,
                    'Date' => $date,
                    'Username' => $username,
                    'Authorizer' => 'N.Auth',
                    'BranchID' => $branchID,
                    'Status' => 1,
                ]);
                // Dr IE → Income line
                DB::table('journal')->insert([
                    'AccountID' => $ieAccount->AccountID,
                    'SubAccountID' => $request->DrAccountNo,
                    'Mode' => 'Dr',
                    'TType' => 'NCash',
                    'ReceiptNo' => $receiptNo,
                    'Dr' => $amount,
                    'Cr' => 0,
                    'Description' => $description,
                    'Date' => $date,
                    'Username' => $username,
                    'Authorizer' => 'N.Auth',
                    'BranchID' => $branchID,
                    'Status' => 1,
                ]);
                // PnL — Dr
                DB::table('pnl_transaction')->insert([
                    'AccountID' => $request->DrAccountNo,
                    'Stamp' => 'NB',
                    'Mode' => 'Dr',
                    'MainBL' => 'CrGLDrInc',
                    'HouseBL' => 'CrGLDrInc',
                    'ReceiptNo' => $receiptNo,
                    'Description' => $description,
                    'Dr' => $amount,
                    'Cr' => 0,
                    'Date' => $date,
                    'Time' => now()->toDateTimeString(),
                    'BranchID' => $branchID,
                    'Username' => $username,
                    'Status' => 1,
                ]);
                break;

                // ── Dr GL – Cr Expense ──
            case 'DR_GL_CR_EXP':
                // Dr GL line
                DB::table('journal')->insert([
                    'AccountID' => $request->DrAccountNo,
                    'SubAccountID' => $request->DrAccountNo,
                    'Mode' => 'Dr',
                    'TType' => 'NCash',
                    'ReceiptNo' => $receiptNo,
                    'Dr' => $amount,
                    'Cr' => 0,
                    'Description' => $description,
                    'Date' => $date,
                    'Username' => $username,
                    'Authorizer' => 'N.Auth',
                    'BranchID' => $branchID,
                    'Status' => 1,
                ]);
                // Cr IE → Expense line
                DB::table('journal')->insert([
                    'AccountID' => $ieAccount->AccountID,
                    'SubAccountID' => $request->CrAccountNo,
                    'Mode' => 'Cr',
                    'TType' => 'NCash',
                    'ReceiptNo' => $receiptNo,
                    'Dr' => 0,
                    'Cr' => $amount,
                    'Description' => $description,
                    'Date' => $date,
                    'Username' => $username,
                    'Authorizer' => 'N.Auth',
                    'BranchID' => $branchID,
                    'Status' => 1,
                ]);
                // PnL — Cr
                DB::table('pnl_transaction')->insert([
                    'AccountID' => $request->CrAccountNo,
                    'Stamp' => 'NB',
                    'Mode' => 'Cr',
                    'MainBL' => 'DrGLCrExp',
                    'HouseBL' => 'DrGLCrExp',
                    'ReceiptNo' => $receiptNo,
                    'Description' => $description,
                    'Dr' => 0,
                    'Cr' => $amount,
                    'Date' => $date,
                    'Time' => now()->toDateTimeString(),
                    'BranchID' => $branchID,
                    'Username' => $username,
                    'Status' => 1,
                ]);
                break;

                // ── Single Dr Income ──
            case 'SINGLE_DR_INC':
                DB::table('journal')->insert([
                    'AccountID' => $ieAccount->AccountID,
                    'SubAccountID' => $request->SingleAccountNo,
                    'Mode' => 'Dr',
                    'TType' => 'NCash',
                    'ReceiptNo' => $receiptNo,
                    'Dr' => $amount,
                    'Cr' => 0,
                    'Description' => $description,
                    'Date' => $date,
                    'Username' => $username,
                    'Authorizer' => 'N.Auth',
                    'BranchID' => $branchID,
                    'Status' => 1,
                ]);
                DB::table('pnl_transaction')->insert([
                    'AccountID' => $request->SingleAccountNo,
                    'Stamp' => 'NB',
                    'Mode' => 'Dr',
                    'MainBL' => 'SDrInc',
                    'HouseBL' => 'SDrInc',
                    'ReceiptNo' => $receiptNo,
                    'Description' => $description,
                    'Dr' => $amount,
                    'Cr' => 0,
                    'Date' => $date,
                    'Time' => now()->toDateTimeString(),
                    'BranchID' => $branchID,
                    'Username' => $username,
                    'Status' => 1,
                ]);
                break;

                // ── Single Cr Income ──
            case 'SINGLE_CR_INC':
                DB::table('journal')->insert([
                    'AccountID' => $ieAccount->AccountID,
                    'SubAccountID' => $request->SingleAccountNo,
                    'Mode' => 'Cr',
                    'TType' => 'NCash',
                    'ReceiptNo' => $receiptNo,
                    'Dr' => 0,
                    'Cr' => $amount,
                    'Description' => $description,
                    'Date' => $date,
                    'Username' => $username,
                    'Authorizer' => 'N.Auth',
                    'BranchID' => $branchID,
                    'Status' => 1,
                ]);
                DB::table('pnl_transaction')->insert([
                    'AccountID' => $request->SingleAccountNo,
                    'Stamp' => 'NB',
                    'Mode' => 'Cr',
                    'MainBL' => 'SCrInc',
                    'HouseBL' => 'SCrInc',
                    'ReceiptNo' => $receiptNo,
                    'Description' => $description,
                    'Dr' => 0,
                    'Cr' => $amount,
                    'Date' => $date,
                    'Time' => now()->toDateTimeString(),
                    'BranchID' => $branchID,
                    'Username' => $username,
                    'Status' => 1,
                ]);
                break;

                // ── Single Dr Expense ──
            case 'SINGLE_DR_EXP':
                DB::table('journal')->insert([
                    'AccountID' => $ieAccount->AccountID,
                    'SubAccountID' => $request->SingleAccountNo,
                    'Mode' => 'Dr',
                    'TType' => 'NCash',
                    'ReceiptNo' => $receiptNo,
                    'Dr' => $amount,
                    'Cr' => 0,
                    'Description' => $description,
                    'Date' => $date,
                    'Username' => $username,
                    'Authorizer' => 'N.Auth',
                    'BranchID' => $branchID,
                    'Status' => 1,
                ]);
                DB::table('pnl_transaction')->insert([
                    'AccountID' => $request->SingleAccountNo,
                    'Stamp' => 'NB',
                    'Mode' => 'Dr',
                    'MainBL' => 'SDrExp',
                    'HouseBL' => 'SDrExp',
                    'ReceiptNo' => $receiptNo,
                    'Description' => $description,
                    'Dr' => $amount,
                    'Cr' => 0,
                    'Date' => $date,
                    'Time' => now()->toDateTimeString(),
                    'BranchID' => $branchID,
                    'Username' => $username,
                    'Status' => 1,
                ]);
                break;

                // ── Single Cr Expense ──
            case 'SINGLE_CR_EXP':
                DB::table('journal')->insert([
                    'AccountID' => $ieAccount->AccountID,
                    'SubAccountID' => $request->SingleAccountNo,
                    'Mode' => 'Cr',
                    'TType' => 'NCash',
                    'ReceiptNo' => $receiptNo,
                    'Dr' => 0,
                    'Cr' => $amount,
                    'Description' => $description,
                    'Date' => $date,
                    'Username' => $username,
                    'Authorizer' => 'N.Auth',
                    'BranchID' => $branchID,
                    'Status' => 1,
                ]);
                DB::table('pnl_transaction')->insert([
                    'AccountID' => $request->SingleAccountNo,
                    'Stamp' => 'NB',
                    'Mode' => 'Cr',
                    'MainBL' => 'SCrExp',
                    'HouseBL' => 'SCrExp',
                    'ReceiptNo' => $receiptNo,
                    'Description' => $description,
                    'Dr' => 0,
                    'Cr' => $amount,
                    'Date' => $date,
                    'Time' => now()->toDateTimeString(),
                    'BranchID' => $branchID,
                    'Username' => $username,
                    'Status' => 1,
                ]);
                break;
        }
    }
}
