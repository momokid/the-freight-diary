<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class OperationsReportController extends Controller
{
    // ── Consignment Status Summary ───────────────────────────────────────────

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

        $query = DB::table('container_main as cm')
            ->leftJoin('consignee_main as co', 'cm.ConsigneeID', '=', 'co.ConsigneeID')
            ->leftJoin('container_details as cd', 'cm.ConsignmentID', '=', 'cd.ConsignmentID')
            ->where('cm.BranchID', $user->BranchID)
            ->where('cm.Status', '!=', 9)
            ->whereBetween('cm.Date', [$dateFrom, $dateTo])
            ->select([
                'cm.ConsignmentID',
                'cm.BL as MainBL',
                'co.FullName as ConsigneeName',
                'cd.ContainerNo',
                'cm.CmdtTypeID',
                'cm.Status',
                'cm.Date',
                DB::raw('DATEDIFF(CURDATE(), cm.Date) as AgeDays'),
            ]);

        if ($status !== 'all') {
            $query->where('cm.Status', $status);
        }

        $rows = $query->orderBy('cm.Date', 'asc')->get();

        // Summary counts
        $summary = [
            'not_arrived' => $rows->where('Status', 1)->count(),
            'pending' => $rows->where('Status', 2)->count(),
            'gated_out' => $rows->where('Status', 3)->count(),
            'cleared' => $rows->where('Status', 0)->count(),
            'total' => $rows->count(),
        ];

        return response()->json([
            'success' => true,
            'rows' => $rows,
            'summary' => $summary,
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

        $query = DB::table('container_main as cm')
            ->leftJoin('consignee_main as co', 'cm.ConsigneeID', '=', 'co.ConsigneeID')
            ->leftJoin('container_details as cd', 'cm.ConsignmentID', '=', 'cd.ConsignmentID')
            ->where('cm.BranchID', $user->BranchID)
            ->where('cm.Status', '!=', 9)
            ->whereBetween('cm.Date', [$dateFrom, $dateTo])
            ->select([
                'cm.ConsignmentID',
                'cm.BL as MainBL',
                'co.FullName as ConsigneeName',
                'cd.ContainerNo',
                'cm.CmdtTypeID',
                'cm.Status',
                'cm.Date',
                DB::raw('DATEDIFF(CURDATE(), cm.Date) as AgeDays'),
            ]);

        if ($status !== 'all') {
            $query->where('cm.Status', $status);
        }

        $rows = $query->orderBy('cm.Date', 'asc')->get();

        $summary = [
            'not_arrived' => $rows->where('Status', 1)->count(),
            'pending' => $rows->where('Status', 2)->count(),
            'gated_out' => $rows->where('Status', 3)->count(),
            'cleared' => $rows->where('Status', 0)->count(),
            'total' => $rows->count(),
        ];

        $reportTitle = 'Consignment Status Summary';
        $dateFrom = \Carbon\Carbon::parse($dateFrom)->format('d M Y');
        $dateTo = \Carbon\Carbon::parse($dateTo)->format('d M Y');

        $company = DB::table('company_main')->where('BranchID', $user->BranchID)->first();

        return view('reports.operations.consignment-status-print', compact(
            'rows', 'summary', 'reportTitle', 'dateFrom', 'dateTo', 'company'
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

        $query = DB::table('container_main as cm')
            ->leftJoin('consignee_main as co', 'cm.ConsigneeID', '=', 'co.ConsigneeID')
            ->leftJoin('container_details as cd', 'cm.ConsignmentID', '=', 'cd.ConsignmentID')
            ->where('cm.BranchID', $user->BranchID)
            ->where('cm.Status', '!=', 9)
            ->whereBetween('cm.Date', [$dateFrom, $dateTo])
            ->select([
                'cm.ConsignmentID',
                'cm.BL as MainBL',
                'co.FullName as ConsigneeName',
                'cd.ContainerNo',
                'cm.CmdtTypeID',
                'cm.Status',
                'cm.Date',
                DB::raw('DATEDIFF(CURDATE(), cm.Date) as AgeDays'),
            ]);

        if ($status !== 'all') {
            $query->where('cm.Status', $status);
        }

        $rows = $query->orderBy('cm.Date', 'asc')->get();

        $statusLabels = [0 => 'Cleared', 1 => 'Not Arrived', 2 => 'Pending', 3 => 'Gated Out'];

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Consignment Status');

        // ── Company header ───────────────────────────────────────────────────
        $sheet->mergeCells('A1:H1');
        $sheet->setCellValue('A1', $user->BranchID ?? 'Prime Survivors International Ltd');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A2:H2');
        $sheet->setCellValue('A2', 'Consignment Status Summary');
        $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A3:H3');
        $sheet->setCellValue('A3', 'Period: '.\Carbon\Carbon::parse($dateFrom)->format('d M Y').' — '.\Carbon\Carbon::parse($dateTo)->format('d M Y'));
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A4:H4');
        $sheet->setCellValue('A4', 'Generated: '.now()->format('d M Y, h:i A').'  |  By: '.(Auth::user()->FullName ?? Auth::user()->ID));
        $sheet->getStyle('A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A4')->getFont()->setSize(9);

        // ── Column headers ───────────────────────────────────────────────────
        $headers = ['#', 'Main BL', 'House BL', 'Consignee', 'Container No.', 'Type', 'Status', 'Age (Days)', 'Date Registered'];
        $col = 'A';
        $row = 6;
        foreach ($headers as $header) {
            $sheet->setCellValue($col.$row, $header);
            $sheet->getStyle($col.$row)->getFont()->setBold(true);
            $sheet->getStyle($col.$row)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('185FA5');
            $sheet->getStyle($col.$row)->getFont()->getColor()->setRGB('FFFFFF');
            $sheet->getStyle($col.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $col++;
        }

        // ── Data rows ────────────────────────────────────────────────────────
        $dataRow = 7;
        foreach ($rows as $i => $r) {
            $sheet->setCellValue('A'.$dataRow, $i + 1);
            $sheet->setCellValue('B'.$dataRow, $r->MainBL);
            $sheet->setCellValue('C'.$dataRow, $r->HouseBL ?? '-');
            $sheet->setCellValue('D'.$dataRow, $r->ConsigneeName ?? '-');
            $sheet->setCellValue('E'.$dataRow, $r->ContainerNo ?? '-');
            $sheet->setCellValue('F'.$dataRow, $r->CmdtTypeID == 1 ? 'LCL' : 'FCL');
            $sheet->setCellValue('G'.$dataRow, $statusLabels[$r->Status] ?? '-');
            $sheet->setCellValue('H'.$dataRow, $r->AgeDays);
            $sheet->setCellValue('I'.$dataRow, \Carbon\Carbon::parse($r->Date)->format('d M Y'));

            // Highlight overdue rows
            if ($r->AgeDays > 7) {
                $sheet->getStyle('H'.$dataRow)->getFont()->getColor()->setRGB('A32D2D');
                $sheet->getStyle('H'.$dataRow)->getFont()->setBold(true);
            }

            $dataRow++;
        }

        // ── Column widths ────────────────────────────────────────────────────
        $widths = ['A' => 5, 'B' => 18, 'C' => 18, 'D' => 30, 'E' => 18, 'F' => 8, 'G' => 14, 'H' => 12, 'I' => 16];
        foreach ($widths as $c => $w) {
            $sheet->getColumnDimension($c)->setWidth($w);
        }

        // ── Border on data ───────────────────────────────────────────────────
        if ($dataRow > 7) {
            $sheet->getStyle('A6:I'.($dataRow - 1))->getBorders()->getAllBorders()
                ->setBorderStyle(Border::BORDER_THIN);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'consignment-status-'.now()->format('Ymd-His').'.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}
