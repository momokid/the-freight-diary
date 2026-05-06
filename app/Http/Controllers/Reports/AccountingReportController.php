<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class AccountingReportController extends BaseReportController
{
    // ════════════════════════════════════════════════════════════════════════
    // INDEX
    // ════════════════════════════════════════════════════════════════════════

    public function index()
    {
        return view('reports.accounting.index');
    }

    // ════════════════════════════════════════════════════════════════════════
    // SHARED HELPERS
    // ════════════════════════════════════════════════════════════════════════

    // Active Vision 5:29 target
    private function getVisionTarget(): ?object
    {
        return DB::table('vision_targets')
            ->where('IsActive', 1)
            ->orderByDesc('id')
            ->first();
    }

    // YTD net surplus from journal
    private function getYtdSurplus(string $branchID): float
    {
        $year = now()->year;
        $ieAccount = DB::table('active_ie')->first();
        if (! $ieAccount) {
            return 0;
        }

        $income = DB::table('journal')
            ->join('ledger_account', 'journal.SubAccountID', '=', 'ledger_account.AccountNo')
            ->where('journal.AccountID', $ieAccount->AccountID)
            ->whereYear('journal.Date', $year)
            ->where('journal.Reversed', 0)
            ->where('journal.Status', 1)
            ->when($branchID !== 'ALL', fn ($q) => $q->where('journal.BranchID', $branchID))
            ->where('ledger_account.Type', 'INCOME')
            ->sum('journal.Cr');

        $expenditure = DB::table('journal')
            ->join('ledger_account', 'journal.SubAccountID', '=', 'ledger_account.AccountNo')
            ->where('journal.AccountID', $ieAccount->AccountID)
            ->whereYear('journal.Date', $year)
            ->where('journal.Reversed', 0)
            ->where('journal.Status', 1)
            ->when($branchID !== 'ALL', fn ($q) => $q->where('journal.BranchID', $branchID))
            ->where('ledger_account.Type', 'EXPENDITURE')
            ->sum('journal.Dr');

        return round($income - $expenditure, 2);
    }

    // Cumulative surplus from StartYear to now
    private function getCumulativeSurplus(string $branchID, int $startYear): float
    {
        $ieAccount = DB::table('active_ie')->first();
        if (! $ieAccount) {
            return 0;
        }

        $income = DB::table('journal')
            ->join('ledger_account', 'journal.SubAccountID', '=', 'ledger_account.AccountNo')
            ->where('journal.AccountID', $ieAccount->AccountID)
            ->whereYear('journal.Date', '>=', $startYear)
            ->where('journal.Reversed', 0)
            ->where('journal.Status', 1)
            ->when($branchID !== 'ALL', fn ($q) => $q->where('journal.BranchID', $branchID))
            ->where('ledger_account.Type', 'INCOME')
            ->sum('journal.Cr');

        $expenditure = DB::table('journal')
            ->join('ledger_account', 'journal.SubAccountID', '=', 'ledger_account.AccountNo')
            ->where('journal.AccountID', $ieAccount->AccountID)
            ->whereYear('journal.Date', '>=', $startYear)
            ->where('journal.Reversed', 0)
            ->where('journal.Status', 1)
            ->when($branchID !== 'ALL', fn ($q) => $q->where('journal.BranchID', $branchID))
            ->where('ledger_account.Type', 'EXPENDITURE')
            ->sum('journal.Dr');

        return round($income - $expenditure, 2);
    }

    // Build Vision 5:29 progress data
    private function buildVisionProgress(string $branchID): array
    {
        $target = $this->getVisionTarget();
        if (! $target) {
            return [];
        }

        $ytdSurplus = $this->getYtdSurplus($branchID);
        $cumulativeSurplus = $this->getCumulativeSurplus($branchID, $target->StartYear);
        $yearsRemaining = max(0, $target->TargetYear - now()->year);
        $progressPct = $target->TargetAmount > 0
            ? round(($cumulativeSurplus / $target->TargetAmount) * 100, 2)
            : 0;
        $yearsElapsed = max(1, now()->year - $target->StartYear);
        $avgAnnualSurplus = round($cumulativeSurplus / $yearsElapsed, 2);
        $requiredAnnual = $yearsRemaining > 0
            ? round(($target->TargetAmount - $cumulativeSurplus) / $yearsRemaining, 2)
            : 0;
        $onTrack = $avgAnnualSurplus >= $requiredAnnual;

        return [
            'target' => $target,
            'ytd_surplus' => $ytdSurplus,
            'cumulative_surplus' => $cumulativeSurplus,
            'years_remaining' => $yearsRemaining,
            'progress_pct' => $progressPct,
            'avg_annual' => $avgAnnualSurplus,
            'required_annual' => $requiredAnnual,
            'on_track' => $onTrack,
        ];
    }

    // Branch list for filter dropdown
    private function getBranches(): \Illuminate\Support\Collection
    {
        return DB::table('inst_branch')
            ->orderBy('Branch')
            ->get(['BranchID', 'Branch']);
    }

    // ════════════════════════════════════════════════════════════════════════
    // REPORT 1 — TRIAL BALANCE
    // ════════════════════════════════════════════════════════════════════════

    private function buildTrialBalance(string $asAt, string $branchID): \Illuminate\Support\Collection
    {
        $allowedRestricted = $this->allowedRestricted();

        $rows = DB::table('journal as j')
            ->join('ledger_account as la', 'j.AccountID', '=', 'la.AccountNo')
            ->where('j.Reversed', 0)
            ->where('j.Date', '<=', $asAt)
            ->whereIn('j.Restricted', $allowedRestricted)
            ->when($branchID !== 'ALL', fn ($q) => $q->where('j.BranchID', $branchID))
            ->groupBy('j.AccountID')
            ->orderByRaw('la.Type, la.AccountName')
            ->get([
                'j.AccountID',
                'la.AccountName',
                'la.Type',
                'la.Class',
                DB::raw('ROUND(SUM(j.Dr), 2) as TotalDr'),
                DB::raw('ROUND(SUM(j.Cr), 2) as TotalCr'),
            ])
            ->map(function ($row) {
                $row->Balance = $row->Class === 'Dr'
                    ? round($row->TotalDr - $row->TotalCr, 2)
                    : round($row->TotalCr - $row->TotalDr, 2);

                return $row;
            });

        return $rows;
    }

    public function trialBalancePrint(Request $request)
    {
        $request->validate([
            'as_at' => ['required', 'date'],
            'branch_id' => ['nullable', 'string'],
        ]);

        $user = Auth::user();
        $asAt = $request->as_at;
        $branchID = $request->branch_id ?? $user->BranchID;

        $rows = $this->buildTrialBalance($asAt, $branchID);
        $totalDr = round($rows->sum('TotalDr'), 2);
        $totalCr = round($rows->sum('TotalCr'), 2);
        $vision = $this->buildVisionProgress($branchID);
        $reportTitle = 'Trial Balance';
        $asAtFormatted = \Carbon\Carbon::parse($asAt)->format('d M Y');

        return view('reports.accounting.trial-balance-print', compact(
            'rows', 'totalDr', 'totalCr', 'vision',
            'reportTitle', 'asAtFormatted', 'branchID'
        ));
    }

    public function trialBalanceExport(Request $request)
    {
        $request->validate([
            'as_at' => ['required', 'date'],
            'branch_id' => ['nullable', 'string'],
        ]);

        $user = Auth::user();
        $asAt = $request->as_at;
        $branchID = $request->branch_id ?? $user->BranchID;

        $rows = $this->buildTrialBalance($asAt, $branchID);
        $totalDr = round($rows->sum('TotalDr'), 2);
        $totalCr = round($rows->sum('TotalCr'), 2);

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Trial Balance');

        $this->buildExcelHeader(
            $sheet, 'Trial Balance — As At '.
            \Carbon\Carbon::parse($asAt)->format('d M Y'),
            '', '', 'E'
        );

        $headers = ['Account No', 'Account Name', 'Type', 'Total Dr (GH₵)', 'Total Cr (GH₵)', 'Balance (GH₵)'];
        $dataRow = $this->buildExcelColumnHeaders($sheet, 6, $headers);

        foreach ($rows as $r) {
            $sheet->setCellValue('A'.$dataRow, $r->AccountID);
            $sheet->setCellValue('B'.$dataRow, $r->AccountName);
            $sheet->setCellValue('C'.$dataRow, $r->Type);
            $sheet->setCellValue('D'.$dataRow, $r->TotalDr);
            $sheet->setCellValue('E'.$dataRow, $r->TotalCr);
            $sheet->setCellValue('F'.$dataRow, $r->Balance);

            $hex = $r->Balance >= 0 ? '15803d' : 'b91c1c';
            $sheet->getStyle('F'.$dataRow)->getFont()->getColor()->setRGB($hex);
            $dataRow++;
        }

        // Totals
        $sheet->setCellValue('A'.$dataRow, 'TOTALS');
        $sheet->setCellValue('D'.$dataRow, $totalDr);
        $sheet->setCellValue('E'.$dataRow, $totalCr);
        $sheet->getStyle('A'.$dataRow.':F'.$dataRow)->getFont()->setBold(true);

        $widths = ['A' => 12, 'B' => 30, 'C' => 14, 'D' => 18, 'E' => 18, 'F' => 18];
        foreach ($widths as $c => $w) {
            $sheet->getColumnDimension($c)->setWidth($w);
        }

        $this->buildExcelBorders($sheet, 6, $dataRow, 'F');
        $this->streamExcel($spreadsheet, 'trial-balance-'.now()->format('Ymd').'.xlsx');
    }

    // ════════════════════════════════════════════════════════════════════════
    // REPORT 2 — GL STATEMENT
    // ════════════════════════════════════════════════════════════════════════

    // AJAX — load GL accounts for dropdown
    public function glAccounts()
    {
        $accounts = DB::table('ledger_account')
            ->where('Type', 'GL')
            ->where('Status', 1)
            ->orderBy('AccountName')
            ->get(['AccountNo', 'AccountName']);

        return response()->json($accounts);
    }

    private function buildGlStatement(
        int $accountNo,
        string $dateFrom,
        string $dateTo,
        string $branchID
    ): array {
        // Opening balance — all movements before dateFrom
        $openingQuery = DB::table('journal')
            ->where('AccountID', $accountNo)
            ->where('Status', 1)
            ->where('Reversed', 0)
            ->where('Date', '<', $dateFrom);

        if ($branchID !== 'ALL') {
            $openingQuery->where('BranchID', $branchID);
        }

        $openingDr = $openingQuery->sum('Dr');
        $openingCr = $openingQuery->sum('Cr');

        // Get account class
        $account = DB::table('ledger_account')
            ->where('AccountNo', $accountNo)
            ->first(['AccountName', 'Class', 'Type']);

        $openingBalance = $account?->Class === 'Dr'
            ? round($openingDr - $openingCr, 2)
            : round($openingCr - $openingDr, 2);

        // Period transactions
        $txQuery = DB::table('journal as j')
            ->leftJoin('ledger_account as sub', 'j.SubAccountID', '=', 'sub.AccountNo')
            ->where('j.AccountID', $accountNo)
            ->where('j.Status', 1)
            ->where('j.Reversed', 0)
            ->whereBetween('j.Date', [$dateFrom, $dateTo]);

        if ($branchID !== 'ALL') {
            $txQuery->where('j.BranchID', $branchID);
        }

        $transactions = $txQuery
            ->orderBy('j.Date')
            ->orderBy('j.Time')
            ->get([
                'j.ReceiptNo',
                'j.Date',
                'j.Description',
                'j.Mode',
                'j.Dr',
                'j.Cr',
                'j.Username',
                'sub.AccountName as SubAccountName',
            ]);

        // Add running balance
        $runningBalance = $openingBalance;
        $transactions = $transactions->map(function ($tx) use (&$runningBalance, $account) {
            $runningBalance += $account?->Class === 'Dr'
                ? ($tx->Dr - $tx->Cr)
                : ($tx->Cr - $tx->Dr);
            $tx->RunningBalance = round($runningBalance, 2);

            return $tx;
        });

        return [
            'account' => $account,
            'accountNo' => $accountNo,
            'openingBalance' => $openingBalance,
            'transactions' => $transactions,
            'closingBalance' => $runningBalance,
            'totalDr' => round($transactions->sum('Dr'), 2),
            'totalCr' => round($transactions->sum('Cr'), 2),
        ];
    }

    public function glStatementPrint(Request $request)
    {
        $request->validate([
            'account_no' => ['required', 'integer'],
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date'],
            'branch_id' => ['nullable', 'string'],
        ]);

        $user = Auth::user();
        $branchID = $request->branch_id ?? $user->BranchID;

        $data = $this->buildGlStatement(
            (int) $request->account_no,
            $request->date_from,
            $request->date_to,
            $branchID
        );
        $vision = $this->buildVisionProgress($branchID);
        $reportTitle = 'GL Statement — '.($data['account']?->AccountName ?? '');
        $dateFrom = \Carbon\Carbon::parse($request->date_from)->format('d M Y');
        $dateTo = \Carbon\Carbon::parse($request->date_to)->format('d M Y');

        return view('reports.accounting.gl-statement-print', compact(
            'data', 'vision', 'reportTitle', 'dateFrom', 'dateTo', 'branchID'
        ));
    }

    public function glStatementExport(Request $request)
    {
        $request->validate([
            'account_no' => ['required', 'integer'],
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date'],
            'branch_id' => ['nullable', 'string'],
        ]);

        $user = Auth::user();
        $branchID = $request->branch_id ?? $user->BranchID;
        $data = $this->buildGlStatement(
            (int) $request->account_no,
            $request->date_from,
            $request->date_to,
            $branchID
        );

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('GL Statement');

        $this->buildExcelHeader(
            $sheet,
            'GL Statement — '.($data['account']?->AccountName ?? ''),
            \Carbon\Carbon::parse($request->date_from)->format('d M Y'),
            \Carbon\Carbon::parse($request->date_to)->format('d M Y'),
            'G'
        );

        $headers = ['Date', 'Receipt No', 'Description', 'Sub Account', 'Dr (GH₵)', 'Cr (GH₵)', 'Balance (GH₵)'];
        $dataRow = $this->buildExcelColumnHeaders($sheet, 6, $headers);

        // Opening balance row
        $sheet->setCellValue('A'.$dataRow, 'Opening Balance');
        $sheet->setCellValue('G'.$dataRow, $data['openingBalance']);
        $sheet->getStyle('A'.$dataRow.':G'.$dataRow)->getFont()->setBold(true);
        $dataRow++;

        foreach ($data['transactions'] as $tx) {
            $sheet->setCellValue('A'.$dataRow, \Carbon\Carbon::parse($tx->Date)->format('d M Y'));
            $sheet->setCellValue('B'.$dataRow, $tx->ReceiptNo ?? '-');
            $sheet->setCellValue('C'.$dataRow, $tx->Description ?? '-');
            $sheet->setCellValue('D'.$dataRow, $tx->SubAccountName ?? '-');
            $sheet->setCellValue('E'.$dataRow, $tx->Dr);
            $sheet->setCellValue('F'.$dataRow, $tx->Cr);
            $sheet->setCellValue('G'.$dataRow, $tx->RunningBalance);

            $hex = $tx->RunningBalance >= 0 ? '15803d' : 'b91c1c';
            $sheet->getStyle('G'.$dataRow)->getFont()->getColor()->setRGB($hex);
            $dataRow++;
        }

        // Closing balance row
        $sheet->setCellValue('A'.$dataRow, 'Closing Balance');
        $sheet->setCellValue('E'.$dataRow, $data['totalDr']);
        $sheet->setCellValue('F'.$dataRow, $data['totalCr']);
        $sheet->setCellValue('G'.$dataRow, $data['closingBalance']);
        $sheet->getStyle('A'.$dataRow.':G'.$dataRow)->getFont()->setBold(true);

        $widths = ['A' => 14, 'B' => 16, 'C' => 30, 'D' => 24, 'E' => 16, 'F' => 16, 'G' => 18];
        foreach ($widths as $c => $w) {
            $sheet->getColumnDimension($c)->setWidth($w);
        }

        $this->buildExcelBorders($sheet, 6, $dataRow, 'G');
        $this->streamExcel($spreadsheet, 'gl-statement-'.now()->format('Ymd').'.xlsx');
    }

    // ════════════════════════════════════════════════════════════════════════
    // REPORT 3 — INCOME & EXPENDITURE STATEMENT
    // ════════════════════════════════════════════════════════════════════════

    private function buildIncomeExpenditure(
        string $dateFrom,
        string $dateTo,
        string $branchID
    ): array {
        $ieAccount = DB::table('active_ie')->first();
        if (! $ieAccount) {
            return ['income' => collect(), 'expenditure' => collect(),
                'totalIncome' => 0, 'totalExpenditure' => 0, 'netSurplus' => 0];
        }

        $baseQuery = fn () => DB::table('journal as j')
            ->join('ledger_account as la', 'j.SubAccountID', '=', 'la.AccountNo')
            ->where('j.AccountID', $ieAccount->AccountID)
            ->where('j.Status', 1)
            ->where('j.Reversed', 0)
            ->whereBetween('j.Date', [$dateFrom, $dateTo])
            ->when($branchID !== 'ALL', fn ($q) => $q->where('j.BranchID', $branchID))
            ->groupBy('j.SubAccountID', 'la.AccountName', 'la.Type');

        $income = $baseQuery()
            ->where('la.Type', 'INCOME')
            ->orderByRaw('SUM(j.Cr) DESC')
            ->get([
                'j.SubAccountID as AccountNo',
                'la.AccountName',
                DB::raw('ROUND(SUM(j.Cr), 2) as TotalCr'),
                DB::raw('ROUND(SUM(j.Dr), 2) as TotalDr'),
            ]);

        $expenditure = $baseQuery()
            ->where('la.Type', 'EXPENDITURE')
            ->orderByRaw('SUM(j.Dr) DESC')
            ->get([
                'j.SubAccountID as AccountNo',
                'la.AccountName',
                DB::raw('ROUND(SUM(j.Dr), 2) as TotalDr'),
                DB::raw('ROUND(SUM(j.Cr), 2) as TotalCr'),
            ]);

        $totalIncome = round($income->sum('TotalCr'), 2);
        $totalExpenditure = round($expenditure->sum('TotalDr'), 2);
        $netSurplus = round($totalIncome - $totalExpenditure, 2);

        return compact('income', 'expenditure', 'totalIncome', 'totalExpenditure', 'netSurplus');
    }

    public function incomeExpenditurePrint(Request $request)
    {
        $request->validate([
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date'],
            'branch_id' => ['nullable', 'string'],
        ]);

        $user = Auth::user();
        $branchID = $request->branch_id ?? $user->BranchID;

        $data = $this->buildIncomeExpenditure($request->date_from, $request->date_to, $branchID);
        $vision = $this->buildVisionProgress($branchID);
        $reportTitle = 'Income & Expenditure Statement';
        $dateFrom = \Carbon\Carbon::parse($request->date_from)->format('d M Y');
        $dateTo = \Carbon\Carbon::parse($request->date_to)->format('d M Y');

        return view('reports.accounting.income-expenditure-print', compact(
            'data', 'vision', 'reportTitle', 'dateFrom', 'dateTo', 'branchID'
        ));
    }

    public function incomeExpenditureExport(Request $request)
    {
        $request->validate([
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date'],
            'branch_id' => ['nullable', 'string'],
        ]);

        $user = Auth::user();
        $branchID = $request->branch_id ?? $user->BranchID;
        $data = $this->buildIncomeExpenditure($request->date_from, $request->date_to, $branchID);

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Income and Expenditure');

        $this->buildExcelHeader(
            $sheet, 'Income & Expenditure Statement',
            \Carbon\Carbon::parse($request->date_from)->format('d M Y'),
            \Carbon\Carbon::parse($request->date_to)->format('d M Y'),
            'C'
        );

        $dataRow = 6;
        // Income section
        $sheet->setCellValue('A'.$dataRow, 'INCOME');
        $sheet->getStyle('A'.$dataRow)->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A'.$dataRow.':C'.$dataRow)
            ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('eff6ff');
        $dataRow++;

        foreach ($data['income'] as $r) {
            $sheet->setCellValue('A'.$dataRow, $r->AccountNo);
            $sheet->setCellValue('B'.$dataRow, $r->AccountName);
            $sheet->setCellValue('C'.$dataRow, $r->TotalCr);
            $sheet->getStyle('C'.$dataRow)->getFont()->getColor()->setRGB('15803d');
            $dataRow++;
        }

        $sheet->setCellValue('B'.$dataRow, 'Total Income');
        $sheet->setCellValue('C'.$dataRow, $data['totalIncome']);
        $sheet->getStyle('A'.$dataRow.':C'.$dataRow)->getFont()->setBold(true);
        $dataRow += 2;

        // Expenditure section
        $sheet->setCellValue('A'.$dataRow, 'EXPENDITURE');
        $sheet->getStyle('A'.$dataRow)->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A'.$dataRow.':C'.$dataRow)
            ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('fef2f2');
        $dataRow++;

        foreach ($data['expenditure'] as $r) {
            $sheet->setCellValue('A'.$dataRow, $r->AccountNo);
            $sheet->setCellValue('B'.$dataRow, $r->AccountName);
            $sheet->setCellValue('C'.$dataRow, $r->TotalDr);
            $sheet->getStyle('C'.$dataRow)->getFont()->getColor()->setRGB('b91c1c');
            $dataRow++;
        }

        $sheet->setCellValue('B'.$dataRow, 'Total Expenditure');
        $sheet->setCellValue('C'.$dataRow, $data['totalExpenditure']);
        $sheet->getStyle('A'.$dataRow.':C'.$dataRow)->getFont()->setBold(true);
        $dataRow += 2;

        // Net surplus
        $sheet->setCellValue('B'.$dataRow, 'NET SURPLUS / (DEFICIT)');
        $sheet->setCellValue('C'.$dataRow, $data['netSurplus']);
        $sheet->getStyle('A'.$dataRow.':C'.$dataRow)->getFont()->setBold(true)->setSize(12);
        $hex = $data['netSurplus'] >= 0 ? '15803d' : 'b91c1c';
        $sheet->getStyle('C'.$dataRow)->getFont()->getColor()->setRGB($hex);

        $widths = ['A' => 12, 'B' => 35, 'C' => 20];
        foreach ($widths as $c => $w) {
            $sheet->getColumnDimension($c)->setWidth($w);
        }

        $this->streamExcel($spreadsheet, 'income-expenditure-'.now()->format('Ymd').'.xlsx');
    }

    // ════════════════════════════════════════════════════════════════════════
    // REPORT 4 — DAILY BALANCING SHEET
    // ════════════════════════════════════════════════════════════════════════

    private function buildDailyBalance(string $date, string $branchID): \Illuminate\Support\Collection
    {
        // Get all active bank/cash accounts
        $cashAccounts = DB::table('active_bank_cash as abc')
            ->join('ledger_account as la', 'abc.AccountID', '=', 'la.AccountNo')
            ->get(['la.AccountNo', 'la.AccountName', 'la.Class']);

        return $cashAccounts->map(function ($account) use ($date, $branchID) {
            $baseQuery = fn ($q) => $q
                ->where('AccountID', $account->AccountNo)
                ->where('Status', 1)
                ->where('Reversed', 0)
                ->when($branchID !== 'ALL', fn ($q) => $q->where('BranchID', $branchID));

            // Opening balance — all movements before selected date
            $openingDr = DB::table('journal')
                ->where('Date', '<', $date)
                ->tap($baseQuery)->sum('Dr');
            $openingCr = DB::table('journal')
                ->where('Date', '<', $date)
                ->tap($baseQuery)->sum('Cr');

            $openingBalance = $account->Class === 'Dr'
                ? round($openingDr - $openingCr, 2)
                : round($openingCr - $openingDr, 2);

            // Today's transactions
            $transactions = DB::table('journal')
                ->where('Date', $date)
                ->tap($baseQuery)
                ->orderBy('Time')
                ->get(['ReceiptNo', 'Description', 'Mode', 'Dr', 'Cr', 'Username', 'Date', 'Time']);

            $todayDr = round($transactions->sum('Dr'), 2);
            $todayCr = round($transactions->sum('Cr'), 2);

            $closingBalance = $account->Class === 'Dr'
                ? round($openingBalance + $todayDr - $todayCr, 2)
                : round($openingBalance + $todayCr - $todayDr, 2);

            return (object) [
                'AccountNo' => $account->AccountNo,
                'AccountName' => $account->AccountName,
                'Class' => $account->Class,
                'OpeningBalance' => $openingBalance,
                'TodayDr' => $todayDr,
                'TodayCr' => $todayCr,
                'ClosingBalance' => $closingBalance,
                'Transactions' => $transactions,
            ];
        });
    }

    public function dailyBalancePrint(Request $request)
    {
        $request->validate([
            'date' => ['required', 'date'],
            'branch_id' => ['nullable', 'string'],
        ]);

        $user = Auth::user();
        $date = $request->date;
        $branchID = $request->branch_id ?? $user->BranchID;

        $accounts = $this->buildDailyBalance($date, $branchID);
        $reportTitle = 'Daily Balancing Sheet';
        $dateFormatted = \Carbon\Carbon::parse($date)->format('d M Y');

        return view('reports.accounting.daily-balance-print', compact(
            'accounts', 'reportTitle', 'dateFormatted', 'branchID'
        ));
    }

    public function dailyBalanceExport(Request $request)
    {
        $request->validate([
            'date' => ['required', 'date'],
            'branch_id' => ['nullable', 'string'],
        ]);

        $user = Auth::user();
        $date = $request->date;
        $branchID = $request->branch_id ?? $user->BranchID;
        $accounts = $this->buildDailyBalance($date, $branchID);

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Daily Balance');

        $this->buildExcelHeader(
            $sheet, 'Daily Balancing Sheet — '.
            \Carbon\Carbon::parse($date)->format('d M Y'),
            '', '', 'D'
        );

        $dataRow = 6;
        foreach ($accounts as $account) {
            // Account header
            $sheet->setCellValue('A'.$dataRow, $account->AccountName);
            $sheet->getStyle('A'.$dataRow)->getFont()->setBold(true)->setSize(11);
            $dataRow++;

            $sheet->setCellValue('A'.$dataRow, 'Opening Balance');
            $sheet->setCellValue('D'.$dataRow, $account->OpeningBalance);
            $sheet->getStyle('D'.$dataRow)->getFont()->setBold(true);
            $dataRow++;

            foreach ($account->Transactions as $tx) {
                $sheet->setCellValue('A'.$dataRow, \Carbon\Carbon::parse($tx->Date)->format('d M Y'));
                $sheet->setCellValue('B'.$dataRow, $tx->ReceiptNo ?? '-');
                $sheet->setCellValue('C'.$dataRow, $tx->Description ?? '-');
                $sheet->setCellValue('D'.$dataRow, $tx->Mode === 'Dr' ? $tx->Dr : -$tx->Cr);
                $dataRow++;
            }

            $sheet->setCellValue('A'.$dataRow, 'Closing Balance');
            $sheet->setCellValue('D'.$dataRow, $account->ClosingBalance);
            $sheet->getStyle('A'.$dataRow.':D'.$dataRow)->getFont()->setBold(true);
            $hex = $account->ClosingBalance >= 0 ? '15803d' : 'b91c1c';
            $sheet->getStyle('D'.$dataRow)->getFont()->getColor()->setRGB($hex);
            $dataRow += 2;
        }

        $widths = ['A' => 14, 'B' => 16, 'C' => 35, 'D' => 18];
        foreach ($widths as $c => $w) {
            $sheet->getColumnDimension($c)->setWidth($w);
        }

        $this->streamExcel($spreadsheet, 'daily-balance-'.now()->format('Ymd').'.xlsx');
    }

    // ════════════════════════════════════════════════════════════════════════
    // REPORT 5 — WASTE SHEET
    // ════════════════════════════════════════════════════════════════════════

    private function buildWasteSheet(
        string $dateFrom,
        string $dateTo,
        string $branchID,
        ?string $username = null
    ) {
        $query = DB::table('journal as j')
            ->join('ledger_account as la', 'j.AccountID', '=', 'la.AccountNo')
            ->where('j.Reversed', 1)
            ->whereBetween('j.Date', [$dateFrom, $dateTo])
            ->when($branchID !== 'ALL', fn ($q) => $q->where('j.BranchID', $branchID))
            ->when($username, fn ($q) => $q->where('j.ReversedBy', $username))
            ->orderBy('j.ReversedAt', 'desc')
            ->get([
                'j.ReceiptNo',
                'j.Date',
                'j.Description',
                'j.Dr',
                'j.Cr',
                'j.Mode',
                'j.Username',
                'j.ReversedBy',
                'j.ReversedAt',
                'la.AccountName',
                'la.Type',
            ]);

        $totals = [
            'dr' => round($query->sum('Dr'), 2),
            'cr' => round($query->sum('Cr'), 2),
            'count' => $query->count(),
        ];

        return compact('query', 'totals');
    }

    public function wasteSheetPrint(Request $request)
    {
        $request->validate([
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date'],
            'branch_id' => ['nullable', 'string'],
            'username' => ['nullable', 'string', 'max:20'],
        ]);

        $user = Auth::user();
        $branchID = $request->branch_id ?? $user->BranchID;

        $data = $this->buildWasteSheet(
            $request->date_from, $request->date_to,
            $branchID, $request->username
        );
        $reportTitle = 'Waste Sheet — Reversal Audit';
        $dateFrom = \Carbon\Carbon::parse($request->date_from)->format('d M Y');
        $dateTo = \Carbon\Carbon::parse($request->date_to)->format('d M Y');

        return view('reports.accounting.waste-sheet-print', compact(
            'data', 'reportTitle', 'dateFrom', 'dateTo', 'branchID'
        ));
    }

    public function wasteSheetExport(Request $request)
    {
        $request->validate([
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date'],
            'branch_id' => ['nullable', 'string'],
            'username' => ['nullable', 'string'],
        ]);

        $user = Auth::user();
        $branchID = $request->branch_id ?? $user->BranchID;
        $data = $this->buildWasteSheet(
            $request->date_from, $request->date_to,
            $branchID, $request->username
        );

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Waste Sheet');

        $this->buildExcelHeader(
            $sheet, 'Waste Sheet — Reversal Audit',
            \Carbon\Carbon::parse($request->date_from)->format('d M Y'),
            \Carbon\Carbon::parse($request->date_to)->format('d M Y'),
            'I'
        );

        $headers = ['Date', 'Receipt No', 'Account', 'Description', 'Dr', 'Cr', 'Posted By', 'Reversed By', 'Reversed At'];
        $dataRow = $this->buildExcelColumnHeaders($sheet, 6, $headers);

        foreach ($data['query'] as $r) {
            $sheet->setCellValue('A'.$dataRow, \Carbon\Carbon::parse($r->Date)->format('d M Y'));
            $sheet->setCellValue('B'.$dataRow, $r->ReceiptNo ?? '-');
            $sheet->setCellValue('C'.$dataRow, $r->AccountName ?? '-');
            $sheet->setCellValue('D'.$dataRow, $r->Description ?? '-');
            $sheet->setCellValue('E'.$dataRow, $r->Dr);
            $sheet->setCellValue('F'.$dataRow, $r->Cr);
            $sheet->setCellValue('G'.$dataRow, $r->Username ?? '-');
            $sheet->setCellValue('H'.$dataRow, $r->ReversedBy ?? '-');
            $sheet->setCellValue('I'.$dataRow, $r->ReversedAt
                ? \Carbon\Carbon::parse($r->ReversedAt)->format('d M Y h:i A') : '-');
            $sheet->getStyle('A'.$dataRow.':I'.$dataRow)
                ->getFont()->getColor()->setRGB('b91c1c');
            $dataRow++;
        }

        $sheet->setCellValue('A'.$dataRow, 'TOTALS — '.$data['totals']['count'].' reversals');
        $sheet->setCellValue('E'.$dataRow, $data['totals']['dr']);
        $sheet->setCellValue('F'.$dataRow, $data['totals']['cr']);
        $sheet->getStyle('A'.$dataRow.':I'.$dataRow)->getFont()->setBold(true);

        $widths = ['A' => 14, 'B' => 16, 'C' => 22, 'D' => 28, 'E' => 14, 'F' => 14, 'G' => 14, 'H' => 14, 'I' => 20];
        foreach ($widths as $c => $w) {
            $sheet->getColumnDimension($c)->setWidth($w);
        }

        $this->buildExcelBorders($sheet, 6, $dataRow, 'I');
        $this->streamExcel($spreadsheet, 'waste-sheet-'.now()->format('Ymd').'.xlsx');
    }

    // ════════════════════════════════════════════════════════════════════════
    // REPORT 6 — RECEIPT REGISTER
    // ════════════════════════════════════════════════════════════════════════

    private function buildReceiptRegister(
        string $dateFrom,
        string $dateTo,
        string $branchID,
        ?string $username = null
    ) {
        $query = DB::table('journal as j')
            ->join('ledger_account as la', 'j.AccountID', '=', 'la.AccountNo')
            ->where('j.Status', 1)
            ->where('j.Reversed', 0)
            ->whereBetween('j.Date', [$dateFrom, $dateTo])
            ->when($branchID !== 'ALL', fn ($q) => $q->where('j.BranchID', $branchID))
            ->when($username, fn ($q) => $q->where('j.Username', $username))
            ->groupBy('j.ReceiptNo', 'j.Date', 'j.Username', 'j.BranchID', 'la.AccountName')
            ->orderBy('j.Date', 'asc')
            ->orderBy('j.ReceiptNo', 'asc')
            ->get([
                'j.ReceiptNo',
                'j.Date',
                'j.Username',
                'la.AccountName',
                DB::raw('ROUND(SUM(j.Dr), 2) as TotalDr'),
                DB::raw('ROUND(SUM(j.Cr), 2) as TotalCr'),
                DB::raw('COUNT(*) as LineCount'),
            ]);

        $totals = [
            'dr' => round($query->sum('TotalDr'), 2),
            'cr' => round($query->sum('TotalCr'), 2),
            'count' => $query->count(),
        ];

        return compact('query', 'totals');
    }

    public function receiptRegisterPrint(Request $request)
    {
        $request->validate([
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date'],
            'branch_id' => ['nullable', 'string'],
            'username' => ['nullable', 'string'],
        ]);

        $user = Auth::user();
        $branchID = $request->branch_id ?? $user->BranchID;

        $data = $this->buildReceiptRegister(
            $request->date_from, $request->date_to,
            $branchID, $request->username
        );
        $reportTitle = 'Receipt Register';
        $dateFrom = \Carbon\Carbon::parse($request->date_from)->format('d M Y');
        $dateTo = \Carbon\Carbon::parse($request->date_to)->format('d M Y');

        return view('reports.accounting.receipt-register-print', compact(
            'data', 'reportTitle', 'dateFrom', 'dateTo', 'branchID'
        ));
    }

    public function receiptRegisterExport(Request $request)
    {
        $request->validate([
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date'],
            'branch_id' => ['nullable', 'string'],
            'username' => ['nullable', 'string'],
        ]);

        $user = Auth::user();
        $branchID = $request->branch_id ?? $user->BranchID;
        $data = $this->buildReceiptRegister(
            $request->date_from, $request->date_to,
            $branchID, $request->username
        );

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Receipt Register');

        $this->buildExcelHeader(
            $sheet, 'Receipt Register',
            \Carbon\Carbon::parse($request->date_from)->format('d M Y'),
            \Carbon\Carbon::parse($request->date_to)->format('d M Y'),
            'F'
        );

        $headers = ['#', 'Date', 'Receipt No', 'Account', 'Dr (GH₵)', 'Cr (GH₵)', 'Lines', 'Officer'];
        $dataRow = $this->buildExcelColumnHeaders($sheet, 6, $headers);

        foreach ($data['query'] as $i => $r) {
            $sheet->setCellValue('A'.$dataRow, $i + 1);
            $sheet->setCellValue('B'.$dataRow, \Carbon\Carbon::parse($r->Date)->format('d M Y'));
            $sheet->setCellValue('C'.$dataRow, $r->ReceiptNo ?? '-');
            $sheet->setCellValue('D'.$dataRow, $r->AccountName ?? '-');
            $sheet->setCellValue('E'.$dataRow, $r->TotalDr);
            $sheet->setCellValue('F'.$dataRow, $r->TotalCr);
            $sheet->setCellValue('G'.$dataRow, $r->Lines);
            $sheet->setCellValue('H'.$dataRow, $r->Username ?? '-');
            $dataRow++;
        }

        $sheet->setCellValue('A'.$dataRow, 'TOTALS — '.$data['totals']['count'].' receipts');
        $sheet->setCellValue('E'.$dataRow, $data['totals']['dr']);
        $sheet->setCellValue('F'.$dataRow, $data['totals']['cr']);
        $sheet->getStyle('A'.$dataRow.':H'.$dataRow)->getFont()->setBold(true);

        $widths = ['A' => 5, 'B' => 14, 'C' => 16, 'D' => 26, 'E' => 16, 'F' => 16, 'G' => 8, 'H' => 14];
        foreach ($widths as $c => $w) {
            $sheet->getColumnDimension($c)->setWidth($w);
        }

        $this->buildExcelBorders($sheet, 6, $dataRow, 'H');
        $this->streamExcel($spreadsheet, 'receipt-register-'.now()->format('Ymd').'.xlsx');
    }

    // ════════════════════════════════════════════════════════════════════════
    // REPORT 7 — ACCOUNT ACTIVITY
    // ════════════════════════════════════════════════════════════════════════

    // AJAX — all accounts by type
    public function allAccounts(Request $request)
    {
        $type = $request->type ?? 'GL';

        $accounts = DB::table('ledger_account')
            ->where('Status', 1)
            ->when($type !== 'ALL', fn ($q) => $q->where('Type', $type))
            ->orderBy('AccountName')
            ->get(['AccountNo', 'AccountName', 'Type']);

        return response()->json($accounts);
    }

    private function buildAccountActivity(
        int $accountNo,
        string $dateFrom,
        string $dateTo,
        string $branchID,
        string $accountType
    ): array {
        $account = DB::table('ledger_account')
            ->where('AccountNo', $accountNo)
            ->first(['AccountName', 'Class', 'Type']);

        $ieAccount = DB::table('active_ie')->first();

        // For GL accounts — query journal.AccountID directly
        // For Income/Expenditure — query journal where AccountID = IE and SubAccountID = this account
        if ($accountType === 'GL') {
            $baseQuery = DB::table('journal as j')
                ->where('j.AccountID', $accountNo)
                ->where('j.Status', 1)
                ->where('j.Reversed', 0)
                ->when($branchID !== 'ALL', fn ($q) => $q->where('j.BranchID', $branchID));

            $openingDr = (clone $baseQuery)->where('j.Date', '<', $dateFrom)->sum('j.Dr');
            $openingCr = (clone $baseQuery)->where('j.Date', '<', $dateFrom)->sum('j.Cr');

            $transactions = (clone $baseQuery)
                ->whereBetween('j.Date', [$dateFrom, $dateTo])
                ->leftJoin('ledger_account as sub', 'j.SubAccountID', '=', 'sub.AccountNo')
                ->orderBy('j.Date')->orderBy('j.Time')
                ->get([
                    'j.ReceiptNo', 'j.Date', 'j.Description',
                    'j.Mode', 'j.Dr', 'j.Cr', 'j.Username',
                    'sub.AccountName as SubAccountName',
                ]);
        } else {
            // Income or Expenditure
            if (! $ieAccount) {
                return ['account' => $account, 'accountNo' => $accountNo,
                    'openingBalance' => 0, 'transactions' => collect(),
                    'closingBalance' => 0, 'totalDr' => 0, 'totalCr' => 0];
            }

            $baseQuery = DB::table('journal as j')
                ->where('j.AccountID', $ieAccount->AccountID)
                ->where('j.SubAccountID', $accountNo)
                ->where('j.Status', 1)
                ->where('j.Reversed', 0)
                ->when($branchID !== 'ALL', fn ($q) => $q->where('j.BranchID', $branchID));

            $openingDr = (clone $baseQuery)->where('j.Date', '<', $dateFrom)->sum('j.Dr');
            $openingCr = (clone $baseQuery)->where('j.Date', '<', $dateFrom)->sum('j.Cr');

            $transactions = (clone $baseQuery)
                ->whereBetween('j.Date', [$dateFrom, $dateTo])
                ->orderBy('j.Date')->orderBy('j.Time')
                ->get([
                    'j.ReceiptNo', 'j.Date', 'j.Description',
                    'j.Mode', 'j.Dr', 'j.Cr', 'j.Username',
                ]);
        }

        $openingBalance = $account?->Class === 'Dr'
            ? round($openingDr - $openingCr, 2)
            : round($openingCr - $openingDr, 2);

        $runningBalance = $openingBalance;
        $transactions = $transactions->map(function ($tx) use (&$runningBalance, $account) {
            $runningBalance += $account?->Class === 'Dr'
                ? ($tx->Dr - $tx->Cr)
                : ($tx->Cr - $tx->Dr);
            $tx->RunningBalance = round($runningBalance, 2);

            return $tx;
        });

        return [
            'account' => $account,
            'accountNo' => $accountNo,
            'openingBalance' => $openingBalance,
            'transactions' => $transactions,
            'closingBalance' => $runningBalance,
            'totalDr' => round($transactions->sum('Dr'), 2),
            'totalCr' => round($transactions->sum('Cr'), 2),
        ];
    }

    public function accountActivityPrint(Request $request)
    {
        $request->validate([
            'account_no' => ['required', 'integer'],
            'account_type' => ['required', 'string', 'in:GL,INCOME,EXPENDITURE'],
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date'],
            'branch_id' => ['nullable', 'string'],
        ]);

        $user = Auth::user();
        $branchID = $request->branch_id ?? $user->BranchID;

        $data = $this->buildAccountActivity(
            (int) $request->account_no,
            $request->date_from,
            $request->date_to,
            $branchID,
            $request->account_type
        );
        $reportTitle = 'Account Activity — '.($data['account']?->AccountName ?? '');
        $dateFrom = \Carbon\Carbon::parse($request->date_from)->format('d M Y');
        $dateTo = \Carbon\Carbon::parse($request->date_to)->format('d M Y');

        return view('reports.accounting.account-activity-print', compact(
            'data', 'reportTitle', 'dateFrom', 'dateTo', 'branchID'
        ));
    }

    public function accountActivityExport(Request $request)
    {
        $request->validate([
            'account_no' => ['required', 'integer'],
            'account_type' => ['required', 'string', 'in:GL,INCOME,EXPENDITURE'],
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date'],
            'branch_id' => ['nullable', 'string'],
        ]);

        $user = Auth::user();
        $branchID = $request->branch_id ?? $user->BranchID;
        $data = $this->buildAccountActivity(
            (int) $request->account_no,
            $request->date_from,
            $request->date_to,
            $branchID,
            $request->account_type
        );

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Account Activity');

        $this->buildExcelHeader(
            $sheet,
            'Account Activity — '.($data['account']?->AccountName ?? ''),
            \Carbon\Carbon::parse($request->date_from)->format('d M Y'),
            \Carbon\Carbon::parse($request->date_to)->format('d M Y'),
            'G'
        );

        $headers = ['Date', 'Receipt No', 'Description', 'Sub Account', 'Dr (GH₵)', 'Cr (GH₵)', 'Balance (GH₵)'];
        $dataRow = $this->buildExcelColumnHeaders($sheet, 6, $headers);

        $sheet->setCellValue('A'.$dataRow, 'Opening Balance');
        $sheet->setCellValue('G'.$dataRow, $data['openingBalance']);
        $sheet->getStyle('A'.$dataRow.':G'.$dataRow)->getFont()->setBold(true);
        $dataRow++;

        foreach ($data['transactions'] as $tx) {
            $sheet->setCellValue('A'.$dataRow, \Carbon\Carbon::parse($tx->Date)->format('d M Y'));
            $sheet->setCellValue('B'.$dataRow, $tx->ReceiptNo ?? '-');
            $sheet->setCellValue('C'.$dataRow, $tx->Description ?? '-');
            $sheet->setCellValue('D'.$dataRow, $tx->SubAccountName ?? '-');
            $sheet->setCellValue('E'.$dataRow, $tx->Dr);
            $sheet->setCellValue('F'.$dataRow, $tx->Cr);
            $sheet->setCellValue('G'.$dataRow, $tx->RunningBalance);
            $hex = $tx->RunningBalance >= 0 ? '15803d' : 'b91c1c';
            $sheet->getStyle('G'.$dataRow)->getFont()->getColor()->setRGB($hex);
            $dataRow++;
        }

        $sheet->setCellValue('A'.$dataRow, 'Closing Balance');
        $sheet->setCellValue('E'.$dataRow, $data['totalDr']);
        $sheet->setCellValue('F'.$dataRow, $data['totalCr']);
        $sheet->setCellValue('G'.$dataRow, $data['closingBalance']);
        $sheet->getStyle('A'.$dataRow.':G'.$dataRow)->getFont()->setBold(true);

        $widths = ['A' => 14, 'B' => 16, 'C' => 30, 'D' => 24, 'E' => 16, 'F' => 16, 'G' => 18];
        foreach ($widths as $c => $w) {
            $sheet->getColumnDimension($c)->setWidth($w);
        }

        $this->buildExcelBorders($sheet, 6, $dataRow, 'G');
        $this->streamExcel($spreadsheet, 'account-activity-'.now()->format('Ymd').'.xlsx');
    }

    // ════════════════════════════════════════════════════════════════════════
    // REPORT 8 — BALANCE SHEET
    // ════════════════════════════════════════════════════════════════════════

    private function buildBalanceSheet(string $asAt, string $branchID): array
    {
        $allowedRestricted = $this->allowedRestricted();

        $rows = DB::table('journal as j')
            ->join('ledger_account as la', 'j.AccountID', '=', 'la.AccountNo')
            ->where('j.Reversed', 0)
            ->where('j.Status', 1)
            ->where('j.Date', '<=', $asAt)
            ->where('la.Type', 'GL')
            ->whereIn('j.Restricted', $allowedRestricted)
            ->when($branchID !== 'ALL', fn ($q) => $q->where('j.BranchID', $branchID))
            ->groupBy('j.AccountID', 'la.AccountName', 'la.Class', 'la.Type')
            ->orderBy('la.AccountName')
            ->get([
                'j.AccountID',
                'la.AccountName',
                'la.Class',
                DB::raw('ROUND(SUM(j.Dr), 2) as TotalDr'),
                DB::raw('ROUND(SUM(j.Cr), 2) as TotalCr'),
            ])
            ->map(function ($row) {
                $row->NetBalance = $row->Class === 'Dr'
                    ? round($row->TotalDr - $row->TotalCr, 2)
                    : round($row->TotalCr - $row->TotalDr, 2);

                return $row;
            });

        $assets = $rows->where('Class', 'Dr')->values();
        $liabilities = $rows->where('Class', 'Cr')->values();
        $totalAssets = round($assets->sum('NetBalance'), 2);
        $totalLiabilities = round($liabilities->sum('NetBalance'), 2);
        $difference = round($totalAssets - $totalLiabilities, 2);

        return compact('assets', 'liabilities', 'totalAssets', 'totalLiabilities', 'difference');
    }

    public function balanceSheetPrint(Request $request)
    {
        $request->validate([
            'as_at' => ['required', 'date'],
            'branch_id' => ['nullable', 'string'],
        ]);

        $user = Auth::user();
        $asAt = $request->as_at;
        $branchID = $request->branch_id ?? $user->BranchID;
        $data = $this->buildBalanceSheet($asAt, $branchID);
        $vision = $this->buildVisionProgress($branchID);
        $reportTitle = 'Balance Sheet';
        $asAtFormatted = \Carbon\Carbon::parse($asAt)->format('d M Y');

        return view('reports.accounting.balance-sheet-print', compact(
            'data', 'vision', 'reportTitle', 'asAtFormatted', 'branchID'
        ));
    }

    public function balanceSheetExport(Request $request)
    {
        $request->validate([
            'as_at' => ['required', 'date'],
            'branch_id' => ['nullable', 'string'],
        ]);

        $user = Auth::user();
        $asAt = $request->as_at;
        $branchID = $request->branch_id ?? $user->BranchID;
        $data = $this->buildBalanceSheet($asAt, $branchID);

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Balance Sheet');

        $this->buildExcelHeader(
            $sheet,
            'Balance Sheet — As At '.\Carbon\Carbon::parse($asAt)->format('d M Y'),
            '', '', 'C'
        );

        $dataRow = 6;

        // ── Assets ───────────────────────────────────────────────────────────
        $sheet->setCellValue('A'.$dataRow, 'ASSETS');
        $sheet->getStyle('A'.$dataRow)->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A'.$dataRow.':C'.$dataRow)
            ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('eff6ff');
        $dataRow++;

        foreach ($data['assets'] as $r) {
            $sheet->setCellValue('A'.$dataRow, $r->AccountID);
            $sheet->setCellValue('B'.$dataRow, $r->AccountName);
            $sheet->setCellValue('C'.$dataRow, $r->NetBalance);
            $sheet->getStyle('C'.$dataRow)->getFont()->getColor()->setRGB($r->NetBalance >= 0 ? '15803d' : 'b91c1c');
            $dataRow++;
        }

        $sheet->setCellValue('B'.$dataRow, 'TOTAL ASSETS');
        $sheet->setCellValue('C'.$dataRow, $data['totalAssets']);
        $sheet->getStyle('A'.$dataRow.':C'.$dataRow)->getFont()->setBold(true);
        $dataRow += 2;

        // ── Liabilities & Equity ─────────────────────────────────────────────
        $sheet->setCellValue('A'.$dataRow, 'LIABILITIES & EQUITY');
        $sheet->getStyle('A'.$dataRow)->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A'.$dataRow.':C'.$dataRow)
            ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('fef2f2');
        $dataRow++;

        foreach ($data['liabilities'] as $r) {
            $sheet->setCellValue('A'.$dataRow, $r->AccountID);
            $sheet->setCellValue('B'.$dataRow, $r->AccountName);
            $sheet->setCellValue('C'.$dataRow, $r->NetBalance);
            $sheet->getStyle('C'.$dataRow)->getFont()->getColor()->setRGB($r->NetBalance >= 0 ? '15803d' : 'b91c1c');
            $dataRow++;
        }

        $sheet->setCellValue('B'.$dataRow, 'TOTAL LIABILITIES & EQUITY');
        $sheet->setCellValue('C'.$dataRow, $data['totalLiabilities']);
        $sheet->getStyle('A'.$dataRow.':C'.$dataRow)->getFont()->setBold(true);
        $dataRow += 2;

        // ── Difference ───────────────────────────────────────────────────────
        $sheet->setCellValue('B'.$dataRow, 'DIFFERENCE (should be zero)');
        $sheet->setCellValue('C'.$dataRow, $data['difference']);
        $sheet->getStyle('A'.$dataRow.':C'.$dataRow)->getFont()->setBold(true);
        $sheet->getStyle('C'.$dataRow)->getFont()->getColor()->setRGB(abs($data['difference']) < 0.01 ? '15803d' : 'b91c1c');

        $widths = ['A' => 12, 'B' => 38, 'C' => 20];
        foreach ($widths as $c => $w) {
            $sheet->getColumnDimension($c)->setWidth($w);
        }

        $this->streamExcel($spreadsheet, 'balance-sheet-'.now()->format('Ymd').'.xlsx');
    }

    // ════════════════════════════════════════════════════════════════════════
    // REPORT 9 — CASH FLOW STATEMENT
    // ════════════════════════════════════════════════════════════════════════

    private function buildCashFlow(string $dateFrom, string $dateTo, string $branchID): array
    {
        $cashAccountIDs = DB::table('active_bank_cash as abc')
            ->join('ledger_account as la', 'abc.AccountID', '=', 'la.AccountNo')
            ->pluck('la.AccountNo')
            ->toArray();

        if (empty($cashAccountIDs)) {
            return ['accounts' => collect(), 'grandInflows' => 0, 'grandOutflows' => 0, 'netMovement' => 0];
        }

        $accounts = collect();
        $grandInflows = 0;
        $grandOutflows = 0;

        foreach ($cashAccountIDs as $accountNo) {
            $account = DB::table('ledger_account')
                ->where('AccountNo', $accountNo)
                ->first(['AccountNo', 'AccountName']);

            if (! $account) {
                continue;
            }

            $baseQuery = fn () => DB::table('journal as j')
                ->leftJoin('ledger_account as sub', 'j.SubAccountID', '=', 'sub.AccountNo')
                ->where('j.AccountID', $accountNo)
                ->where('j.Reversed', 0)
                ->where('j.Status', 1)
                ->whereBetween('j.Date', [$dateFrom, $dateTo])
                ->when($branchID !== 'ALL', fn ($q) => $q->where('j.BranchID', $branchID))
                ->groupBy('j.SubAccountID', 'sub.AccountName');

            $inflows = $baseQuery()
                ->where('j.Dr', '>', 0)
                ->orderByRaw('SUM(j.Dr) DESC')
                ->get([
                    'j.SubAccountID',
                    'sub.AccountName as SubAccountName',
                    DB::raw('ROUND(SUM(j.Dr), 2) as Amount'),
                ]);

            $outflows = $baseQuery()
                ->where('j.Cr', '>', 0)
                ->orderByRaw('SUM(j.Cr) DESC')
                ->get([
                    'j.SubAccountID',
                    'sub.AccountName as SubAccountName',
                    DB::raw('ROUND(SUM(j.Cr), 2) as Amount'),
                ]);

            $totalInflows = round($inflows->sum('Amount'), 2);
            $totalOutflows = round($outflows->sum('Amount'), 2);
            $grandInflows += $totalInflows;
            $grandOutflows += $totalOutflows;

            $accounts->push((object) [
                'AccountNo' => $account->AccountNo,
                'AccountName' => $account->AccountName,
                'Inflows' => $inflows,
                'Outflows' => $outflows,
                'TotalInflows' => $totalInflows,
                'TotalOutflows' => $totalOutflows,
                'NetMovement' => round($totalInflows - $totalOutflows, 2),
            ]);
        }

        return [
            'accounts' => $accounts,
            'grandInflows' => round($grandInflows, 2),
            'grandOutflows' => round($grandOutflows, 2),
            'netMovement' => round($grandInflows - $grandOutflows, 2),
        ];
    }

    public function cashFlowPrint(Request $request)
    {
        $request->validate([
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date'],
            'branch_id' => ['nullable', 'string'],
        ]);

        $user = Auth::user();
        $branchID = $request->branch_id ?? $user->BranchID;
        $data = $this->buildCashFlow($request->date_from, $request->date_to, $branchID);
        $reportTitle = 'Cash Flow Statement';
        $dateFrom = \Carbon\Carbon::parse($request->date_from)->format('d M Y');
        $dateTo = \Carbon\Carbon::parse($request->date_to)->format('d M Y');

        return view('reports.accounting.cash-flow-print', compact(
            'data', 'reportTitle', 'dateFrom', 'dateTo', 'branchID'
        ));
    }

    public function cashFlowExport(Request $request)
    {
        $request->validate([
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date'],
            'branch_id' => ['nullable', 'string'],
        ]);

        $user = Auth::user();
        $branchID = $request->branch_id ?? $user->BranchID;
        $data = $this->buildCashFlow($request->date_from, $request->date_to, $branchID);

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Cash Flow');

        $this->buildExcelHeader(
            $sheet, 'Cash Flow Statement',
            \Carbon\Carbon::parse($request->date_from)->format('d M Y'),
            \Carbon\Carbon::parse($request->date_to)->format('d M Y'),
            'C'
        );

        $dataRow = 6;

        foreach ($data['accounts'] as $account) {
            $sheet->setCellValue('A'.$dataRow, $account->AccountName);
            $sheet->getStyle('A'.$dataRow)->getFont()->setBold(true)->setSize(11);
            $sheet->getStyle('A'.$dataRow.':C'.$dataRow)
                ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setRGB('eff6ff');
            $dataRow++;

            $sheet->setCellValue('A'.$dataRow, 'INFLOWS');
            $sheet->getStyle('A'.$dataRow)->getFont()->setBold(true);
            $dataRow++;

            foreach ($account->Inflows as $r) {
                $sheet->setCellValue('B'.$dataRow, $r->SubAccountName ?? 'Account #'.$r->SubAccountID);
                $sheet->setCellValue('C'.$dataRow, $r->Amount);
                $sheet->getStyle('C'.$dataRow)->getFont()->getColor()->setRGB('15803d');
                $dataRow++;
            }

            $sheet->setCellValue('B'.$dataRow, 'Total Inflows');
            $sheet->setCellValue('C'.$dataRow, $account->TotalInflows);
            $sheet->getStyle('A'.$dataRow.':C'.$dataRow)->getFont()->setBold(true);
            $dataRow++;

            $sheet->setCellValue('A'.$dataRow, 'OUTFLOWS');
            $sheet->getStyle('A'.$dataRow)->getFont()->setBold(true);
            $dataRow++;

            foreach ($account->Outflows as $r) {
                $sheet->setCellValue('B'.$dataRow, $r->SubAccountName ?? 'Account #'.$r->SubAccountID);
                $sheet->setCellValue('C'.$dataRow, $r->Amount);
                $sheet->getStyle('C'.$dataRow)->getFont()->getColor()->setRGB('b91c1c');
                $dataRow++;
            }

            $sheet->setCellValue('B'.$dataRow, 'Total Outflows');
            $sheet->setCellValue('C'.$dataRow, $account->TotalOutflows);
            $sheet->getStyle('A'.$dataRow.':C'.$dataRow)->getFont()->setBold(true);
            $dataRow++;

            $sheet->setCellValue('B'.$dataRow, 'Net Movement');
            $sheet->setCellValue('C'.$dataRow, $account->NetMovement);
            $sheet->getStyle('A'.$dataRow.':C'.$dataRow)->getFont()->setBold(true)->setSize(11);
            $sheet->getStyle('C'.$dataRow)->getFont()->getColor()
                ->setRGB($account->NetMovement >= 0 ? '15803d' : 'b91c1c');
            $dataRow += 2;
        }

        // Grand totals
        $sheet->setCellValue('B'.$dataRow, 'TOTAL INFLOWS (ALL ACCOUNTS)');
        $sheet->setCellValue('C'.$dataRow, $data['grandInflows']);
        $sheet->getStyle('A'.$dataRow.':C'.$dataRow)->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('C'.$dataRow)->getFont()->getColor()->setRGB('15803d');
        $dataRow++;

        $sheet->setCellValue('B'.$dataRow, 'TOTAL OUTFLOWS (ALL ACCOUNTS)');
        $sheet->setCellValue('C'.$dataRow, $data['grandOutflows']);
        $sheet->getStyle('A'.$dataRow.':C'.$dataRow)->getFont()->setBold(true);
        $sheet->getStyle('C'.$dataRow)->getFont()->getColor()->setRGB('b91c1c');
        $dataRow++;

        $sheet->setCellValue('B'.$dataRow, 'NET CASH MOVEMENT');
        $sheet->setCellValue('C'.$dataRow, $data['netMovement']);
        $sheet->getStyle('A'.$dataRow.':C'.$dataRow)->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('C'.$dataRow)->getFont()->getColor()
            ->setRGB($data['netMovement'] >= 0 ? '15803d' : 'b91c1c');

        $widths = ['A' => 24, 'B' => 36, 'C' => 20];
        foreach ($widths as $c => $w) {
            $sheet->getColumnDimension($c)->setWidth($w);
        }

        $this->streamExcel($spreadsheet, 'cash-flow-'.now()->format('Ymd').'.xlsx');
    }

    // ════════════════════════════════════════════════════════════════════════
    // REPORT 10 & 11 — INCOME & EXPENDITURE ACCOUNT STATEMENTS
    // ════════════════════════════════════════════════════════════════════════

    // AJAX — income accounts dropdown
    public function incomeAccounts()
    {
        $accounts = DB::table('ledger_account')
            ->where('Type', 'INCOME')
            ->where('Status', 1)
            ->orderBy('AccountName')
            ->get(['AccountNo', 'AccountName']);

        return response()->json($accounts);
    }

    // AJAX — expenditure accounts dropdown
    public function expenditureAccounts()
    {
        $accounts = DB::table('ledger_account')
            ->where('Type', 'EXPENDITURE')
            ->where('Status', 1)
            ->orderBy('AccountName')
            ->get(['AccountNo', 'AccountName']);

        return response()->json($accounts);
    }

    // Shared builder — used by both income and expenditure statement methods
    private function buildIEStatement(
        string $type,
        int $accountNo,
        string $dateFrom,
        string $dateTo,
        string $branchID
    ): array {
        $ieAccount = DB::table('active_ie')->first();

        if (! $ieAccount) {
            return [
                'account' => null,
                'accountNo' => $accountNo,
                'openingBalance' => 0,
                'transactions' => collect(),
                'closingBalance' => 0,
                'totalDr' => 0,
                'totalCr' => 0,
            ];
        }

        $account = DB::table('ledger_account')
            ->where('AccountNo', $accountNo)
            ->first(['AccountName', 'Class', 'Type']);

        $baseQuery = fn () => DB::table('journal as j')
            ->where('j.AccountID', $ieAccount->AccountID)
            ->where('j.SubAccountID', $accountNo)
            ->where('j.Reversed', 0)
            ->where('j.Status', 1)
            ->when($branchID !== 'ALL', fn ($q) => $q->where('j.BranchID', $branchID));

        // Opening balance — all movements before dateFrom
        $openingDr = (clone $baseQuery())->where('j.Date', '<', $dateFrom)->sum('j.Dr');
        $openingCr = (clone $baseQuery())->where('j.Date', '<', $dateFrom)->sum('j.Cr');

        // Income is Cr-class, expenditure is Dr-class
        $openingBalance = $type === 'INCOME'
            ? round($openingCr - $openingDr, 2)
            : round($openingDr - $openingCr, 2);

        // Period transactions
        $transactions = (clone $baseQuery())
            ->whereBetween('j.Date', [$dateFrom, $dateTo])
            ->orderBy('j.Date')
            ->orderBy('j.Time')
            ->get([
                'j.ReceiptNo',
                'j.Date',
                'j.Description',
                'j.Mode',
                'j.Dr',
                'j.Cr',
                'j.Username',
            ]);

        // Running balance
        $runningBalance = $openingBalance;
        $transactions = $transactions->map(function ($tx) use (&$runningBalance, $type) {
            $runningBalance += $type === 'INCOME'
                ? ($tx->Cr - $tx->Dr)
                : ($tx->Dr - $tx->Cr);
            $tx->RunningBalance = round($runningBalance, 2);

            return $tx;
        });

        return [
            'account' => $account,
            'accountNo' => $accountNo,
            'openingBalance' => $openingBalance,
            'transactions' => $transactions,
            'closingBalance' => $runningBalance,
            'totalDr' => round($transactions->sum('Dr'), 2),
            'totalCr' => round($transactions->sum('Cr'), 2),
        ];
    }

    // ── Income Statement ──────────────────────────────────────────────────

    public function incomeStatementPrint(Request $request)
    {
        $request->validate([
            'account_no' => ['required', 'integer'],
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date'],
            'branch_id' => ['nullable', 'string'],
        ]);

        $user = Auth::user();
        $branchID = $request->branch_id ?? $user->BranchID;
        $data = $this->buildIEStatement('INCOME', (int) $request->account_no, $request->date_from, $request->date_to, $branchID);
        $reportTitle = 'Income Account Statement — '.($data['account']?->AccountName ?? '');
        $dateFrom = \Carbon\Carbon::parse($request->date_from)->format('d M Y');
        $dateTo = \Carbon\Carbon::parse($request->date_to)->format('d M Y');

        return view('reports.accounting.income-statement-print', compact(
            'data', 'reportTitle', 'dateFrom', 'dateTo', 'branchID'
        ));
    }

    public function incomeStatementExport(Request $request)
    {
        $request->validate([
            'account_no' => ['required', 'integer'],
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date'],
            'branch_id' => ['nullable', 'string'],
        ]);

        $user = Auth::user();
        $branchID = $request->branch_id ?? $user->BranchID;
        $data = $this->buildIEStatement('INCOME', (int) $request->account_no, $request->date_from, $request->date_to, $branchID);

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Income Statement');

        $this->buildExcelHeader(
            $sheet,
            'Income Account Statement — '.($data['account']?->AccountName ?? ''),
            \Carbon\Carbon::parse($request->date_from)->format('d M Y'),
            \Carbon\Carbon::parse($request->date_to)->format('d M Y'),
            'F'
        );

        $headers = ['Date', 'Receipt No', 'Description', 'Dr (GH₵)', 'Cr (GH₵)', 'Balance (GH₵)'];
        $dataRow = $this->buildExcelColumnHeaders($sheet, 6, $headers);

        $sheet->setCellValue('A'.$dataRow, 'Opening Balance');
        $sheet->setCellValue('F'.$dataRow, $data['openingBalance']);
        $sheet->getStyle('A'.$dataRow.':F'.$dataRow)->getFont()->setBold(true);
        $dataRow++;

        foreach ($data['transactions'] as $tx) {
            $sheet->setCellValue('A'.$dataRow, \Carbon\Carbon::parse($tx->Date)->format('d M Y'));
            $sheet->setCellValue('B'.$dataRow, $tx->ReceiptNo ?? '-');
            $sheet->setCellValue('C'.$dataRow, $tx->Description ?? '-');
            $sheet->setCellValue('D'.$dataRow, $tx->Dr);
            $sheet->setCellValue('E'.$dataRow, $tx->Cr);
            $sheet->setCellValue('F'.$dataRow, $tx->RunningBalance);
            $sheet->getStyle('F'.$dataRow)->getFont()->getColor()->setRGB($tx->RunningBalance >= 0 ? '15803d' : 'b91c1c');
            $dataRow++;
        }

        $sheet->setCellValue('A'.$dataRow, 'Closing Balance');
        $sheet->setCellValue('D'.$dataRow, $data['totalDr']);
        $sheet->setCellValue('E'.$dataRow, $data['totalCr']);
        $sheet->setCellValue('F'.$dataRow, $data['closingBalance']);
        $sheet->getStyle('A'.$dataRow.':F'.$dataRow)->getFont()->setBold(true);

        $widths = ['A' => 14, 'B' => 16, 'C' => 32, 'D' => 16, 'E' => 16, 'F' => 18];
        foreach ($widths as $c => $w) {
            $sheet->getColumnDimension($c)->setWidth($w);
        }

        $this->buildExcelBorders($sheet, 6, $dataRow, 'F');
        $this->streamExcel($spreadsheet, 'income-statement-'.now()->format('Ymd').'.xlsx');
    }

    // ── Expenditure Statement ─────────────────────────────────────────────

    public function expenditureStatementPrint(Request $request)
    {
        $request->validate([
            'account_no' => ['required', 'integer'],
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date'],
            'branch_id' => ['nullable', 'string'],
        ]);

        $user = Auth::user();
        $branchID = $request->branch_id ?? $user->BranchID;
        $data = $this->buildIEStatement('EXPENDITURE', (int) $request->account_no, $request->date_from, $request->date_to, $branchID);
        $reportTitle = 'Expenditure Account Statement — '.($data['account']?->AccountName ?? '');
        $dateFrom = \Carbon\Carbon::parse($request->date_from)->format('d M Y');
        $dateTo = \Carbon\Carbon::parse($request->date_to)->format('d M Y');

        return view('reports.accounting.expenditure-statement-print', compact(
            'data', 'reportTitle', 'dateFrom', 'dateTo', 'branchID'
        ));
    }

    public function expenditureStatementExport(Request $request)
    {
        $request->validate([
            'account_no' => ['required', 'integer'],
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date'],
            'branch_id' => ['nullable', 'string'],
        ]);

        $user = Auth::user();
        $branchID = $request->branch_id ?? $user->BranchID;
        $data = $this->buildIEStatement('EXPENDITURE', (int) $request->account_no, $request->date_from, $request->date_to, $branchID);

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Expenditure Statement');

        $this->buildExcelHeader(
            $sheet,
            'Expenditure Account Statement — '.($data['account']?->AccountName ?? ''),
            \Carbon\Carbon::parse($request->date_from)->format('d M Y'),
            \Carbon\Carbon::parse($request->date_to)->format('d M Y'),
            'F'
        );

        $headers = ['Date', 'Receipt No', 'Description', 'Dr (GH₵)', 'Cr (GH₵)', 'Balance (GH₵)'];
        $dataRow = $this->buildExcelColumnHeaders($sheet, 6, $headers);

        $sheet->setCellValue('A'.$dataRow, 'Opening Balance');
        $sheet->setCellValue('F'.$dataRow, $data['openingBalance']);
        $sheet->getStyle('A'.$dataRow.':F'.$dataRow)->getFont()->setBold(true);
        $dataRow++;

        foreach ($data['transactions'] as $tx) {
            $sheet->setCellValue('A'.$dataRow, \Carbon\Carbon::parse($tx->Date)->format('d M Y'));
            $sheet->setCellValue('B'.$dataRow, $tx->ReceiptNo ?? '-');
            $sheet->setCellValue('C'.$dataRow, $tx->Description ?? '-');
            $sheet->setCellValue('D'.$dataRow, $tx->Dr);
            $sheet->setCellValue('E'.$dataRow, $tx->Cr);
            $sheet->setCellValue('F'.$dataRow, $tx->RunningBalance);
            $sheet->getStyle('F'.$dataRow)->getFont()->getColor()->setRGB($tx->RunningBalance >= 0 ? '15803d' : 'b91c1c');
            $dataRow++;
        }

        $sheet->setCellValue('A'.$dataRow, 'Closing Balance');
        $sheet->setCellValue('D'.$dataRow, $data['totalDr']);
        $sheet->setCellValue('E'.$dataRow, $data['totalCr']);
        $sheet->setCellValue('F'.$dataRow, $data['closingBalance']);
        $sheet->getStyle('A'.$dataRow.':F'.$dataRow)->getFont()->setBold(true);

        $widths = ['A' => 14, 'B' => 16, 'C' => 32, 'D' => 16, 'E' => 16, 'F' => 18];
        foreach ($widths as $c => $w) {
            $sheet->getColumnDimension($c)->setWidth($w);
        }

        $this->buildExcelBorders($sheet, 6, $dataRow, 'F');
        $this->streamExcel($spreadsheet, 'expenditure-statement-'.now()->format('Ymd').'.xlsx');
    }
}
