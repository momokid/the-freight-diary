<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Services\CompanyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ManagementReportController extends Controller
{
    public function executiveSummary()
    {
        return view('reports.management.executive-summary');
    }

    public function executiveSummaryPrint(Request $request)
    {
        $request->validate([
            'date_from' => 'required|date',
            'date_to'   => 'required|date|after_or_equal:date_from',
        ]);

        $dateFrom = $request->date_from;
        $dateTo   = $request->date_to;
        $user     = Auth::user();
        $branch   = $user->BranchID;

        // 1. Pipeline counts — all active consignments, no date filter
        $pipeline = DB::selectOne("
            SELECT
                SUM(CASE WHEN Status = 1 THEN 1 ELSE 0 END) AS not_arrived,
                SUM(CASE WHEN Status = 2 THEN 1 ELSE 0 END) AS pending,
                SUM(CASE WHEN Status = 3 THEN 1 ELSE 0 END) AS gated_out,
                SUM(CASE WHEN Status = 0 THEN 1 ELSE 0 END) AS cleared,
                COUNT(*) AS total
            FROM container_main
            WHERE Status != 9
        ");

        // 2. Revenue for period — net = SUM(Cr) - SUM(Dr)
        $revenue = DB::selectOne("
            SELECT COALESCE(SUM(j.Cr), 0) - COALESCE(SUM(j.Dr), 0) AS total
            FROM journal j
            JOIN ledger_account la ON la.AccountNo = j.SubAccountID
            JOIN active_ie ai      ON ai.AccountID = j.AccountID
            WHERE j.Date BETWEEN ? AND ?
              AND j.BranchID = ?
              AND la.Type = 'INCOME'
        ", [$dateFrom, $dateTo, $branch]);

        // 3. Expenditure for period — net = SUM(Dr) - SUM(Cr)
        $expenditure = DB::selectOne("
            SELECT COALESCE(SUM(j.Dr), 0) - COALESCE(SUM(j.Cr), 0) AS total
            FROM journal j
            JOIN ledger_account la ON la.AccountNo = j.SubAccountID
            JOIN active_ie ai      ON ai.AccountID = j.AccountID
            WHERE j.Date BETWEEN ? AND ?
              AND j.BranchID = ?
              AND la.Type = 'EXPENDITURE'
        ", [$dateFrom, $dateTo, $branch]);

        $netProfit = ($revenue->total ?? 0) - ($expenditure->total ?? 0);

        // 4. Cash position as at DateTo — Dr - Cr per active GL account
        $cashAccounts = DB::select("
            SELECT
                la.AccountName,
                COALESCE(SUM(j.Dr), 0) - COALESCE(SUM(j.Cr), 0) AS balance
            FROM active_bank_cash abc
            JOIN ledger_account la ON la.AccountNo = abc.AccountID
            LEFT JOIN journal j
                ON  j.AccountID    = abc.AccountID
                AND j.SubAccountID = abc.AccountID
                AND j.Date        <= ?
                AND j.BranchID     = ?
            GROUP BY abc.AccountID, la.AccountName
            ORDER BY la.AccountName
        ", [$dateTo, $branch]);

        // 5. Weekly revenue vs expenditure for bar chart
        $chartRows = DB::select("
            SELECT
                YEARWEEK(j.Date, 1) AS week_key,
                MIN(j.Date)         AS week_start,
                COALESCE(SUM(CASE WHEN la.Type = 'INCOME'
                    THEN j.Cr - j.Dr ELSE 0 END), 0) AS revenue,
                COALESCE(SUM(CASE WHEN la.Type = 'EXPENDITURE'
                    THEN j.Dr - j.Cr ELSE 0 END), 0) AS expenditure
            FROM journal j
            JOIN ledger_account la ON la.AccountNo = j.SubAccountID
            JOIN active_ie ai      ON ai.AccountID = j.AccountID
            WHERE j.Date BETWEEN ? AND ?
              AND j.BranchID = ?
              AND la.Type IN ('INCOME', 'EXPENDITURE')
            GROUP BY YEARWEEK(j.Date, 1)
            ORDER BY week_key
        ", [$dateFrom, $dateTo, $branch]);

        // 6. Vision 5:29 strip — inline, no helper methods
        $vision    = [];
        $vTarget   = DB::table('vision_targets')->where('IsActive', 1)->first();
        $ieAccount = DB::table('active_ie')->first();

        if ($vTarget && $ieAccount) {
            $cumIncome = DB::table('journal')
                ->join('ledger_account', 'journal.SubAccountID', '=', 'ledger_account.AccountNo')
                ->where('journal.AccountID', $ieAccount->AccountID)
                ->whereYear('journal.Date', '>=', $vTarget->StartYear)
                ->where('journal.Reversed', 0)
                ->where('journal.Status', 1)
                ->where('journal.BranchID', $branch)
                ->where('ledger_account.Type', 'INCOME')
                ->sum('journal.Cr');

            $cumExpend = DB::table('journal')
                ->join('ledger_account', 'journal.SubAccountID', '=', 'ledger_account.AccountNo')
                ->where('journal.AccountID', $ieAccount->AccountID)
                ->whereYear('journal.Date', '>=', $vTarget->StartYear)
                ->where('journal.Reversed', 0)
                ->where('journal.Status', 1)
                ->where('journal.BranchID', $branch)
                ->where('ledger_account.Type', 'EXPENDITURE')
                ->sum('journal.Dr');

            $cumulative  = round($cumIncome - $cumExpend, 2);
            $progressPct = $vTarget->TargetAmount > 0
                ? round(($cumulative / $vTarget->TargetAmount) * 100, 2)
                : 0;

            $vision = [
                'target'       => $vTarget,
                'cumulative'   => $cumulative,
                'progress_pct' => $progressPct,
                'rag'          => $progressPct >= 100 ? 'green'
                    : ($progressPct >= 75  ? 'amber' : 'red'),
            ];
        }

        $company = CompanyService::get();

        return view('reports.management.executive-summary-print', compact(
            'pipeline',
            'revenue',
            'expenditure',
            'netProfit',
            'cashAccounts',
            'chartRows',
            'vision',
            'dateFrom',
            'dateTo',
            'company',
            'user'
        ));
    }
}
