<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class DisbursementReportController extends BaseReportController
{
    // ════════════════════════════════════════════════════════════════════════
    // INDEX
    // ════════════════════════════════════════════════════════════════════════

    public function index()
    {
        return view('reports.disbursement.index');
    }

    // ════════════════════════════════════════════════════════════════════════
    // CONSIGNMENT P&L REPORT
    // ════════════════════════════════════════════════════════════════════════

    // ── Shared query — one row per BL (FCL) or per HBL (LCL) ────────────────
    private function buildPnlQuery(
        string $dateFrom,
        string $dateTo,
        string $branchID,
        array $allowedRestricted
    ) {
        // FCL — group by MainBL (HBL = BL for FCL)
        $fcl = DB::table('disbursement_analysis as da')
            ->join('container_main as cm', function ($join) {
                $join->on('da.BL', '=', 'cm.BL')
                    ->on('da.BL', '=', 'cm.BL'); // ensure single join
            })
            ->leftJoin('consignee_main as co', 'cm.ConsigneeID', '=', 'co.ConsigneeID')
            ->leftJoin('container_details as cd', 'cm.ConsignmentID', '=', 'cd.ConsignmentID')
            ->where('cm.BranchID', $branchID)
            ->where('cm.Status', '!=', 9)
            ->where('da.InReport', 1)
            ->where('da.Type', 'FCL')
            ->whereIn('da.Restricted', $allowedRestricted)
            ->whereBetween('da.Date', [$dateFrom, $dateTo])
            ->groupBy(
                'da.BL', 'cm.ConsignmentID', 'co.FullName',
                'cm.CmdtTypeID', 'cm.Date', 'cd.ContainerSize',
                'cd.ItemDetails'
            )
            ->select([
                'da.BL as MainBL',
                DB::raw('NULL as HBL'),
                'cm.ConsignmentID',
                'co.FullName as ConsigneeName',
                'cm.CmdtTypeID',
                'cm.Date',
                'cd.ContainerSize',
                'cd.ItemDetails',
                DB::raw('"FCL" as Type'),
                DB::raw('ROUND(SUM(da.Revenue), 2) as TotalRevenue'),
                DB::raw('ROUND(SUM(da.Expenditure), 2) as TotalExpenditure'),
                DB::raw('ROUND(SUM(da.Revenue) - SUM(da.Expenditure), 2) as NetProfit'),
                DB::raw('ROUND(SUM(da.TotalCashReceipt), 2) as TotalCashReceipt'),
            ]);

        // LCL — group by MainBL + HBL
        $lcl = DB::table('disbursement_analysis as da')
            ->join('container_main as cm', 'da.BL', '=', 'cm.BL')
            ->leftJoin('manifestation_breakdown as mb', function ($join) {
                $join->on('da.BL', '=', 'mb.MainBL')
                    ->on('da.HBL', '=', 'mb.HouseBL');
            })
            ->leftJoin('consignee_main as co', 'mb.ConsigneeID', '=', 'co.ConsigneeID')
            ->leftJoin('container_details as cd', 'cm.ConsignmentID', '=', 'cd.ConsignmentID')
            ->where('cm.BranchID', $branchID)
            ->where('cm.Status', '!=', 9)
            ->where('da.InReport', 1)
            ->where('da.Type', 'LCL')
            ->whereIn('da.Restricted', $allowedRestricted)
            ->whereBetween('da.Date', [$dateFrom, $dateTo])
            ->groupBy(
                'da.BL', 'da.HBL', 'cm.ConsignmentID',
                'co.FullName', 'cm.CmdtTypeID', 'cm.Date',
                'cd.ContainerSize', 'cd.ItemDetails'
            )
            ->select([
                'da.BL as MainBL',
                'da.HBL',
                'cm.ConsignmentID',
                'co.FullName as ConsigneeName',
                'cm.CmdtTypeID',
                'cm.Date',
                'cd.ContainerSize',
                'cd.ItemDetails',
                DB::raw('"LCL" as Type'),
                DB::raw('ROUND(SUM(da.Revenue), 2) as TotalRevenue'),
                DB::raw('ROUND(SUM(da.Expenditure), 2) as TotalExpenditure'),
                DB::raw('ROUND(SUM(da.Revenue) - SUM(da.Expenditure), 2) as NetProfit'),
                DB::raw('ROUND(SUM(da.TotalCashReceipt), 2) as TotalCashReceipt'),
            ]);

        $fcl = $fcl->get();
        $lcl = $lcl->get();

        // Group LCL rows under their MainBL for the "Both" display
        // Each MainBL gets a summary row + individual HBL rows
        $lclGrouped = $lcl->groupBy('MainBL')->map(function ($hbls) {
            $summary = (object) [
                'MainBL' => $hbls->first()->MainBL,
                'HBL' => null,
                'ConsignmentID' => $hbls->first()->ConsignmentID,
                'ConsigneeName' => $hbls->count().' HBL'.($hbls->count() != 1 ? 's' : ''),
                'CmdtTypeID' => $hbls->first()->CmdtTypeID,
                'Date' => $hbls->first()->Date,
                'ContainerSize' => $hbls->first()->ContainerSize,
                'ItemDetails' => $hbls->first()->ItemDetails,
                'Type' => 'LCL',
                'TotalRevenue' => round($hbls->sum('TotalRevenue'), 2),
                'TotalExpenditure' => round($hbls->sum('TotalExpenditure'), 2),
                'NetProfit' => round($hbls->sum('NetProfit'), 2),
                'TotalCashReceipt' => round($hbls->sum('TotalCashReceipt'), 2),
                'isLCLSummary' => true,
                'hblRows' => $hbls,
            ];

            return $summary;
        })->values();

        return [
            'fcl' => $fcl,
            'lcl' => $lclGrouped,
            'all' => $fcl->concat($lclGrouped->toBase())
                ->sortBy('Date')->values(),
        ];
    }

    // ── P&L summary strip ────────────────────────────────────────────────────
    private function buildPnlSummary($rows): array
    {
        $totalRevenue = round($rows->sum('TotalRevenue'), 2);
        $totalExpenditure = round($rows->sum('TotalExpenditure'), 2);
        $netProfit = round($totalRevenue - $totalExpenditure, 2);
        $margin = $totalRevenue > 0
            ? round(($netProfit / $totalRevenue) * 100, 1) : 0;

        return [
            'total_revenue' => $totalRevenue,
            'total_expenditure' => $totalExpenditure,
            'net_profit' => $netProfit,
            'margin' => $margin,
            'profitable' => $rows->where('NetProfit', '>', 0)->count(),
            'loss_making' => $rows->where('NetProfit', '<', 0)->count(),
            'breakeven' => $rows->where('NetProfit', 0)->count(),
            'total' => $rows->count(),
        ];
    }

    public function consignmentPnl()
    {
        return view('reports.disbursement.index');
    }

    public function consignmentPnlPrint(Request $request)
    {
        $request->validate([
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date'],
        ]);

        $user = Auth::user();
        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;
        $allowedRestricted = $this->allowedRestricted();

        $data = $this->buildPnlQuery($dateFrom, $dateTo, $user->BranchID, $allowedRestricted);
        $summary = $this->buildPnlSummary($data['all']);

        $reportTitle = 'Consignment P&L Report';
        $dateFrom = \Carbon\Carbon::parse($dateFrom)->format('d M Y');
        $dateTo = \Carbon\Carbon::parse($dateTo)->format('d M Y');

        return view('reports.disbursement.consignment-pnl-print', compact(
            'data', 'summary', 'reportTitle', 'dateFrom', 'dateTo'
        ));
    }

    public function consignmentPnlExport(Request $request)
    {
        $request->validate([
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date'],
        ]);

        $user = Auth::user();
        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;
        $allowedRestricted = $this->allowedRestricted();

        $data = $this->buildPnlQuery($dateFrom, $dateTo, $user->BranchID, $allowedRestricted);
        $summary = $this->buildPnlSummary($data['all']);

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Consignment P&L');

        $this->buildExcelHeader(
            $sheet, 'Consignment P&L Report',
            \Carbon\Carbon::parse($dateFrom)->format('d M Y'),
            \Carbon\Carbon::parse($dateTo)->format('d M Y'),
            'H'
        );

        $headers = ['#', 'Main BL', 'HBL', 'Consignee', 'Type', 'Revenue (GH₵)', 'Expenditure (GH₵)', 'Net Profit (GH₵)'];
        $dataRow = $this->buildExcelColumnHeaders($sheet, 6, $headers);

        $i = 1;
        foreach ($data['all'] as $row) {
            // FCL or LCL summary row
            $sheet->setCellValue('A'.$dataRow, $i++);
            $sheet->setCellValue('B'.$dataRow, $row->MainBL ?? '-');
            $sheet->setCellValue('C'.$dataRow, $row->HBL ?? ($row->Type === 'LCL' ? 'LCL Summary' : '-'));
            $sheet->setCellValue('D'.$dataRow, $row->ConsigneeName ?? '-');
            $sheet->setCellValue('E'.$dataRow, $row->Type);
            $sheet->setCellValue('F'.$dataRow, $row->TotalRevenue);
            $sheet->setCellValue('G'.$dataRow, $row->TotalExpenditure);
            $sheet->setCellValue('H'.$dataRow, $row->NetProfit);

            // Colour net profit
            $this->applyPnlColour($sheet, 'H', $dataRow, $row->NetProfit);

            // Bold LCL summary rows
            if (! empty($row->isLCLSummary)) {
                $sheet->getStyle('A'.$dataRow.':H'.$dataRow)->getFont()->setBold(true);
            }

            $dataRow++;

            // LCL HBL breakdown rows
            if (! empty($row->isLCLSummary) && ! empty($row->hblRows)) {
                foreach ($row->hblRows as $hbl) {
                    $sheet->setCellValue('B'.$dataRow, '  └ '.$hbl->MainBL);
                    $sheet->setCellValue('C'.$dataRow, $hbl->HBL);
                    $sheet->setCellValue('D'.$dataRow, $hbl->ConsigneeName ?? '-');
                    $sheet->setCellValue('E'.$dataRow, 'LCL');
                    $sheet->setCellValue('F'.$dataRow, $hbl->TotalRevenue);
                    $sheet->setCellValue('G'.$dataRow, $hbl->TotalExpenditure);
                    $sheet->setCellValue('H'.$dataRow, $hbl->NetProfit);
                    $sheet->getStyle('A'.$dataRow.':H'.$dataRow)
                        ->getFont()->getColor()->setRGB('6b7280');
                    $this->applyPnlColour($sheet, 'H', $dataRow, $hbl->NetProfit);
                    $dataRow++;
                }
            }
        }

        // Totals row
        $sheet->setCellValue('A'.$dataRow, 'TOTALS');
        $sheet->setCellValue('F'.$dataRow, $summary['total_revenue']);
        $sheet->setCellValue('G'.$dataRow, $summary['total_expenditure']);
        $sheet->setCellValue('H'.$dataRow, $summary['net_profit']);
        $sheet->getStyle('A'.$dataRow.':H'.$dataRow)->getFont()->setBold(true);
        $sheet->getStyle('A'.$dataRow.':H'.$dataRow)
            ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('f3f4f6');
        $this->applyPnlColour($sheet, 'H', $dataRow, $summary['net_profit']);

        $widths = ['A' => 5, 'B' => 22, 'C' => 16, 'D' => 28, 'E' => 8, 'F' => 18, 'G' => 18, 'H' => 18];
        foreach ($widths as $c => $w) {
            $sheet->getColumnDimension($c)->setWidth($w);
        }

        $this->buildExcelBorders($sheet, 6, $dataRow, 'H');
        $this->streamExcel($spreadsheet, 'consignment-pnl-'.now()->format('Ymd-His').'.xlsx');
    }

    // ── P&L colour helper — green for profit, red for loss ──────────────────
    private function applyPnlColour(
        \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet,
        string $col,
        int $row,
        float $value
    ): void {
        $hex = $value > 0 ? '15803d' : ($value < 0 ? 'b91c1c' : '6b7280');
        $sheet->getStyle($col.$row)->getFont()->getColor()->setRGB($hex);
        if ($value != 0) {
            $sheet->getStyle($col.$row)->getFont()->setBold(true);
        }
    }

    // ════════════════════════════════════════════════════════════════════════
    // EXPENDITURE BY ACCOUNT
    // ════════════════════════════════════════════════════════════════════════

    private function buildExpenditureByAccountQuery(
        string $dateFrom,
        string $dateTo,
        string $branchID,
        array $allowedRestricted
    ) {
        return DB::table('disbursement_analysis as da')
            ->join('container_main as cm', 'da.BL', '=', 'cm.BL')
            ->leftJoin('ledger_account as la', 'da.AccountID', '=', 'la.AccountNo')
            ->where('cm.BranchID', $branchID)
            ->where('cm.Status', '!=', 9)
            ->where('da.InReport', 1)
            ->whereIn('da.Restricted', $allowedRestricted)
            ->whereBetween('da.Date', [$dateFrom, $dateTo])
            ->groupBy('da.AccountID', 'la.AccountName')
            ->orderByRaw('SUM(da.Expenditure) DESC')
            ->get([
                'la.AccountName',
                DB::raw('ROUND(SUM(da.Expenditure), 2) as TotalExpenditure'),
                DB::raw('ROUND(SUM(da.Revenue), 2) as TotalRevenue'),
                DB::raw('COUNT(DISTINCT da.BL) as ConsignmentCount'),
                DB::raw('ROUND(AVG(da.Expenditure), 2) as AvgPerEntry'),
            ]);
    }

    public function expenditureByAccount()
    {
        return view('reports.disbursement.index');
    }

    public function expenditureByAccountPrint(Request $request)
    {
        $request->validate([
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date'],
        ]);

        $user = Auth::user();
        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;
        $allowedRestricted = $this->allowedRestricted();

        $rows = $this->buildExpenditureByAccountQuery($dateFrom, $dateTo, $user->BranchID, $allowedRestricted);
        $totalExp = round($rows->sum('TotalExpenditure'), 2);
        $reportTitle = 'Expenditure by Account';
        $dateFrom = \Carbon\Carbon::parse($dateFrom)->format('d M Y');
        $dateTo = \Carbon\Carbon::parse($dateTo)->format('d M Y');

        return view('reports.disbursement.expenditure-by-account-print', compact(
            'rows', 'totalExp', 'reportTitle', 'dateFrom', 'dateTo'
        ));
    }

    public function expenditureByAccountExport(Request $request)
    {
        $request->validate([
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date'],
        ]);

        $user = Auth::user();
        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;
        $allowedRestricted = $this->allowedRestricted();

        $rows = $this->buildExpenditureByAccountQuery($dateFrom, $dateTo, $user->BranchID, $allowedRestricted);
        $totalExp = round($rows->sum('TotalExpenditure'), 2);

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Expenditure by Account');

        $this->buildExcelHeader(
            $sheet, 'Expenditure by Account',
            \Carbon\Carbon::parse($dateFrom)->format('d M Y'),
            \Carbon\Carbon::parse($dateTo)->format('d M Y'),
            'E'
        );

        $headers = ['Account', 'Consignments', 'Total Expenditure (GH₵)', 'Total Revenue (GH₵)', 'Avg per Entry (GH₵)'];
        $dataRow = $this->buildExcelColumnHeaders($sheet, 6, $headers);

        foreach ($rows as $r) {
            $sheet->setCellValue('A'.$dataRow, $r->AccountName ?? '-');
            $sheet->setCellValue('B'.$dataRow, $r->ConsignmentCount);
            $sheet->setCellValue('C'.$dataRow, $r->TotalExpenditure);
            $sheet->setCellValue('D'.$dataRow, $r->TotalRevenue);
            $sheet->setCellValue('E'.$dataRow, $r->AvgPerEntry);
            $dataRow++;
        }

        // Totals
        $sheet->setCellValue('A'.$dataRow, 'TOTAL');
        $sheet->setCellValue('C'.$dataRow, $totalExp);
        $sheet->getStyle('A'.$dataRow.':E'.$dataRow)->getFont()->setBold(true);
        $sheet->getStyle('A'.$dataRow.':E'.$dataRow)
            ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('f3f4f6');

        $widths = ['A' => 30, 'B' => 16, 'C' => 22, 'D' => 22, 'E' => 20];
        foreach ($widths as $c => $w) {
            $sheet->getColumnDimension($c)->setWidth($w);
        }

        $this->buildExcelBorders($sheet, 6, $dataRow, 'E');
        $this->streamExcel($spreadsheet, 'expenditure-by-account-'.now()->format('Ymd-His').'.xlsx');
    }

    // ════════════════════════════════════════════════════════════════════════
    // COMPARATIVE DISBURSEMENT
    // ════════════════════════════════════════════════════════════════════════

    private function buildComparativeQuery(
        string $dateFrom,
        string $dateTo,
        string $branchID,
        array $allowedRestricted,
        ?string $ItemDetails = null,
        ?string $containerSize = null
    ) {
        $query = DB::table('disbursement_analysis as da')
            ->join('container_main as cm', 'da.BL', '=', 'cm.BL')
            ->leftJoin('consignee_main as co', 'cm.ConsigneeID', '=', 'co.ConsigneeID')
            ->leftJoin('container_details as cd', 'cm.ConsignmentID', '=', 'cd.ConsignmentID')
            ->where('cm.BranchID', $branchID)
            ->where('cm.Status', '!=', 9)
            ->where('da.InReport', 1)
            ->whereIn('da.Restricted', $allowedRestricted)
            ->whereBetween('da.Date', [$dateFrom, $dateTo])
            ->groupBy(
                'da.BL', 'cm.ConsignmentID', 'co.FullName',
                'cm.CmdtTypeID', 'cm.Date', 'cd.ContainerSize',
                'cd.ItemDetails', 'da.Type'
            )
            ->select([
                'da.BL as MainBL',
                'cm.ConsignmentID',
                'co.FullName as ConsigneeName',
                'cm.CmdtTypeID',
                'cm.Date',
                'cd.ContainerSize',
                'cd.ItemDetails',
                'da.Type',
                DB::raw('ROUND(SUM(da.Revenue), 2) as TotalRevenue'),
                DB::raw('ROUND(SUM(da.Expenditure), 2) as TotalExpenditure'),
                DB::raw('ROUND(SUM(da.Revenue) - SUM(da.Expenditure), 2) as NetProfit'),
                DB::raw('ROUND(SUM(da.TotalCashReceipt), 2) as TotalCashReceipt'),
            ]);

        if ($ItemDetails) {
            $query->where('cd.ItemDetails', 'like', '%'.$ItemDetails.'%');
        }
        if ($containerSize) {
            $query->where('cd.ContainerSize', $containerSize);
        }

        $rows = $query->orderBy('da.Date', 'asc')->get();

        // Statistical benchmarks across similar consignments
        $benchmarks = [
            'avg_expenditure' => round($rows->avg('TotalExpenditure'), 2),
            'min_expenditure' => round($rows->min('TotalExpenditure'), 2),
            'max_expenditure' => round($rows->max('TotalExpenditure'), 2),
            'avg_revenue' => round($rows->avg('TotalRevenue'), 2),
            'avg_net_profit' => round($rows->avg('NetProfit'), 2),
            'total' => $rows->count(),
        ];

        return ['rows' => $rows, 'benchmarks' => $benchmarks];
    }

    // Distinct item descriptions for filter dropdown
    public function ItemDetailss(Request $request)
    {
        $branchID = Auth::user()->BranchID;

        $items = DB::table('container_details as cd')
            ->join('container_main as cm', 'cd.ConsignmentID', '=', 'cm.ConsignmentID')
            ->where('cm.BranchID', $branchID)
            ->where('cm.Status', '!=', 9)
            ->whereNotNull('cd.ItemDetails')
            ->where('cd.ItemDetails', '!=', '')
            ->when($request->q, fn ($q) => $q->where('cd.ItemDetails', 'like', '%'.$request->q.'%'))
            ->groupBy('cd.ItemDetails')
            ->orderBy('cd.ItemDetails')
            ->limit(20)
            ->pluck('cd.ItemDetails');

        return response()->json($items);
    }

    // Distinct container sizes for filter dropdown
    public function containerSizes()
    {
        $sizes = DB::table('container_details')
            ->whereNotNull('ContainerSize')
            ->where('ContainerSize', '!=', '')
            ->groupBy('ContainerSize')
            ->orderBy('ContainerSize')
            ->pluck('ContainerSize');

        return response()->json($sizes);
    }

    public function comparative()
    {
        return view('reports.disbursement.index');
    }

    public function comparativePrint(Request $request)
    {
        $request->validate([
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date'],
        ]);

        $user = Auth::user();
        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;
        $allowedRestricted = $this->allowedRestricted();
        $ItemDetails = $request->item_description;
        $containerSize = $request->container_size;

        $result = $this->buildComparativeQuery($dateFrom, $dateTo, $user->BranchID, $allowedRestricted, $ItemDetails, $containerSize);
        $reportTitle = 'Comparative Disbursement Report';
        $dateFrom = \Carbon\Carbon::parse($dateFrom)->format('d M Y');
        $dateTo = \Carbon\Carbon::parse($dateTo)->format('d M Y');

        return view('reports.disbursement.comparative-print', [
            'rows' => $result['rows'],
            'benchmarks' => $result['benchmarks'],
            'reportTitle' => $reportTitle,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'ItemDetails' => $ItemDetails,
            'containerSize' => $containerSize,
        ]);
    }

    public function comparativeExport(Request $request)
    {
        $request->validate([
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date'],
        ]);

        $user = Auth::user();
        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;
        $allowedRestricted = $this->allowedRestricted();

        $result = $this->buildComparativeQuery(
            $dateFrom, $dateTo, $user->BranchID, $allowedRestricted,
            $request->item_description, $request->container_size
        );

        $rows = $result['rows'];
        $benchmarks = $result['benchmarks'];

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Comparative Disbursement');

        $this->buildExcelHeader(
            $sheet, 'Comparative Disbursement Report',
            \Carbon\Carbon::parse($dateFrom)->format('d M Y'),
            \Carbon\Carbon::parse($dateTo)->format('d M Y'),
            'I'
        );

        $headers = ['#', 'Main BL', 'Consignee', 'Item Description', 'Container Size', 'Revenue (GH₵)', 'Expenditure (GH₵)', 'Net Profit (GH₵)', 'vs Avg Exp'];
        $dataRow = $this->buildExcelColumnHeaders($sheet, 6, $headers);

        foreach ($rows as $i => $r) {
            $vsAvg = $benchmarks['avg_expenditure'] > 0
                ? round((($r->TotalExpenditure - $benchmarks['avg_expenditure']) / $benchmarks['avg_expenditure']) * 100, 1)
                : 0;

            $sheet->setCellValue('A'.$dataRow, $i + 1);
            $sheet->setCellValue('B'.$dataRow, $r->MainBL ?? '-');
            $sheet->setCellValue('C'.$dataRow, $r->ConsigneeName ?? '-');
            $sheet->setCellValue('D'.$dataRow, $r->ItemDetails ?? '-');
            $sheet->setCellValue('E'.$dataRow, $r->ContainerSize ?? '-');
            $sheet->setCellValue('F'.$dataRow, $r->TotalRevenue);
            $sheet->setCellValue('G'.$dataRow, $r->TotalExpenditure);
            $sheet->setCellValue('H'.$dataRow, $r->NetProfit);
            $sheet->setCellValue('I'.$dataRow, ($vsAvg > 0 ? '+' : '').$vsAvg.'%');

            $this->applyPnlColour($sheet, 'H', $dataRow, $r->NetProfit);

            $vsHex = $vsAvg > 10 ? 'b91c1c' : ($vsAvg < -10 ? '15803d' : 'b45309');
            $sheet->getStyle('I'.$dataRow)->getFont()->getColor()->setRGB($vsHex);

            $dataRow++;
        }

        // Benchmark rows
        $dataRow++;
        $sheet->setCellValue('A'.$dataRow, 'BENCHMARKS (similar consignments)');
        $sheet->getStyle('A'.$dataRow)->getFont()->setBold(true);
        $dataRow++;

        foreach ([
            ['Avg Expenditure', $benchmarks['avg_expenditure']],
            ['Min Expenditure', $benchmarks['min_expenditure']],
            ['Max Expenditure', $benchmarks['max_expenditure']],
            ['Avg Revenue',     $benchmarks['avg_revenue']],
            ['Avg Net Profit',  $benchmarks['avg_net_profit']],
        ] as [$label, $val]) {
            $sheet->setCellValue('A'.$dataRow, $label);
            $sheet->setCellValue('G'.$dataRow, $val);
            $dataRow++;
        }

        $widths = ['A' => 5, 'B' => 20, 'C' => 26, 'D' => 22, 'E' => 14, 'F' => 18, 'G' => 18, 'H' => 18, 'I' => 12];
        foreach ($widths as $c => $w) {
            $sheet->getColumnDimension($c)->setWidth($w);
        }

        $this->buildExcelBorders($sheet, 6, $dataRow - 8, 'I');
        $this->streamExcel($spreadsheet, 'comparative-disbursement-'.now()->format('Ymd-His').'.xlsx');
    }

    // ════════════════════════════════════════════════════════════════════════
    // DISBURSEMENT DETAIL REPORT
    // ════════════════════════════════════════════════════════════════════════

    private function buildDetailQuery(
        string $dateFrom,
        string $dateTo,
        string $branchID,
        array $allowedRestricted,
        ?string $bl = null
    ) {
        $query = DB::table('disbursement_analysis as da')
            ->join('container_main as cm', 'da.BL', '=', 'cm.BL')
            ->leftJoin('consignee_main as co', 'cm.ConsigneeID', '=', 'co.ConsigneeID')
            ->leftJoin('ledger_account as la', 'da.AccountID', '=', 'la.AccountNo')
            ->where('cm.BranchID', $branchID)
            ->where('cm.Status', '!=', 9)
            ->where('da.InReport', 1)
            ->whereIn('da.Restricted', $allowedRestricted)
            ->whereBetween('da.Date', [$dateFrom, $dateTo])
            ->when($bl, fn ($q) => $q->where('da.BL', strtoupper($bl)))
            ->orderBy('da.Date', 'asc')
            ->orderBy('da.BL', 'asc')
            ->get([
                'da.BL as MainBL',
                'da.HBL',
                'da.ReceiptNo',
                'da.Date',
                'la.AccountName',
                'da.Expenditure',
                'da.Revenue',
                'da.TotalCashReceipt',
                'da.Type',
                'da.Username',
                'co.FullName as ConsigneeName',
            ]);

        return $query;
    }

    public function detail()
    {
        return view('reports.disbursement.index');
    }

    public function detailPrint(Request $request)
    {
        $request->validate([
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date'],
            'bl' => ['nullable', 'string', 'max:50'],
        ]);

        $user = Auth::user();
        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;
        $allowedRestricted = $this->allowedRestricted();

        $rows = $this->buildDetailQuery($dateFrom, $dateTo, $user->BranchID, $allowedRestricted, $request->bl);

        $totals = [
            'expenditure' => round($rows->sum('Expenditure'), 2),
            'revenue' => round($rows->sum('Revenue'), 2),
            'cash_receipt' => round($rows->sum('TotalCashReceipt'), 2),
            'net_profit' => round($rows->sum('Revenue') - $rows->sum('Expenditure'), 2),
        ];

        $reportTitle = 'Disbursement Detail Report';
        $dateFrom = \Carbon\Carbon::parse($dateFrom)->format('d M Y');
        $dateTo = \Carbon\Carbon::parse($dateTo)->format('d M Y');

        return view('reports.disbursement.detail-print', compact(
            'rows', 'totals', 'reportTitle', 'dateFrom', 'dateTo'
        ));
    }

    public function detailExport(Request $request)
    {
        $request->validate([
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date'],
            'bl' => ['nullable', 'string', 'max:50'],
        ]);

        $user = Auth::user();
        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;
        $allowedRestricted = $this->allowedRestricted();

        $rows = $this->buildDetailQuery($dateFrom, $dateTo, $user->BranchID, $allowedRestricted, $request->bl);
        $totals = [
            'expenditure' => round($rows->sum('Expenditure'), 2),
            'revenue' => round($rows->sum('Revenue'), 2),
            'net_profit' => round($rows->sum('Revenue') - $rows->sum('Expenditure'), 2),
        ];

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Disbursement Detail');

        $this->buildExcelHeader(
            $sheet, 'Disbursement Detail Report',
            \Carbon\Carbon::parse($dateFrom)->format('d M Y'),
            \Carbon\Carbon::parse($dateTo)->format('d M Y'),
            'J'
        );

        $headers = ['#', 'Date', 'Main BL', 'HBL', 'Consignee', 'Account', 'Receipt No', 'Expenditure (GH₵)', 'Revenue (GH₵)', 'Net (GH₵)'];
        $dataRow = $this->buildExcelColumnHeaders($sheet, 6, $headers);

        foreach ($rows as $i => $r) {
            $net = round($r->Revenue - $r->Expenditure, 2);

            $sheet->setCellValue('A'.$dataRow, $i + 1);
            $sheet->setCellValue('B'.$dataRow, \Carbon\Carbon::parse($r->Date)->format('d M Y'));
            $sheet->setCellValue('C'.$dataRow, $r->MainBL ?? '-');
            $sheet->setCellValue('D'.$dataRow, $r->HBL ?? '-');
            $sheet->setCellValue('E'.$dataRow, $r->ConsigneeName ?? '-');
            $sheet->setCellValue('F'.$dataRow, $r->AccountName ?? '-');
            $sheet->setCellValue('G'.$dataRow, $r->ReceiptNo ?? '-');
            $sheet->setCellValue('H'.$dataRow, $r->Expenditure);
            $sheet->setCellValue('I'.$dataRow, $r->Revenue);
            $sheet->setCellValue('J'.$dataRow, $net);
            $this->applyPnlColour($sheet, 'J', $dataRow, $net);
            $dataRow++;
        }

        // Totals
        $sheet->setCellValue('A'.$dataRow, 'TOTALS');
        $sheet->setCellValue('H'.$dataRow, $totals['expenditure']);
        $sheet->setCellValue('I'.$dataRow, $totals['revenue']);
        $sheet->setCellValue('J'.$dataRow, $totals['net_profit']);
        $sheet->getStyle('A'.$dataRow.':J'.$dataRow)->getFont()->setBold(true);
        $this->applyPnlColour($sheet, 'J', $dataRow, $totals['net_profit']);

        $widths = ['A' => 5, 'B' => 14, 'C' => 18, 'D' => 14, 'E' => 24, 'F' => 24, 'G' => 16, 'H' => 18, 'I' => 18, 'J' => 16];
        foreach ($widths as $c => $w) {
            $sheet->getColumnDimension($c)->setWidth($w);
        }

        $this->buildExcelBorders($sheet, 6, $dataRow, 'J');
        $this->streamExcel($spreadsheet, 'disbursement-detail-'.now()->format('Ymd-His').'.xlsx');
    }

    // ════════════════════════════════════════════════════════════════════════
    // UNAPPROVED DISBURSEMENTS
    // ════════════════════════════════════════════════════════════════════════

    private function buildUnapprovedQuery(string $branchID, array $allowedRestricted)
    {
        return DB::table('disbursement_analysis as da')
            ->join('container_main as cm', 'da.BL', '=', 'cm.BL')
            ->leftJoin('consignee_main as co', 'cm.ConsigneeID', '=', 'co.ConsigneeID')
            ->leftJoin('ledger_account as la', 'da.AccountID', '=', 'la.AccountNo')
            ->where('cm.BranchID', $branchID)
            ->where('cm.Status', '!=', 9)
            ->where('da.Status', 2)  // pending only
            ->where('da.InReport', 1)
            ->whereIn('da.Restricted', $allowedRestricted)
            ->orderBy('da.Date', 'asc')
            ->get([
                'da.BL as MainBL',
                'da.HBL',
                'da.ReceiptNo',
                'da.Date',
                'da.Expenditure',
                'da.Revenue',
                'da.Username',
                'la.AccountName',
                'co.FullName as ConsigneeName',
                'da.Type',
                DB::raw('DATEDIFF(CURDATE(), da.Date) as DaysPending'),
            ]);
    }

    public function unapproved()
    {
        return view('reports.disbursement.index');
    }

    public function unapprovedPrint(Request $request)
    {
        $user = Auth::user();
        $allowedRestricted = $this->allowedRestricted();

        $rows = $this->buildUnapprovedQuery($user->BranchID, $allowedRestricted);

        $totals = [
            'expenditure' => round($rows->sum('Expenditure'), 2),
            'revenue' => round($rows->sum('Revenue'), 2),
            'total' => $rows->count(),
            'overdue' => $rows->where('DaysPending', '>', 7)->count(),
        ];

        $reportTitle = 'Unapproved Disbursements';
        $dateFrom = now()->startOfMonth()->format('d M Y');
        $dateTo = now()->format('d M Y');

        return view('reports.disbursement.unapproved-print', compact(
            'rows', 'totals', 'reportTitle', 'dateFrom', 'dateTo'
        ));
    }

    public function unapprovedExport(Request $request)
    {
        $user = Auth::user();
        $allowedRestricted = $this->allowedRestricted();

        $rows = $this->buildUnapprovedQuery($user->BranchID, $allowedRestricted);
        $totals = [
            'expenditure' => round($rows->sum('Expenditure'), 2),
            'total' => $rows->count(),
        ];

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Unapproved Disbursements');

        $this->buildExcelHeader(
            $sheet, 'Unapproved Disbursements',
            now()->startOfMonth()->format('d M Y'),
            now()->format('d M Y'),
            'I'
        );

        $headers = ['#', 'Date', 'Main BL', 'HBL', 'Consignee', 'Account', 'Receipt No', 'Expenditure (GH₵)', 'Days Pending'];
        $dataRow = $this->buildExcelColumnHeaders($sheet, 6, $headers);

        foreach ($rows as $i => $r) {
            $sheet->setCellValue('A'.$dataRow, $i + 1);
            $sheet->setCellValue('B'.$dataRow, \Carbon\Carbon::parse($r->Date)->format('d M Y'));
            $sheet->setCellValue('C'.$dataRow, $r->MainBL ?? '-');
            $sheet->setCellValue('D'.$dataRow, $r->HBL ?? '-');
            $sheet->setCellValue('E'.$dataRow, $r->ConsigneeName ?? '-');
            $sheet->setCellValue('F'.$dataRow, $r->AccountName ?? '-');
            $sheet->setCellValue('G'.$dataRow, $r->ReceiptNo ?? '-');
            $sheet->setCellValue('H'.$dataRow, $r->Expenditure);
            $sheet->setCellValue('I'.$dataRow, $r->DaysPending);

            if ($r->DaysPending > 7) {
                $sheet->getStyle('I'.$dataRow)->getFont()->getColor()->setRGB('b91c1c');
                $sheet->getStyle('I'.$dataRow)->getFont()->setBold(true);
            }
            $dataRow++;
        }

        $sheet->setCellValue('A'.$dataRow, 'TOTAL');
        $sheet->setCellValue('H'.$dataRow, $totals['expenditure']);
        $sheet->getStyle('A'.$dataRow.':I'.$dataRow)->getFont()->setBold(true);

        $widths = ['A' => 5, 'B' => 14, 'C' => 18, 'D' => 14, 'E' => 24, 'F' => 24, 'G' => 16, 'H' => 18, 'I' => 14];
        foreach ($widths as $c => $w) {
            $sheet->getColumnDimension($c)->setWidth($w);
        }

        $this->buildExcelBorders($sheet, 6, $dataRow - 1, 'I');
        $this->streamExcel($spreadsheet, 'unapproved-disbursements-'.now()->format('Ymd-His').'.xlsx');
    }

    // ════════════════════════════════════════════════════════════════════════
    // OFFICER DISBURSEMENT SUMMARY
    // ════════════════════════════════════════════════════════════════════════

    private function buildOfficerSummaryQuery(
        string $dateFrom,
        string $dateTo,
        string $branchID,
        array $allowedRestricted
    ) {
        return DB::table('disbursement_analysis as da')
            ->join('container_main as cm', 'da.BL', '=', 'cm.BL')
            ->where('cm.BranchID', $branchID)
            ->where('cm.Status', '!=', 9)
            ->where('da.InReport', 1)
            ->whereIn('da.Restricted', $allowedRestricted)
            ->whereBetween('da.Date', [$dateFrom, $dateTo])
            ->groupBy('da.Username')
            ->orderByRaw('SUM(da.Expenditure) DESC')
            ->get([
                'da.Username',
                DB::raw('ROUND(SUM(da.TotalCashReceipt), 2) as TotalCashReceived'),
                DB::raw('ROUND(SUM(da.Expenditure), 2) as TotalExpenditure'),
                DB::raw('ROUND(SUM(da.TotalCashReceipt) - SUM(da.Expenditure), 2) as ChangeReturned'),
                DB::raw('ROUND(SUM(da.Revenue), 2) as TotalRevenue'),
                DB::raw('COUNT(DISTINCT da.BL) as ConsignmentCount'),
                DB::raw('COUNT(*) as EntryCount'),
            ]);
    }

    public function officerSummary()
    {
        return view('reports.disbursement.index');
    }

    public function officerSummaryPrint(Request $request)
    {
        $request->validate([
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date'],
        ]);

        $user = Auth::user();
        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;
        $allowedRestricted = $this->allowedRestricted();

        $rows = $this->buildOfficerSummaryQuery($dateFrom, $dateTo, $user->BranchID, $allowedRestricted);

        $totals = [
            'cash_received' => round($rows->sum('TotalCashReceived'), 2),
            'expenditure' => round($rows->sum('TotalExpenditure'), 2),
            'change' => round($rows->sum('ChangeReturned'), 2),
            'revenue' => round($rows->sum('TotalRevenue'), 2),
        ];

        $reportTitle = 'Officer Disbursement Summary';
        $dateFrom = \Carbon\Carbon::parse($dateFrom)->format('d M Y');
        $dateTo = \Carbon\Carbon::parse($dateTo)->format('d M Y');

        return view('reports.disbursement.officer-summary-print', compact(
            'rows', 'totals', 'reportTitle', 'dateFrom', 'dateTo'
        ));
    }

    public function officerSummaryExport(Request $request)
    {
        $request->validate([
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date'],
        ]);

        $user = Auth::user();
        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;
        $allowedRestricted = $this->allowedRestricted();

        $rows = $this->buildOfficerSummaryQuery($dateFrom, $dateTo, $user->BranchID, $allowedRestricted);
        $totals = [
            'cash_received' => round($rows->sum('TotalCashReceived'), 2),
            'expenditure' => round($rows->sum('TotalExpenditure'), 2),
            'change' => round($rows->sum('ChangeReturned'), 2),
        ];

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Officer Summary');

        $this->buildExcelHeader(
            $sheet, 'Officer Disbursement Summary',
            \Carbon\Carbon::parse($dateFrom)->format('d M Y'),
            \Carbon\Carbon::parse($dateTo)->format('d M Y'),
            'G'
        );

        $headers = ['Officer', 'Consignments', 'Entries', 'Cash Received (GH₵)', 'Expenditure (GH₵)', 'Change Returned (GH₵)', 'Revenue (GH₵)'];
        $dataRow = $this->buildExcelColumnHeaders($sheet, 6, $headers);

        foreach ($rows as $r) {
            $sheet->setCellValue('A'.$dataRow, $r->Username ?? '-');
            $sheet->setCellValue('B'.$dataRow, $r->ConsignmentCount);
            $sheet->setCellValue('C'.$dataRow, $r->EntryCount);
            $sheet->setCellValue('D'.$dataRow, $r->TotalCashReceived);
            $sheet->setCellValue('E'.$dataRow, $r->TotalExpenditure);
            $sheet->setCellValue('F'.$dataRow, $r->ChangeReturned);
            $sheet->setCellValue('G'.$dataRow, $r->TotalRevenue);

            // Flag negative change returned (spent more than received)
            if ($r->ChangeReturned < 0) {
                $sheet->getStyle('F'.$dataRow)->getFont()->getColor()->setRGB('b91c1c');
                $sheet->getStyle('F'.$dataRow)->getFont()->setBold(true);
            }
            $dataRow++;
        }

        $sheet->setCellValue('A'.$dataRow, 'TOTALS');
        $sheet->setCellValue('D'.$dataRow, $totals['cash_received']);
        $sheet->setCellValue('E'.$dataRow, $totals['expenditure']);
        $sheet->setCellValue('F'.$dataRow, $totals['change']);
        $sheet->getStyle('A'.$dataRow.':G'.$dataRow)->getFont()->setBold(true);

        $widths = ['A' => 20, 'B' => 14, 'C' => 10, 'D' => 22, 'E' => 22, 'F' => 22, 'G' => 18];
        foreach ($widths as $c => $w) {
            $sheet->getColumnDimension($c)->setWidth($w);
        }

        $this->buildExcelBorders($sheet, 6, $dataRow, 'G');
        $this->streamExcel($spreadsheet, 'officer-summary-'.now()->format('Ymd-His').'.xlsx');
    }
}
