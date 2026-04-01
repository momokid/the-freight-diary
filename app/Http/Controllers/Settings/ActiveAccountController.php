<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\LedgerAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActiveAccountController extends Controller
{
    // All active account configurations in one place
    // Each entry defines the table, column, filter and label
    private function getConfigs(): array
    {
        return [
            'ie' => [
                'label'  => 'IE Account',
                'table'  => 'active_ie',
                'column' => 'AccountID',
                'type'   => 'GL',
            ],
            'vault' => [
                'label'  => 'Vault Account',
                'table'  => 'active_vault',
                'column' => 'AccountNo',
                'type'   => 'GL',
            ],
            'bank_cash' => [
                'label'  => 'Bank / Cash Account',
                'table'  => 'active_bank_cash',
                'column' => 'AccountID',
                'type'   => 'GL',
            ],
            'momo' => [
                'label'  => 'Mobile Money Account',
                'table'  => 'active_momo',
                'column' => 'AccountNo',
                'type'   => 'GL',
            ],
            'petty' => [
                'label'  => 'Petty Cash Account',
                'table'  => 'active_petty',
                'column' => 'AccountNo',
                'type'   => 'GL',
            ],
            'write_off' => [
                'label'  => 'Write Off Account',
                'table'  => 'active_write_off',
                'column' => 'AccountNo',
                'type'   => 'GL',
            ],
            'account_receivable' => [
                'label'  => 'Account Receivable',
                'table'  => 'active_account_receivable',
                'column' => 'AccountNo',
                'type'   => 'GL',
            ],
            'service_charge' => [
                'label'  => 'Service Charge Account',
                'table'  => 'active_service_charge',
                'column' => 'AccountNo',
                'type'   => 'INCOME',
            ],
            'consignment_revenue' => [
                'label'  => 'Consignment Revenue Account',
                'table'  => 'active_consignment_revenue',
                'column' => 'AccountNo',
                'type'   => 'INCOME',
            ],
            'declaration_income' => [
                'label'  => 'Declaration Income Account',
                'table'  => 'active_declaration_income',
                'column' => 'AccountNo',
                'type'   => 'INCOME',
            ],
            // Special case — two columns
            'accounts' => [
                'label'  => 'IE Main + Cash Receipt',
                'table'  => 'active_accounts',
                'column' => null, // handled separately
                'type'   => 'GL',
            ],
        ];
    }

    public function index()
    {
        $configs = $this->getConfigs();

        // Load current account for each config
        $current = [];
        foreach ($configs as $key => $config) {
            if ($key === 'accounts') {
                $row = DB::table('active_accounts')->first();
                if ($row) {
                    $ieMain      = LedgerAccount::find($row->IE_Main);
                    $cashReceipt = LedgerAccount::find($row->CashReceipt);
                    $current[$key] = [
                        'ie_main'      => $ieMain,
                        'cash_receipt' => $cashReceipt,
                    ];
                } else {
                    $current[$key] = null;
                }
            } else {
                $row = DB::table($config['table'])->first();
                if ($row) {
                    $current[$key] = LedgerAccount::find($row->{$config['column']});
                } else {
                    $current[$key] = null;
                }
            }
        }

        // Load GL and INCOME accounts for dropdowns
        $glAccounts     = LedgerAccount::active()->where('Type', 'GL')->orderBy('AccountName')->get();
        $incomeAccounts = LedgerAccount::active()->where('Type', 'INCOME')->orderBy('AccountName')->get();

        return view('settings.active-accounts', compact(
            'configs',
            'current',
            'glAccounts',
            'incomeAccounts'
        ));
    }

    public function update(Request $request, string $key)
    {
        $configs = $this->getConfigs();

        if (!array_key_exists($key, $configs)) {
            return response()->json(['success' => false, 'message' => 'Invalid account type.'], 404);
        }

        $config = $configs[$key];

        // Special case — active_accounts has two columns
        if ($key === 'accounts') {
            $request->validate([
                'ie_main'      => ['required', 'integer', 'exists:ledger_account,AccountNo'],
                'cash_receipt' => ['required', 'integer', 'exists:ledger_account,AccountNo'],
            ]);

            DB::table('active_accounts')->delete();
            DB::table('active_accounts')->insert([
                'IE_Main'     => $request->ie_main,
                'CashReceipt' => $request->cash_receipt,
            ]);

            return response()->json(['success' => true, 'message' => 'Active accounts updated successfully.']);
        }

        // Standard single column case
        $request->validate([
            'account_no' => ['required', 'integer', 'exists:ledger_account,AccountNo'],
        ]);

        // Delete existing and insert new — only one account allowed
        DB::table($config['table'])->delete();
        DB::table($config['table'])->insert([
            $config['column'] => $request->account_no,
        ]);

        return response()->json(['success' => true, 'message' => "{$config['label']} updated successfully."]);
    }
}