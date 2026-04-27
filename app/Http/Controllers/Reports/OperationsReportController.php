<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class OperationsReportController extends BaseReportController
{
    // ── Consignment Status — shared query builder ────────────────────────────
    // Used by Data (AJAX), Print and Export — one place to maintain.

    private function buildConsignmentStatusQuery(
        string $dateFrom,
        string $dateTo,
        string $status,
        string $branchID
    ) {
        $query = DB::table('container_main as cm')
            ->leftJoin('consignee_main as co', 'cm.ConsigneeID', '=', 'co.ConsigneeID')
            ->leftJoin('container_details as cd', 'cm.ConsignmentID', '=', 'cd.ConsignmentID')
            ->where('cm.BranchID', $branchID)
            ->where('cm.Status', '!=', 9)
            ->whereBetween('cm.Date', [$dateFrom, $dateTo])
            ->groupBy(
                'cm.ConsignmentID',
                'cm.BL',
                'co.FullName',
                'cm.CmdtTypeID',
                'cm.Status',
                'cm.Date'
            )
            ->select([
                'cm.ConsignmentID',
                'cm.BL as MainBL',
                'co.FullName as ConsigneeName',
                'cm.CmdtTypeID',
                'cm.Status',
                'cm.Date',
                DB::raw('DATEDIFF(CURDATE(), cm.Date) as AgeDays'),
                DB::raw('GROUP_CONCAT(cd.ContainerNo ORDER BY cd.ContainerNo SEPARATOR ", ") as ContainerNos'),
            ]);

        if ($status !== 'all') {
            $query->where('cm.Status', (int) $status);
        }

        return $query->orderBy('cm.Date', 'asc')->get();
    }

    // ── Consignment Detail — shared query builder ────────────────────────────
    // Used by Print and Export — one place to maintain.

    private function buildConsignmentDetailQuery(
        string $dateFrom,
        string $dateTo,
        string $status,
        string $branchID
    ) {
        $query = DB::table('container_main as cm')
            ->leftJoin('consignee_main as co', 'cm.ConsigneeID', '=', 'co.ConsigneeID')
            ->leftJoin('ship_carrier as sc', 'cm.CarrierID', '=', 'sc.CarrierID')
            ->leftJoin('shipper_main as sm', 'cm.ShipperID', '=', 'sm.ShipperID')
            ->leftJoin('pol as p', 'cm.POL_ID', '=', 'p.POL_ID')
            ->leftJoin('container_details as cd', 'cm.ConsignmentID', '=', 'cd.ConsignmentID')
            ->where('cm.BranchID', $branchID)
            ->where('cm.Status', '!=', 9)
            ->whereBetween('cm.Date', [$dateFrom, $dateTo])
            ->groupBy(
                'cm.ConsignmentID',
                'cm.BL',
                'cm.VesselName',
                'cm.VoyageNo',
                'sc.CarrierName',
                'sm.ShipperName',
                'co.FullName',
                'p.POL_Name',
                'cm.CmdtTypeID',
                'cm.Status',
                'cm.Date'
            )
            ->select([
                'cm.ConsignmentID',
                'cm.BL as MainBL',
                'cm.VesselName',
                'cm.VoyageNo',
                'sc.CarrierName',
                'sm.ShipperName',
                'co.FullName as ConsigneeName',
                'p.POL_Name',
                'cm.CmdtTypeID',
                'cm.Status',
                'cm.Date',
                DB::raw('DATEDIFF(CURDATE(), cm.Date) as AgeDays'),
                DB::raw('GROUP_CONCAT(cd.ContainerNo ORDER BY cd.ContainerNo SEPARATOR ", ") as ContainerNos'),
                DB::raw('(SELECT COUNT(*) FROM manifestation_breakdown WHERE ConsignmentID = cm.ConsignmentID) as HBLCount'),
            ]);

        if ($status !== 'all') {
            $query->where('cm.Status', (int) $status);
        }

        return $query->orderBy('cm.Date', 'asc')->get();
    }

    // ── Shared summary builder ───────────────────────────────────────────────
    // Counts rows by status — used by both Print and Data methods.

    private function buildSummary($rows): array
    {
        return [
            'not_arrived' => $rows->where('Status', 1)->count(),
            'pending' => $rows->where('Status', 2)->count(),
            'gated_out' => $rows->where('Status', 3)->count(),
            'cleared' => $rows->where('Status', 0)->count(),
            'total' => $rows->count(),
        ];
    }

    // ════════════════════════════════════════════════════════════════════════
    // CONSIGNMENT STATUS SUMMARY
    // ════════════════════════════════════════════════════════════════════════

    public function consignmentStatus()
    {
        return view('reports.operations.consignment-status');
    }

    public function consignmentStatusData(Request $request)
    {
        $request->validate([
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date', 'gte:date_from'],
            'status' => ['nullable', 'in:0,1,2,3,all'],
        ]);

        $user = Auth::user();
        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;
        $status = $request->input('status', 'all');

        $rows = $this->buildConsignmentStatusQuery($dateFrom, $dateTo, $status, $user->BranchID);

        return response()->json([
            'success' => true,
            'rows' => $rows,
            'summary' => $this->buildSummary($rows),
            'dateFrom' => \Carbon\Carbon::parse($dateFrom)->format('d M Y'),
            'dateTo' => \Carbon\Carbon::parse($dateTo)->format('d M Y'),
        ]);
    }

    public function consignmentStatusPrint(Request $request)
    {
        $request->validate([
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date'],
            'status' => ['nullable', 'in:0,1,2,3,all'],
        ]);

        $user = Auth::user();
        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;
        $status = $request->input('status', 'all');

        $rows = $this->buildConsignmentStatusQuery($dateFrom, $dateTo, $status, $user->BranchID);
        $summary = $this->buildSummary($rows);
        $reportTitle = 'Consignment Status Summary';
        $dateFrom = \Carbon\Carbon::parse($dateFrom)->format('d M Y');
        $dateTo = \Carbon\Carbon::parse($dateTo)->format('d M Y');

        // $company is auto-shared by AppServiceProvider — do NOT query it here.

        return view('reports.operations.consignment-status-print', compact(
            'rows', 'summary', 'reportTitle', 'dateFrom', 'dateTo'
        ));
    }

    public function consignmentStatusExport(Request $request)
    {
        $request->validate([
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date'],
            'status' => ['nullable', 'in:0,1,2,3,all'],
        ]);

        $user = Auth::user();
        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;
        $status = $request->input('status', 'all');
        $statusLabels = [0 => 'Cleared', 1 => 'Not Arrived', 2 => 'Pending', 3 => 'Gated Out'];

        $rows = $this->buildConsignmentStatusQuery($dateFrom, $dateTo, $status, $user->BranchID);

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Consignment Status');

        // ── Header rows (company, title, period, generated-by) ──────────────
        $this->buildExcelHeader(
            $sheet,
            'Consignment Status Summary',
            \Carbon\Carbon::parse($dateFrom)->format('d M Y'),
            \Carbon\Carbon::parse($dateTo)->format('d M Y'),
            'H'
        );

        // ── Blue column header row ───────────────────────────────────────────
        $headers = ['#', 'Main BL', 'Consignee', 'Container No(s).', 'Type', 'Status', 'Age (Days)', 'Date Registered'];
        $dataRow = $this->buildExcelColumnHeaders($sheet, 6, $headers);

        // ── Data rows ────────────────────────────────────────────────────────
        foreach ($rows as $i => $r) {
            $sheet->setCellValue('A'.$dataRow, $i + 1);
            $sheet->setCellValue('B'.$dataRow, $r->MainBL ?? '-');
            $sheet->setCellValue('C'.$dataRow, $r->ConsigneeName ?? '-');
            $sheet->setCellValue('D'.$dataRow, $r->ContainerNos ?? '-');
            $sheet->setCellValue('E'.$dataRow, $r->CmdtTypeID == 1 ? 'LCL' : 'FCL');
            $sheet->setCellValue('F'.$dataRow, $statusLabels[$r->Status] ?? '-');
            $sheet->setCellValue('G'.$dataRow, $r->AgeDays);
            $sheet->setCellValue('H'.$dataRow, \Carbon\Carbon::parse($r->Date)->format('d M Y'));

            // Age warning — red bold if active and overdue
            if ($r->Status != 0 && $r->AgeDays > 7) {
                $sheet->getStyle('G'.$dataRow)->getFont()->getColor()->setRGB('B91C1C');
                $sheet->getStyle('G'.$dataRow)->getFont()->setBold(true);
            }

            $dataRow++;
        }

        // ── Column widths ────────────────────────────────────────────────────
        $widths = ['A' => 5, 'B' => 20, 'C' => 30, 'D' => 28, 'E' => 8, 'F' => 14, 'G' => 12, 'H' => 18];
        foreach ($widths as $c => $w) {
            $sheet->getColumnDimension($c)->setWidth($w);
        }

        // ── Borders + stream ─────────────────────────────────────────────────
        $this->buildExcelBorders($sheet, 6, $dataRow - 1, 'H');
        $this->streamExcel($spreadsheet, 'consignment-status-'.now()->format('Ymd-His').'.xlsx');
    }

    // ════════════════════════════════════════════════════════════════════════
    // CONSIGNMENT DETAIL REPORT
    // ════════════════════════════════════════════════════════════════════════

    public function consignmentDetail()
    {
        // Reuses the same filter page — both cards live there
        return view('reports.operations.consignment-status');
    }

    public function consignmentDetailPrint(Request $request)
    {
        $request->validate([
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date'],
            'status' => ['nullable', 'in:0,1,2,3,all'],
        ]);

        $user = Auth::user();
        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;
        $status = $request->input('status', 'all');

        $rows = $this->buildConsignmentDetailQuery($dateFrom, $dateTo, $status, $user->BranchID);
        $summary = $this->buildSummary($rows);
        $reportTitle = 'Consignment Detail Report';
        $dateFrom = \Carbon\Carbon::parse($dateFrom)->format('d M Y');
        $dateTo = \Carbon\Carbon::parse($dateTo)->format('d M Y');

        // $company is auto-shared by AppServiceProvider — do NOT query it here.

        return view('reports.operations.consignment-detail-print', compact(
            'rows', 'summary', 'reportTitle', 'dateFrom', 'dateTo'
        ));
    }

    public function consignmentDetailExport(Request $request)
    {
        $request->validate([
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date'],
            'status' => ['nullable', 'in:0,1,2,3,all'],
        ]);

        $user = Auth::user();
        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;
        $status = $request->input('status', 'all');
        $statusLabels = [0 => 'Cleared', 1 => 'Not Arrived', 2 => 'Pending', 3 => 'Gated Out'];

        $rows = $this->buildConsignmentDetailQuery($dateFrom, $dateTo, $status, $user->BranchID);

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Consignment Detail');

        // ── Header rows (company, title, period, generated-by) ──────────────
        $this->buildExcelHeader(
            $sheet,
            'Consignment Detail Report',
            \Carbon\Carbon::parse($dateFrom)->format('d M Y'),
            \Carbon\Carbon::parse($dateTo)->format('d M Y'),
            'L'
        );

        // ── Blue column header row ───────────────────────────────────────────
        $headers = ['#', 'Main BL', 'Vessel / Voyage', 'Carrier', 'Shipper', 'Consignee / HBLs', 'POL', 'Container No(s).', 'Type', 'Status', 'Age (Days)', 'Date Registered'];
        $dataRow = $this->buildExcelColumnHeaders($sheet, 6, $headers);

        // ── Data rows ────────────────────────────────────────────────────────
        foreach ($rows as $i => $r) {
            // LCL shows HBL count, FCL shows consignee name
            $consigneeCell = $r->CmdtTypeID == 1
                ? ($r->HBLCount.' HBL'.($r->HBLCount != 1 ? 's' : ''))
                : ($r->ConsigneeName ?? '-');

            $sheet->setCellValue('A'.$dataRow, $i + 1);
            $sheet->setCellValue('B'.$dataRow, $r->MainBL ?? '-');
            $sheet->setCellValue('C'.$dataRow, trim(($r->VesselName ?? '-').' / '.($r->VoyageNo ?? '-')));
            $sheet->setCellValue('D'.$dataRow, $r->CarrierName ?? '-');
            $sheet->setCellValue('E'.$dataRow, $r->ShipperName ?? '-');
            $sheet->setCellValue('F'.$dataRow, $consigneeCell);
            $sheet->setCellValue('G'.$dataRow, $r->POL_Name ?? '-');
            $sheet->setCellValue('H'.$dataRow, $r->ContainerNos ?? '-');
            $sheet->setCellValue('I'.$dataRow, $r->CmdtTypeID == 1 ? 'LCL' : 'FCL');
            $sheet->setCellValue('J'.$dataRow, $statusLabels[$r->Status] ?? '-');
            $sheet->setCellValue('K'.$dataRow, $r->AgeDays);
            $sheet->setCellValue('L'.$dataRow, \Carbon\Carbon::parse($r->Date)->format('d M Y'));

            // Age warning — red bold if active and overdue
            if ($r->Status != 0 && $r->AgeDays > 7) {
                $sheet->getStyle('K'.$dataRow)->getFont()->getColor()->setRGB('B91C1C');
                $sheet->getStyle('K'.$dataRow)->getFont()->setBold(true);
            }

            $dataRow++;
        }

        // ── Column widths ────────────────────────────────────────────────────
        $widths = [
            'A' => 5,  'B' => 20, 'C' => 24, 'D' => 18,
            'E' => 22, 'F' => 22, 'G' => 16, 'H' => 24,
            'I' => 8,  'J' => 14, 'K' => 12, 'L' => 18,
        ];
        foreach ($widths as $c => $w) {
            $sheet->getColumnDimension($c)->setWidth($w);
        }

        // ── Borders + stream ─────────────────────────────────────────────────
        $this->buildExcelBorders($sheet, 6, $dataRow - 1, 'L');
        $this->streamExcel($spreadsheet, 'consignment-detail-'.now()->format('Ymd-His').'.xlsx');
    }

    // ════════════════════════════════════════════════════════════════════════
    // CONSIGNMENT CARRIER REPORT
    // ════════════════════════════════════════════════════════════════════════

    // ── Carrier dropdown — only carriers with at least one consignment ───────
    public function carrierList(Request $request)
    {
        $request->validate([
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date'],
        ]);

        $carriers = DB::table('ship_carrier as sc')
            ->join('container_main as cm', 'sc.CarrierID', '=', 'cm.CarrierID')
            ->where('cm.Status', '!=', 9)
            ->whereBetween('cm.Date', [$request->date_from, $request->date_to])
            ->groupBy('sc.CarrierID', 'sc.CarrierName')
            ->orderBy('sc.CarrierName')
            ->get(['sc.CarrierID', 'sc.CarrierName']);

        return response()->json($carriers);
    }

    // ── Shared query builder ─────────────────────────────────────────────────
    private function buildConsignmentCarrierQuery(
        string $dateFrom,
        string $dateTo,
        string $branchID,
        ?int $carrierID = null
    ) {
        $query = DB::table('container_main as cm')
            ->leftJoin('consignee_main as co', 'cm.ConsigneeID', '=', 'co.ConsigneeID')
            ->leftJoin('ship_carrier as sc', 'cm.CarrierID', '=', 'sc.CarrierID')
            ->leftJoin('commodity_type as ct', 'cm.CmdtTypeID', '=', 'ct.TypeID')
            ->leftJoin('container_details as cd', 'cm.ConsignmentID', '=', 'cd.ConsignmentID')
            ->where('cm.BranchID', $branchID)
            ->where('cm.Status', '!=', 9)
            ->whereBetween('cm.Date', [$dateFrom, $dateTo])
            ->groupBy(
                'cm.ConsignmentID',
                'cm.BL',
                'cm.ETA',
                'co.FullName',
                'sc.CarrierName',
                'ct.TypeName',
                'cm.CmdtTypeID',
                'cm.Status',
                'cm.Date'
            )
            ->select([
                'cm.ConsignmentID',
                'cm.BL as MainBL',
                'cm.ETA',
                'co.FullName as ConsigneeName',
                'sc.CarrierName',
                'ct.TypeName as CommodityType',
                'cm.CmdtTypeID',
                'cm.Status',
                'cm.Date',
                DB::raw('DATEDIFF(CURDATE(), cm.Date) as AgeDays'),
                DB::raw('GROUP_CONCAT(cd.ContainerNo ORDER BY cd.ContainerNo SEPARATOR ", ") as ContainerNos'),
            ]);

        // Filter by carrier if a specific one is selected
        if ($carrierID) {
            $query->where('cm.CarrierID', $carrierID);
        }

        return $query->orderBy('cm.Date', 'asc')->get();
    }

    // ── Filter page ──────────────────────────────────────────────────────────
    public function consignmentCarrier()
    {
        // Reuses the same filter page — all cards live there
        return view('reports.operations.consignment-status');
    }

    // ── Print view ───────────────────────────────────────────────────────────
    public function consignmentCarrierPrint(Request $request)
    {
        $request->validate([
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date'],
            'carrier_id' => ['nullable', 'integer'],
        ]);

        $user = Auth::user();
        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;
        $carrierID = $request->carrier_id ? (int) $request->carrier_id : null;

        // Resolve carrier name for report title
        $carrierName = $carrierID
            ? DB::table('ship_carrier')->where('CarrierID', $carrierID)->value('CarrierName') ?? 'Unknown Carrier'
            : 'All Carriers';

        $rows = $this->buildConsignmentCarrierQuery($dateFrom, $dateTo, $user->BranchID, $carrierID);
        $summary = $this->buildSummary($rows);

        // LCL / FCL counts for bottom totals row
        $lclCount = $rows->where('CmdtTypeID', 1)->count();
        $fclCount = $rows->where('CmdtTypeID', '!=', 1)->count();

        $reportTitle = 'Consignment Carrier Report — '.$carrierName;
        $dateFrom = \Carbon\Carbon::parse($dateFrom)->format('d M Y');
        $dateTo = \Carbon\Carbon::parse($dateTo)->format('d M Y');

        // $company is auto-shared by AppServiceProvider — do NOT query it here.

        return view('reports.operations.consignment-carrier-print', compact(
            'rows', 'summary', 'reportTitle', 'dateFrom', 'dateTo',
            'carrierName', 'lclCount', 'fclCount'
        ));
    }

    // ── Export ───────────────────────────────────────────────────────────────
    public function consignmentCarrierExport(Request $request)
    {
        $request->validate([
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date'],
            'carrier_id' => ['nullable', 'integer'],
        ]);

        $user = Auth::user();
        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;
        $carrierID = $request->carrier_id ? (int) $request->carrier_id : null;
        $statusLabels = [0 => 'Cleared', 1 => 'Not Arrived', 2 => 'Pending', 3 => 'Gated Out'];

        $carrierName = $carrierID
            ? DB::table('ship_carrier')->where('CarrierID', $carrierID)->value('CarrierName') ?? 'Unknown Carrier'
            : 'All Carriers';

        $rows = $this->buildConsignmentCarrierQuery($dateFrom, $dateTo, $user->BranchID, $carrierID);

        $lclCount = $rows->where('CmdtTypeID', 1)->count();
        $fclCount = $rows->where('CmdtTypeID', '!=', 1)->count();

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Carrier Report');

        // ── Header rows ──────────────────────────────────────────────────────
        $this->buildExcelHeader(
            $sheet,
            'Consignment Carrier Report — '.$carrierName,
            \Carbon\Carbon::parse($dateFrom)->format('d M Y'),
            \Carbon\Carbon::parse($dateTo)->format('d M Y'),
            'I'
        );

        // Row 5 — Carrier label
        $sheet->mergeCells('A5:I5');
        $sheet->setCellValue('A5', 'Carrier: '.$carrierName);
        $sheet->getStyle('A5')->getFont()->setBold(true)->setSize(10);
        $sheet->getStyle('A5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // ── Blue column header row (row 7 — shifted down one for carrier label) ─
        $headers = ['#', 'Main BL', 'ETA', 'Consignee', 'Container No(s).', 'Commodity', 'Status', 'Age (Days)', 'Date Registered'];
        $dataRow = $this->buildExcelColumnHeaders($sheet, 7, $headers);

        // ── Data rows ────────────────────────────────────────────────────────
        foreach ($rows as $i => $r) {
            $sheet->setCellValue('A'.$dataRow, $i + 1);
            $sheet->setCellValue('B'.$dataRow, $r->MainBL ?? '-');
            $sheet->setCellValue('C'.$dataRow, $r->ETA ? \Carbon\Carbon::parse($r->ETA)->format('d M Y') : '-');
            $sheet->setCellValue('D'.$dataRow, $r->ConsigneeName ?? '-');
            $sheet->setCellValue('E'.$dataRow, $r->ContainerNos ?? '-');
            $sheet->setCellValue('F'.$dataRow, $r->CommodityType ?? '-');
            $sheet->setCellValue('G'.$dataRow, $statusLabels[$r->Status] ?? '-');
            $sheet->setCellValue('H'.$dataRow, $r->AgeDays);
            $sheet->setCellValue('I'.$dataRow, \Carbon\Carbon::parse($r->Date)->format('d M Y'));

            // Age warning — red bold if active and overdue
            if ($r->Status != 0 && $r->AgeDays > 7) {
                $sheet->getStyle('H'.$dataRow)->getFont()->getColor()->setRGB('B91C1C');
                $sheet->getStyle('H'.$dataRow)->getFont()->setBold(true);
            }

            $dataRow++;
        }

        // ── LCL / FCL totals row ─────────────────────────────────────────────
        $sheet->setCellValue('A'.$dataRow, 'Totals');
        $sheet->getStyle('A'.$dataRow)->getFont()->setBold(true);
        $sheet->setCellValue('F'.$dataRow, 'LCL: '.$lclCount.'  |  FCL: '.$fclCount);
        $sheet->getStyle('F'.$dataRow)->getFont()->setBold(true);

        // ── Column widths ────────────────────────────────────────────────────
        $widths = [
            'A' => 5,  'B' => 20, 'C' => 14, 'D' => 28,
            'E' => 26, 'F' => 20, 'G' => 14, 'H' => 12, 'I' => 18,
        ];
        foreach ($widths as $c => $w) {
            $sheet->getColumnDimension($c)->setWidth($w);
        }

        // ── Borders + stream ─────────────────────────────────────────────────
        $this->buildExcelBorders($sheet, 7, $dataRow - 1, 'I');
        $this->streamExcel($spreadsheet, 'consignment-carrier-'.now()->format('Ymd-His').'.xlsx');
    }

    // ════════════════════════════════════════════════════════════════════════
    // PORT AGING REPORT
    // ════════════════════════════════════════════════════════════════════════

    // ── Shared query builder ─────────────────────────────────────────────────
    // Age is calculated from ETA — DATEDIFF(CURDATE(), cm.ETA).
    // Includes all statuses except reversed (Status != 9).

    private function buildPortAgingQuery(
        string $dateFrom,
        string $dateTo,
        string $status,
        string $branchID
    ) {
        $query = DB::table('container_main as cm')
            ->leftJoin('consignee_main as co', 'cm.ConsigneeID', '=', 'co.ConsigneeID')
            ->leftJoin('ship_carrier as sc', 'cm.CarrierID', '=', 'sc.CarrierID')
            ->leftJoin('commodity_type as ct', 'cm.CmdtTypeID', '=', 'ct.TypeID')
            ->leftJoin('container_details as cd', 'cm.ConsignmentID', '=', 'cd.ConsignmentID')
            ->where('cm.BranchID', $branchID)
            ->where('cm.Status', '!=', 9)
            ->whereBetween('cm.Date', [$dateFrom, $dateTo])
            ->groupBy(
                'cm.ConsignmentID',
                'cm.BL',
                'cm.ETA',
                'cm.Date',
                'co.FullName',
                'sc.CarrierName',
                'ct.TypeName',
                'cm.CmdtTypeID',
                'cm.Status'
            )
            ->select([
                'cm.ConsignmentID',
                'cm.BL as MainBL',
                'cm.ETA',
                'cm.Date',
                'co.FullName as ConsigneeName',
                'sc.CarrierName',
                'ct.TypeName as CommodityType',
                'cm.CmdtTypeID',
                'cm.Status',
                DB::raw('DATEDIFF(CURDATE(), cm.ETA) as AgeDays'),
                DB::raw('GROUP_CONCAT(cd.ContainerNo ORDER BY cd.ContainerNo SEPARATOR ", ") as ContainerNos'),
            ]);

        if ($status !== 'all') {
            $query->where('cm.Status', (int) $status);
        }

        return $query->orderBy('AgeDays', 'desc')->get();
    }

    // ── Age bracket helper ───────────────────────────────────────────────────
    // Returns bracket label, CSS class and Excel colour for a given age in days.

    private function ageBracket(int $days): array
    {
        if ($days <= 7) {
            return ['label' => 'Fresh',    'cls' => 'age-fresh',    'hex' => '15803d'];
        }
        if ($days <= 14) {
            return ['label' => 'Warning',  'cls' => 'age-warning',  'hex' => 'b45309'];
        }
        if ($days <= 30) {
            return ['label' => 'Critical', 'cls' => 'age-critical', 'hex' => 'c2410c'];
        }

        return ['label' => 'Overdue',  'cls' => 'age-overdue',  'hex' => 'b91c1c'];
    }

    // ── Age summary builder ──────────────────────────────────────────────────
    private function buildAgeSummary($rows): array
    {
        return [
            'fresh' => $rows->filter(fn ($r) => $r->AgeDays <= 7)->count(),
            'warning' => $rows->filter(fn ($r) => $r->AgeDays > 7 && $r->AgeDays <= 14)->count(),
            'critical' => $rows->filter(fn ($r) => $r->AgeDays > 14 && $r->AgeDays <= 30)->count(),
            'overdue' => $rows->filter(fn ($r) => $r->AgeDays > 30)->count(),
            'total' => $rows->count(),
        ];
    }

    // ── Filter page ──────────────────────────────────────────────────────────
    public function portAging()
    {
        return view('reports.operations.consignment-status');
    }

    // ── Print view ───────────────────────────────────────────────────────────
    public function portAgingPrint(Request $request)
    {
        $request->validate([
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date'],
            'status' => ['nullable', 'in:0,1,2,3,all'],
        ]);

        $user = Auth::user();
        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;
        $status = $request->input('status', 'all');

        $rows = $this->buildPortAgingQuery($dateFrom, $dateTo, $status, $user->BranchID);
        $summary = $this->buildSummary($rows);
        $ageSummary = $this->buildAgeSummary($rows);
        $lclCount = $rows->where('CmdtTypeID', 1)->count();
        $fclCount = $rows->where('CmdtTypeID', '!=', 1)->count();

        $reportTitle = 'Port Aging Report';
        $dateFrom = \Carbon\Carbon::parse($dateFrom)->format('d M Y');
        $dateTo = \Carbon\Carbon::parse($dateTo)->format('d M Y');

        // $company is auto-shared by AppServiceProvider — do NOT query it here.

        return view('reports.operations.port-aging-print', compact(
            'rows', 'summary', 'ageSummary', 'lclCount', 'fclCount',
            'reportTitle', 'dateFrom', 'dateTo'
        ));
    }

    // ── Export ───────────────────────────────────────────────────────────────
    public function portAgingExport(Request $request)
    {
        $request->validate([
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date'],
            'status' => ['nullable', 'in:0,1,2,3,all'],
        ]);

        $user = Auth::user();
        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;
        $status = $request->input('status', 'all');
        $statusLabels = [0 => 'Cleared', 1 => 'Not Arrived', 2 => 'Pending', 3 => 'Gated Out'];

        $rows = $this->buildPortAgingQuery($dateFrom, $dateTo, $status, $user->BranchID);
        $lclCount = $rows->where('CmdtTypeID', 1)->count();
        $fclCount = $rows->where('CmdtTypeID', '!=', 1)->count();

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Port Aging');

        // ── Header rows ──────────────────────────────────────────────────────
        $this->buildExcelHeader(
            $sheet,
            'Port Aging Report',
            \Carbon\Carbon::parse($dateFrom)->format('d M Y'),
            \Carbon\Carbon::parse($dateTo)->format('d M Y'),
            'J'
        );

        // ── Blue column header row ───────────────────────────────────────────
        $headers = ['#', 'Main BL', 'ETA', 'Consignee', 'Carrier', 'Container No(s).', 'Commodity', 'Status', 'Age (Days)', 'Date Registered'];
        $dataRow = $this->buildExcelColumnHeaders($sheet, 6, $headers);

        // ── Data rows ────────────────────────────────────────────────────────
        foreach ($rows as $i => $r) {
            $bracket = $this->ageBracket((int) $r->AgeDays);

            $sheet->setCellValue('A'.$dataRow, $i + 1);
            $sheet->setCellValue('B'.$dataRow, $r->MainBL ?? '-');
            $sheet->setCellValue('C'.$dataRow, $r->ETA ? \Carbon\Carbon::parse($r->ETA)->format('d M Y') : '-');
            $sheet->setCellValue('D'.$dataRow, $r->ConsigneeName ?? '-');
            $sheet->setCellValue('E'.$dataRow, $r->CarrierName ?? '-');
            $sheet->setCellValue('F'.$dataRow, $r->ContainerNos ?? '-');
            $sheet->setCellValue('G'.$dataRow, $r->CommodityType ?? '-');
            $sheet->setCellValue('H'.$dataRow, $statusLabels[$r->Status] ?? '-');
            $sheet->setCellValue('I'.$dataRow, $r->AgeDays);
            $sheet->setCellValue('J'.$dataRow, \Carbon\Carbon::parse($r->Date)->format('d M Y'));

            // Age colour coding per bracket
            $sheet->getStyle('I'.$dataRow)->getFont()
                ->getColor()->setRGB($bracket['hex']);

            // Bold for critical and overdue
            if ($r->AgeDays > 14) {
                $sheet->getStyle('I'.$dataRow)->getFont()->setBold(true);
            }

            $dataRow++;
        }

        // ── LCL / FCL totals row ─────────────────────────────────────────────
        $sheet->setCellValue('A'.$dataRow, 'Totals');
        $sheet->getStyle('A'.$dataRow)->getFont()->setBold(true);
        $sheet->setCellValue('G'.$dataRow, 'LCL: '.$lclCount.'  |  FCL: '.$fclCount);
        $sheet->getStyle('G'.$dataRow)->getFont()->setBold(true);

        // ── Column widths ────────────────────────────────────────────────────
        $widths = [
            'A' => 5,  'B' => 20, 'C' => 14, 'D' => 26,
            'E' => 20, 'F' => 24, 'G' => 18, 'H' => 14,
            'I' => 12, 'J' => 18,
        ];
        foreach ($widths as $c => $w) {
            $sheet->getColumnDimension($c)->setWidth($w);
        }

        // ── Borders + stream ─────────────────────────────────────────────────
        $this->buildExcelBorders($sheet, 6, $dataRow - 1, 'J');
        $this->streamExcel($spreadsheet, 'port-aging-'.now()->format('Ymd-His').'.xlsx');
    }

    // ════════════════════════════════════════════════════════════════════════
    // PENDING CLEARANCE REPORT
    // ════════════════════════════════════════════════════════════════════════

    // ── Shared query builder ─────────────────────────────────────────────────
    // Filters by ETA date range, not registration date.
    // Status 9 (reversed) always excluded.

    private function buildPendingClearanceQuery(
        string $dateFrom,
        string $dateTo,
        string $status,
        string $branchID
    ) {
        $query = DB::table('container_main as cm')
            ->leftJoin('consignee_main as co', 'cm.ConsigneeID', '=', 'co.ConsigneeID')
            ->leftJoin('ship_carrier as sc', 'cm.CarrierID', '=', 'sc.CarrierID')
            ->leftJoin('commodity_type as ct', 'cm.CmdtTypeID', '=', 'ct.TypeID')
            ->leftJoin('container_details as cd', 'cm.ConsignmentID', '=', 'cd.ConsignmentID')
            ->where('cm.BranchID', $branchID)
            ->where('cm.Status', '!=', 9)
            ->whereBetween('cm.ETA', [$dateFrom, $dateTo])  // filter by ETA not Date
            ->groupBy(
                'cm.ConsignmentID',
                'cm.BL',
                'cm.ETA',
                'cm.Date',
                'co.FullName',
                'co.TelNo',
                'sc.CarrierName',
                'ct.TypeName',
                'cm.CmdtTypeID',
                'cm.Status'
            )
            ->select([
                'cm.ConsignmentID',
                'cm.BL as MainBL',
                'cm.ETA',
                'cm.Date',
                'co.FullName as ConsigneeName',
                'co.TelNo as ConsigneeTel',         // for combined consignee+phone column
                'sc.CarrierName',
                'ct.TypeName as CommodityType',
                'cm.CmdtTypeID',
                'cm.Status',
                DB::raw('DATEDIFF(CURDATE(), cm.ETA) as DaysOverdue'),
                DB::raw('GROUP_CONCAT(cd.ContainerNo ORDER BY cd.ContainerNo SEPARATOR ", ") as ContainerNos'),
            ]);

        if ($status !== 'all') {
            $query->where('cm.Status', (int) $status);
        }

        // Most overdue first
        return $query->orderBy('DaysOverdue', 'desc')->get();
    }

    // ── Pending clearance summary — by status (excludes Cleared by default) ──
    private function buildPendingClearanceSummary($rows): array
    {
        return [
            'not_arrived' => $rows->where('Status', 1)->count(),
            'pending' => $rows->where('Status', 2)->count(),
            'gated_out' => $rows->where('Status', 3)->count(),
            'cleared' => $rows->where('Status', 0)->count(),
            'total' => $rows->count(),
        ];
    }

    // ── Overdue bracket summary ──────────────────────────────────────────────
    private function buildOverdueSummary($rows): array
    {
        return [
            'one_to_seven' => $rows->filter(fn ($r) => $r->DaysOverdue >= 1 && $r->DaysOverdue <= 7)->count(),
            'eight_to_fourteen' => $rows->filter(fn ($r) => $r->DaysOverdue >= 8 && $r->DaysOverdue <= 14)->count(),
            'fifteen_to_thirty' => $rows->filter(fn ($r) => $r->DaysOverdue >= 15 && $r->DaysOverdue <= 30)->count(),
            'over_thirty' => $rows->filter(fn ($r) => $r->DaysOverdue > 30)->count(),
            'total' => $rows->count(),
        ];
    }

    // ── Filter page ──────────────────────────────────────────────────────────
    public function pendingClearance()
    {
        return view('reports.operations.consignment-status');
    }

    // ── Print view ───────────────────────────────────────────────────────────
    public function pendingClearancePrint(Request $request)
    {
        $request->validate([
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date'],
            'status' => ['nullable', 'in:0,1,2,3,all'],
        ]);

        $user = Auth::user();
        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;
        $status = $request->input('status', 'all');

        $rows = $this->buildPendingClearanceQuery($dateFrom, $dateTo, $status, $user->BranchID);
        $summary = $this->buildPendingClearanceSummary($rows);
        $overdueSummary = $this->buildOverdueSummary($rows);

        $reportTitle = 'Pending Clearance Report';
        $dateFrom = \Carbon\Carbon::parse($dateFrom)->format('d M Y');
        $dateTo = \Carbon\Carbon::parse($dateTo)->format('d M Y');

        // $company is auto-shared by AppServiceProvider — do NOT query it here.

        return view('reports.operations.pending-clearance-print', compact(
            'rows', 'summary', 'overdueSummary',
            'reportTitle', 'dateFrom', 'dateTo'
        ));
    }

    // ── Export ───────────────────────────────────────────────────────────────
    public function pendingClearanceExport(Request $request)
    {
        $request->validate([
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date'],
            'status' => ['nullable', 'in:0,1,2,3,all'],
        ]);

        $user = Auth::user();
        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;
        $status = $request->input('status', 'all');
        $statusLabels = [0 => 'Cleared', 1 => 'Not Arrived', 2 => 'Pending', 3 => 'Gated Out'];

        $rows = $this->buildPendingClearanceQuery($dateFrom, $dateTo, $status, $user->BranchID);

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Pending Clearance');

        // ── Header rows ──────────────────────────────────────────────────────
        $this->buildExcelHeader(
            $sheet,
            'Pending Clearance Report',
            \Carbon\Carbon::parse($dateFrom)->format('d M Y'),
            \Carbon\Carbon::parse($dateTo)->format('d M Y'),
            'K'
        );

        // ── Blue column header row ───────────────────────────────────────────
        $headers = [
            '#', 'Main BL', 'ETA', 'Days Overdue', 'Consignee',
            'Phone', 'Carrier', 'Container No(s).', 'Commodity', 'Status', 'Date Registered',
        ];
        $dataRow = $this->buildExcelColumnHeaders($sheet, 6, $headers);

        // ── Data rows ────────────────────────────────────────────────────────
        foreach ($rows as $i => $r) {
            $days = (int) $r->DaysOverdue;
            $bracket = $this->ageBracket($days);

            $sheet->setCellValue('A'.$dataRow, $i + 1);
            $sheet->setCellValue('B'.$dataRow, $r->MainBL ?? '-');
            $sheet->setCellValue('C'.$dataRow, $r->ETA
                ? \Carbon\Carbon::parse($r->ETA)->format('d M Y') : '-');
            $sheet->setCellValue('D'.$dataRow, $days);
            $sheet->setCellValue('E'.$dataRow, $r->ConsigneeName ?? '-');
            $sheet->setCellValue('F'.$dataRow, $r->ConsigneeTel ?? '-');
            $sheet->setCellValue('G'.$dataRow, $r->CarrierName ?? '-');
            $sheet->setCellValue('H'.$dataRow, $r->ContainerNos ?? '-');
            $sheet->setCellValue('I'.$dataRow, $r->CommodityType ?? '-');
            $sheet->setCellValue('J'.$dataRow, $statusLabels[$r->Status] ?? '-');
            $sheet->setCellValue('K'.$dataRow, \Carbon\Carbon::parse($r->Date)->format('d M Y'));

            // Days overdue colour per bracket
            $sheet->getStyle('D'.$dataRow)->getFont()
                ->getColor()->setRGB($bracket['hex']);

            if ($days > 14) {
                $sheet->getStyle('D'.$dataRow)->getFont()->setBold(true);
            }

            $dataRow++;
        }

        // ── Column widths ────────────────────────────────────────────────────
        $widths = [
            'A' => 5,  'B' => 20, 'C' => 14, 'D' => 14,
            'E' => 26, 'F' => 16, 'G' => 18, 'H' => 24,
            'I' => 18, 'J' => 14, 'K' => 18,
        ];
        foreach ($widths as $c => $w) {
            $sheet->getColumnDimension($c)->setWidth($w);
        }

        // ── Borders + stream ─────────────────────────────────────────────────
        $this->buildExcelBorders($sheet, 6, $dataRow - 1, 'K');
        $this->streamExcel(
            $spreadsheet,
            'pending-clearance-'.now()->format('Ymd-His').'.xlsx'
        );
    }

    // ════════════════════════════════════════════════════════════════════════
    // CONSIGNMENT DETAIL MODAL — AJAX
    // ════════════════════════════════════════════════════════════════════════
    // Returns all data needed for the modal: timeline dates, consignee details,
    // manifest breakdown, and disbursement payment summary.

    public function consignmentModal(int $consignmentId)
    {
        $user = Auth::user();

        // ── Core consignment ─────────────────────────────────────────────────
        $consignment = DB::table('container_main as cm')
            ->leftJoin('consignee_main as co', 'cm.ConsigneeID', '=', 'co.ConsigneeID')
            ->leftJoin('ship_carrier as sc', 'cm.CarrierID', '=', 'sc.CarrierID')
            ->leftJoin('shipper_main as sm', 'cm.ShipperID', '=', 'sm.ShipperID')
            ->leftJoin('commodity_type as ct', 'cm.CmdtTypeID', '=', 'ct.TypeID')
            ->leftJoin('pol as p', 'cm.POL_ID', '=', 'p.POL_ID')
            ->leftJoin('pod as pd', 'cm.POD_ID', '=', 'pd.POD_ID')
            ->where('cm.ConsignmentID', $consignmentId)
            ->where('cm.BranchID', $user->BranchID)
            ->select([
                'cm.ConsignmentID',
                'cm.BL as MainBL',
                'cm.VesselName',
                'cm.VoyageNo',
                'cm.ETA',
                'cm.SOB',
                'cm.DOIS',
                'cm.Date as RegisteredDate',
                'cm.Status',
                'cm.CmdtTypeID',
                'cm.ContainerSize',
                'cm.Rotation',
                'sc.CarrierName',
                'sm.ShipperName',
                'ct.TypeName as CommodityType',
                'p.POL_Name',
                'pd.POD_Name',
                'co.FullName as ConsigneeName',
                'co.TelNo as ConsigneeTel',
                'co.Address1 as ConsigneeAddress',
                DB::raw('DATEDIFF(CURDATE(), cm.ETA) as DaysOverdue'),
            ])
            ->first();

        if (! $consignment) {
            return response()->json([
                'success' => false,
                'message' => 'Consignment not found.',
            ], 404);
        }

        // ── Container details — gate out + return dates ───────────────────────
        $containers = DB::table('container_details')
            ->where('ConsignmentID', $consignmentId)
            ->where('Status', '!=', 9)
            ->get([
                'ContainerNo',
                'ContainerSize',
                'Weight',
                'GateOutDate',
                'ReturnDate',
                'Status',
            ]);

        // ── Timeline — derive dates from available data ───────────────────────
        // First disbursement date for this BL
        $firstDisbursementDate = DB::table('disbursement_analysis')
            ->where('BL', $consignment->MainBL)
            ->min('Date');

        // Gate out date — earliest across all containers
        $gateOutDate = $containers
            ->whereNotNull('GateOutDate')
            ->where('GateOutDate', '!=', '0000-00-00')
            ->min('GateOutDate');

        // Return date — latest across all containers
        $returnDate = $containers
            ->whereNotNull('ReturnDate')
            ->where('ReturnDate', '!=', '0000-00-00')
            ->max('ReturnDate');

        $timeline = [
            [
                'stage' => 'BL Registered',
                'date' => $consignment->RegisteredDate,
                'done' => true,
            ],
            [
                'stage' => 'Shipped on Board',
                'date' => $consignment->SOB !== '1970-01-01' ? $consignment->SOB : null,
                'done' => $consignment->SOB && $consignment->SOB !== '1970-01-01',
            ],
            [
                'stage' => 'ETA / Arrived',
                'date' => $consignment->ETA,
                'done' => now()->toDateString() >= $consignment->ETA,
            ],
            [
                'stage' => 'Disbursement',
                'date' => $firstDisbursementDate,
                'done' => (bool) $firstDisbursementDate,
            ],
            [
                'stage' => 'Gate Out',
                'date' => $gateOutDate,
                'done' => (bool) $gateOutDate,
            ],
            [
                'stage' => 'Cleared',
                'date' => $consignment->Status == 0 ? now()->toDateString() : null,
                'done' => $consignment->Status == 0,
            ],
            [
                'stage' => 'Container Return',
                'date' => $returnDate,
                'done' => (bool) $returnDate,
            ],
        ];

        // ── Manifest breakdown ────────────────────────────────────────────────
        $manifest = DB::table('manifestation_breakdown as mb')
            ->leftJoin('consignee_main as co', 'mb.ConsigneeID', '=', 'co.ConsigneeID')
            ->where('mb.ConsignmentID', $consignmentId)
            ->where('mb.Status', '!=', 9)
            ->orderBy('mb.HouseBL')
            ->get([
                'mb.HouseBL',
                'co.FullName as ConsigneeName',
                'co.TelNo as ConsigneeTel',
                'mb.Description',
                'mb.ItemType',
                'mb.Weight',
                'mb.Package',
                'mb.Unit',
            ]);

        // ── Disbursement payment summary ──────────────────────────────────────
        $payments = DB::table('disbursement_analysis as da')
            ->leftJoin('ledger_account as la', 'da.AccountID', '=', 'la.AccountNo')
            ->where('da.BL', $consignment->MainBL)
            ->orderBy('da.Date', 'asc')
            ->get([
                'da.HBL',
                'da.ReceiptNo',
                'da.Date',
                'da.Expenditure',
                'da.Revenue',
                'la.AccountName',
            ]);

        $totalExpenditure = $payments->sum('Expenditure');
        $totalRevenue = $payments->sum('Revenue');

        return response()->json([
            'success' => true,
            'consignment' => $consignment,
            'containers' => $containers,
            'timeline' => $timeline,
            'manifest' => $manifest,
            'payments' => $payments,
            'totalExpenditure' => $totalExpenditure,
            'totalRevenue' => $totalRevenue,
        ]);
    }
}
