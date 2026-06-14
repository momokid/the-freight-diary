<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Services\CompanyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
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

    public function outstandingCollections()
    {
        return view('reports.management.outstanding-collections');
    }

    public function outstandingCollectionsPrint(Request $request)
    {
        $request->validate([
            'as_at' => 'required|date',
        ]);

        $asAt = $request->as_at;
        $user = Auth::user();

        // Main rows — one per consignee per BL/invoice reference
        $rows = DB::select("
            SELECT
                sf.StudentID,
                co.FullName AS ConsigneeName,
                sf.Stamp,
                CASE
                    WHEN sf.Stamp = 'BL'       THEN sf.SubClassID
                    WHEN sf.Stamp = 'BL_NONBL' THEN sf.CouponID
                    ELSE sf.CouponID
                END                                                         AS Reference,
                MIN(CASE WHEN sf.Dr > 0 THEN sf.Date END)                  AS InvoiceDate,
                DATEDIFF(?, MIN(CASE WHEN sf.Dr > 0 THEN sf.Date END))     AS DaysOutstanding,
                COALESCE(SUM(sf.Dr), 0) - COALESCE(SUM(sf.Cr), 0)         AS Outstanding
            FROM student_fee sf
            JOIN consignee_main co ON co.ConsigneeID = sf.StudentID
            WHERE sf.Date <= ?
            GROUP BY
                sf.StudentID,
                co.FullName,
                sf.SubClassID,
                sf.CouponID,
                sf.Stamp
            HAVING COALESCE(SUM(sf.Dr), 0) - COALESCE(SUM(sf.Cr), 0) > 0
            ORDER BY co.FullName, InvoiceDate
        ", [$asAt, $asAt]);

        // Summary strip — totals by aging bucket
        $summary = DB::selectOne("
            SELECT
                COALESCE(SUM(Outstanding), 0)                                                        AS total,
                COALESCE(SUM(CASE WHEN DaysOutstanding <= 30                   THEN Outstanding END), 0) AS bucket_30,
                COALESCE(SUM(CASE WHEN DaysOutstanding BETWEEN 31 AND 60      THEN Outstanding END), 0) AS bucket_60,
                COALESCE(SUM(CASE WHEN DaysOutstanding BETWEEN 61 AND 90      THEN Outstanding END), 0) AS bucket_90,
                COALESCE(SUM(CASE WHEN DaysOutstanding > 90                   THEN Outstanding END), 0) AS bucket_90plus
            FROM (
                SELECT
                    COALESCE(SUM(sf.Dr), 0) - COALESCE(SUM(sf.Cr), 0)                              AS Outstanding,
                    DATEDIFF(?, MIN(CASE WHEN sf.Dr > 0 THEN sf.Date END))                          AS DaysOutstanding
                FROM student_fee sf
                WHERE sf.Date <= ?
                GROUP BY sf.StudentID, sf.SubClassID, sf.CouponID, sf.Stamp
                HAVING COALESCE(SUM(sf.Dr), 0) - COALESCE(SUM(sf.Cr), 0) > 0
            ) AS aged
        ", [$asAt, $asAt]);

        $company = CompanyService::get();

        return view('reports.management.outstanding-collections-print', compact(
            'rows',
            'summary',
            'asAt',
            'company',
            'user'
        ));
    }

    public function financialPerformance()
    {
        return view('reports.management.executive-summary');
    }

    public function financialPerformancePrint(Request $request)
    {
        $request->validate([
            'period'  => 'required|date_format:Y-m',
            'compare' => 'required|in:prev_month,same_month_last_year,year_on_year',
        ]);

        $user   = Auth::user();
        $branch = $user->BranchID;

        // ── Build current and previous period date ranges ──
        $currentStart = Carbon::createFromFormat('Y-m', $request->period)->startOfMonth();
        $currentEnd   = Carbon::createFromFormat('Y-m', $request->period)->endOfMonth();

        switch ($request->compare) {
            case 'prev_month':
                $prevStart    = $currentStart->copy()->subMonthNoOverflow()->startOfMonth();
                $prevEnd      = $currentStart->copy()->subMonthNoOverflow()->endOfMonth();
                $currentLabel = $currentStart->format('M Y');
                $prevLabel    = $prevStart->format('M Y');
                break;

            case 'same_month_last_year':
                $prevStart    = $currentStart->copy()->subYear()->startOfMonth();
                $prevEnd      = $currentEnd->copy()->subYear()->endOfMonth();
                $currentLabel = $currentStart->format('M Y');
                $prevLabel    = $prevStart->format('M Y');
                break;

            case 'year_on_year':
            default:
                $year         = $currentStart->year;
                $monthName    = $currentEnd->format('M');
                $prevStart    = Carbon::create($year - 1, 1, 1)->startOfDay();
                $prevEnd      = Carbon::create($year - 1, $currentEnd->month, 1)->endOfMonth();
                $currentStart = Carbon::create($year, 1, 1)->startOfDay();
                $currentLabel = 'Jan–' . $monthName . ' ' . $year . ' YTD';
                $prevLabel    = 'Jan–' . $monthName . ' ' . ($year - 1) . ' YTD';
                break;
        }

        // ── Reusable query — run once per period ──
        $sql = "
            SELECT
                la.AccountNo,
                la.AccountName,
                la.Type,
                CASE WHEN la.Type = 'INCOME'
                     THEN COALESCE(SUM(j.Cr), 0) - COALESCE(SUM(j.Dr), 0)
                     ELSE COALESCE(SUM(j.Dr), 0) - COALESCE(SUM(j.Cr), 0)
                END AS amount
            FROM journal j
            JOIN ledger_account la ON la.AccountNo = j.SubAccountID
            JOIN active_ie ai      ON ai.AccountID = j.AccountID
            WHERE j.Date BETWEEN ? AND ?
              AND j.BranchID = ?
              AND la.Type IN ('INCOME', 'EXPENDITURE')
            GROUP BY la.AccountNo, la.AccountName, la.Type
            ORDER BY la.Type, la.AccountName
        ";

        $currentRows = DB::select($sql, [
            $currentStart->format('Y-m-d'),
            $currentEnd->format('Y-m-d'),
            $branch,
        ]);

        $prevRows = DB::select($sql, [
            $prevStart->format('Y-m-d'),
            $prevEnd->format('Y-m-d'),
            $branch,
        ]);

        // ── Build lookup tables by AccountNo ──
        $currLookup = [];
        foreach ($currentRows as $row) {
            $currLookup[$row->AccountNo] = $row;
        }

        $prevLookup = [];
        foreach ($prevRows as $row) {
            $prevLookup[$row->AccountNo] = $row;
        }

        // ── Merge both periods into unified account list ──
        $allAccounts = [];
        foreach ($currentRows as $row) {
            $allAccounts[$row->AccountNo] = $row;
        }
        foreach ($prevRows as $row) {
            if (!isset($allAccounts[$row->AccountNo])) {
                $allAccounts[$row->AccountNo] = $row;
            }
        }

        $incomeRows = [];
        $expendRows = [];

        foreach ($allAccounts as $accountNo => $meta) {
            $curr    = $currLookup[$accountNo]->amount ?? 0;
            $prev    = $prevLookup[$accountNo]->amount ?? 0;
            $varGhs  = $curr - $prev;
            $varPct  = $prev != 0
                ? round(($varGhs / abs($prev)) * 100, 1)
                : ($curr != 0 ? 100.0 : 0.0);

            $entry = [
                'AccountNo'   => $meta->AccountNo,
                'AccountName' => $meta->AccountName,
                'Type'        => $meta->Type,
                'current'     => $curr,
                'previous'    => $prev,
                'var_ghs'     => $varGhs,
                'var_pct'     => $varPct,
            ];

            if ($meta->Type === 'INCOME') {
                $incomeRows[] = $entry;
            } else {
                $expendRows[] = $entry;
            }
        }

        usort($incomeRows, fn($a, $b) => strcmp($a['AccountName'], $b['AccountName']));
        usort($expendRows, fn($a, $b) => strcmp($a['AccountName'], $b['AccountName']));

        // ── Period totals ──
        $totalCurrIncome = array_sum(array_column($incomeRows, 'current'));
        $totalPrevIncome = array_sum(array_column($incomeRows, 'previous'));
        $totalCurrExpend = array_sum(array_column($expendRows, 'current'));
        $totalPrevExpend = array_sum(array_column($expendRows, 'previous'));
        $totalCurrNet    = $totalCurrIncome - $totalCurrExpend;
        $totalPrevNet    = $totalPrevIncome - $totalPrevExpend;

        $incomeVarGhs = $totalCurrIncome - $totalPrevIncome;
        $incomeVarPct = $totalPrevIncome != 0
            ? round(($incomeVarGhs / abs($totalPrevIncome)) * 100, 1) : 0;

        $expendVarGhs = $totalCurrExpend - $totalPrevExpend;
        $expendVarPct = $totalPrevExpend != 0
            ? round(($expendVarGhs / abs($totalPrevExpend)) * 100, 1) : 0;

        $netVarGhs = $totalCurrNet - $totalPrevNet;
        $netVarPct = $totalPrevNet != 0
            ? round(($netVarGhs / abs($totalPrevNet)) * 100, 1) : 0;

        $company = CompanyService::get();

        return view('reports.management.financial-performance-print', compact(
            'incomeRows',
            'expendRows',
            'totalCurrIncome',
            'totalPrevIncome',
            'incomeVarGhs',
            'incomeVarPct',
            'totalCurrExpend',
            'totalPrevExpend',
            'expendVarGhs',
            'expendVarPct',
            'totalCurrNet',
            'totalPrevNet',
            'netVarGhs',
            'netVarPct',
            'currentLabel',
            'prevLabel',
            'company',
            'user'
        ));
    }
}
