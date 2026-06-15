<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientReportController extends BaseReportController
{
    // ════════════════════════════════════════════════════════════════════════
    // INDEX — search page
    // ════════════════════════════════════════════════════════════════════════

    public function index()
    {
        return view('reports.client.index');
    }

    // ════════════════════════════════════════════════════════════════════════
    // SEARCH — AJAX consignee typeahead
    // ════════════════════════════════════════════════════════════════════════

    public function search(Request $request)
    {
        $request->validate([
            'q'         => ['required', 'string', 'min:2', 'max:100'],
            'date_from' => ['nullable', 'date'],
            'date_to'   => ['nullable', 'date'],
        ]);

        $q        = $request->q;
        $dateFrom = $request->date_from;
        $dateTo   = $request->date_to;

        $query = DB::table('consignee_main as co')
            ->join('container_main as cm', 'co.ConsigneeID', '=', 'cm.ConsigneeID')
            ->where('cm.Status', '!=', 9)
            ->where('co.FullName', 'like', '%' . $q . '%')
            ->groupBy('co.ConsigneeID', 'co.FullName', 'co.TelNo');

        if ($dateFrom) $query->where('cm.Date', '>=', $dateFrom);
        if ($dateTo)   $query->where('cm.Date', '<=', $dateTo);

        $results = $query
            ->orderBy('co.FullName')
            ->limit(10)
            ->get([
                'co.ConsigneeID',
                'co.FullName',
                'co.TelNo',
                DB::raw('COUNT(cm.ConsignmentID) as ConsignmentCount'),
            ]);

        return response()->json($results);
    }

    // ════════════════════════════════════════════════════════════════════════
    // PROFILE — AJAX full profile data
    // ════════════════════════════════════════════════════════════════════════

    public function profile(Request $request, int $consigneeId)
    {
        $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to'   => ['nullable', 'date'],
        ]);

        $data = $this->buildProfileData(
            $consigneeId,
            $request->date_from,
            $request->date_to
        );

        if (! $data) {
            return response()->json([
                'success' => false,
                'message' => 'Consignee not found.',
            ], 404);
        }

        return response()->json(array_merge(['success' => true], $data));
    }

    // ════════════════════════════════════════════════════════════════════════
    // PROFILE PRINT
    // ════════════════════════════════════════════════════════════════════════

    public function profilePrint(Request $request, int $consigneeId)
    {
        $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to'   => ['nullable', 'date'],
        ]);

        $data = $this->buildProfileData(
            $consigneeId,
            $request->date_from,
            $request->date_to
        );

        if (! $data) {
            abort(404, 'Consignee not found.');
        }

        $data['dateFrom'] = $request->date_from
            ? \Carbon\Carbon::parse($request->date_from)->format('d M Y')
            : 'All Time';
        $data['dateTo'] = $request->date_to
            ? \Carbon\Carbon::parse($request->date_to)->format('d M Y')
            : now()->format('d M Y');

        return view('reports.client.client-profile-print', $data);
    }

    // ════════════════════════════════════════════════════════════════════════
    // PRIVATE — build all profile data
    // Called by both profile() and profilePrint() directly
    // Returns null if consignee not found
    // ════════════════════════════════════════════════════════════════════════

    private function buildProfileData(
        int $consigneeId,
        ?string $dateFrom,
        ?string $dateTo
    ): ?array {

        // ── Consignee details ─────────────────────────────────────────────
        $consignee = DB::table('consignee_main')
            ->where('ConsigneeID', $consigneeId)
            ->first();

        if (! $consignee) return null;

        // Member since — earliest consignment date
        $memberSince = DB::table('container_main')
            ->where('ConsigneeID', $consigneeId)
            ->where('Status', '!=', 9)
            ->min('Date');

        // ── Consignments (FCL) ────────────────────────────────────────────
        $consignmentQuery = DB::table('container_main as cm')
            ->leftJoin('ship_carrier as sc', 'cm.CarrierID', '=', 'sc.CarrierID')
            ->leftJoin('commodity_type as ct', 'cm.CmdtTypeID', '=', 'ct.TypeID')
            ->leftJoin('container_details as cd', function ($join) {
                $join->on('cm.ConsignmentID', '=', 'cd.ConsignmentID')
                    ->on('cm.BL', '=', 'cd.BL');
            })
            ->where('cm.ConsigneeID', $consigneeId)
            ->where('cm.Status', '!=', 9)
            ->when($dateFrom, fn($q) => $q->where('cm.Date', '>=', $dateFrom))
            ->when($dateTo,   fn($q) => $q->where('cm.Date', '<=', $dateTo));

        $consignments = $consignmentQuery
            ->groupBy(
                'cm.ConsignmentID',
                'cm.BL',
                'cm.ETA',
                'cm.Date',
                'cm.Status',
                'cm.CmdtTypeID',
                'sc.CarrierName',
                'ct.TypeName'
            )
            ->orderBy('cm.Date', 'desc')
            ->get([
                'cm.ConsignmentID',
                'cm.BL as MainBL',
                'cm.ETA',
                'cm.Date',
                'cm.Status',
                'cm.CmdtTypeID',
                'sc.CarrierName',
                'ct.TypeName as CommodityType',
                DB::raw('DATEDIFF(CURDATE(), cm.ETA) as AgeDays'),
                DB::raw('GROUP_CONCAT(cd.ContainerNo ORDER BY cd.ContainerNo SEPARATOR ", ") as ContainerNos'),
            ]);

        // ── HBL entries (LCL) ─────────────────────────────────────────────
        $hblEntries = DB::table('manifestation_breakdown as mb')
            ->leftJoin('container_main as cm', 'mb.ConsignmentID', '=', 'cm.ConsignmentID')
            ->leftJoin('ship_carrier as sc', 'cm.CarrierID', '=', 'sc.CarrierID')
            ->where('mb.ConsigneeID', $consigneeId)
            ->where('mb.Status', '!=', 9)
            ->when($dateFrom, fn($q) => $q->where('cm.Date', '>=', $dateFrom))
            ->when($dateTo,   fn($q) => $q->where('cm.Date', '<=', $dateTo))
            ->orderBy('cm.Date', 'desc')
            ->get([
                'mb.ConsignmentID',
                'mb.MainBL',
                'mb.HouseBL',
                'mb.Description',
                'mb.Weight',
                'mb.Package',
                'mb.Unit',
                'cm.ETA',
                'cm.Date',
                'cm.Status',
                'sc.CarrierName',
                DB::raw('DATEDIFF(CURDATE(), cm.ETA) as AgeDays'),
            ]);

        // ── Consignment summary ───────────────────────────────────────────
        $consignmentSummary = [
            'total'       => $consignments->count(),
            'hbl_total'   => $hblEntries->count(),
            'not_arrived' => $consignments->where('Status', 1)->count(),
            'pending'     => $consignments->where('Status', 2)->count(),
            'gated_out'   => $consignments->where('Status', 3)->count(),
            'cleared'     => $consignments->where('Status', 0)->count(),
        ];

        $mostUsedCarrier = $consignments
            ->groupBy('CarrierName')
            ->map(fn($g) => $g->count())
            ->sortDesc()
            ->keys()
            ->first() ?? '—';

        $clearedConsignments = $consignments->where('Status', 0);
        $avgDaysToClear = $clearedConsignments->count() > 0
            ? round($clearedConsignments->avg('AgeDays'), 1)
            : null;

        // ── Invoice & revenue summary ─────────────────────────────────────
        $invoiceSummary = DB::table('student_fee')
            ->where('StudentID', $consigneeId)
            ->where('Status', 1)
            ->when($dateFrom, fn($q) => $q->where('Date', '>=', $dateFrom))
            ->when($dateTo,   fn($q) => $q->where('Date', '<=', $dateTo))
            ->selectRaw('
                ROUND(SUM(Dr), 2) as TotalInvoiced,
                ROUND(SUM(Cr), 2) as TotalPaid,
                ROUND(SUM(Dr) - SUM(Cr), 2) as Outstanding
            ')
            ->first();

        $invoices = DB::table('student_fee as sf')
            ->leftJoin('ledger_account as la', 'sf.AccountNo', '=', 'la.AccountNo')
            ->where('sf.StudentID', $consigneeId)
            ->where('sf.Status', 1)
            ->when($dateFrom, fn($q) => $q->where('sf.Date', '>=', $dateFrom))
            ->when($dateTo,   fn($q) => $q->where('sf.Date', '<=', $dateTo))
            ->orderBy('sf.Date', 'desc')
            ->get([
                'sf.SubClassID as HouseBL',
                'sf.CouponID as MainBL',
                'sf.ReceiptNo',
                'sf.Description',
                'la.AccountName',
                'sf.Dr',
                'sf.Cr',
                'sf.Date',
                'sf.Stamp',
            ]);

        // ── Customer ranking ──────────────────────────────────────────────
        $volumeRanking = DB::table('container_main')
            ->where('Status', '!=', 9)
            ->whereNotNull('ConsigneeID')
            ->groupBy('ConsigneeID')
            ->orderByRaw('COUNT(*) DESC')
            ->get([
                'ConsigneeID',
                DB::raw('COUNT(*) as Total'),
            ]);

        $lclCounts = DB::table('manifestation_breakdown as mb')
            ->join('container_main as cm', 'mb.ConsignmentID', '=', 'cm.ConsignmentID')
            ->where('cm.Status', '!=', 9)
            ->whereNotNull('mb.ConsigneeID')
            ->groupBy('mb.ConsigneeID')
            ->get([
                'mb.ConsigneeID',
                DB::raw('COUNT(*) as Total'),
            ]);

        $mergedVolume   = collect();
        $allConsigneeIds = $volumeRanking->pluck('ConsigneeID')
            ->merge($lclCounts->pluck('ConsigneeID'))
            ->unique();

        foreach ($allConsigneeIds as $cid) {
            $fcl = $volumeRanking->firstWhere('ConsigneeID', $cid)?->Total ?? 0;
            $lcl = $lclCounts->firstWhere('ConsigneeID', $cid)?->Total ?? 0;
            $mergedVolume->push((object) [
                'ConsigneeID' => $cid,
                'Total'       => $fcl + $lcl,
            ]);
        }

        $mergedVolume     = $mergedVolume->sortByDesc('Total')->values();
        $totalClientsVolume = $mergedVolume->count();
        $volumeRankPos    = $mergedVolume->search(fn($r) => $r->ConsigneeID == $consigneeId);
        $volumeRankPos    = $volumeRankPos !== false ? $volumeRankPos + 1 : $totalClientsVolume;
        $volumeConsignments = $mergedVolume->firstWhere('ConsigneeID', $consigneeId)?->Total ?? 0;
        $volumePercentile = $totalClientsVolume > 0
            ? round(($volumeRankPos / $totalClientsVolume) * 100) : 0;

        $valueRanking = DB::table('disbursement_analysis as da')
            ->join('container_main as cm', 'da.BL', '=', 'cm.BL')
            ->where('cm.Status', '!=', 9)
            ->whereNotNull('da.ConsigneeID')
            ->where('da.InReport', 1)
            ->groupBy('da.ConsigneeID')
            ->orderByRaw('SUM(da.Revenue + da.Expenditure) DESC')
            ->get([
                'da.ConsigneeID',
                DB::raw('ROUND(SUM(da.Revenue + da.Expenditure), 2) as TotalValue'),
                DB::raw('COUNT(DISTINCT da.BL) as ConsignmentCount'),
            ]);

        $totalClientsValue = $valueRanking->count();
        $valueRankPos      = $valueRanking->search(fn($r) => $r->ConsigneeID == $consigneeId);
        $valueRankPos      = $valueRankPos !== false ? $valueRankPos + 1 : $totalClientsValue;
        $clientTotalValue  = $valueRanking->firstWhere('ConsigneeID', $consigneeId)?->TotalValue ?? 0;
        $valuePercentile   = $totalClientsValue > 0
            ? round(($valueRankPos / $totalClientsValue) * 100) : 0;

        $avgPercentile = round(($volumePercentile + $valuePercentile) / 2);
        $rankBadge     = $avgPercentile <= 10 ? ['label' => 'Premium Client',    'cls' => 'gold']
            : ($avgPercentile <= 25            ? ['label' => 'Regular Client',    'cls' => 'silver']
                : ($avgPercentile <= 50            ? ['label' => 'Occasional Client', 'cls' => 'bronze']
                    :                                   ['label' => 'Infrequent Client', 'cls' => 'standard']));

        $ranking = [
            'volume_rank'        => $volumeRankPos,
            'volume_total'       => $totalClientsVolume,
            'volume_percentile'  => $volumePercentile,
            'volume_count'       => $volumeConsignments,
            'value_rank'         => $valueRankPos,
            'value_total'        => $totalClientsValue,
            'value_percentile'   => $valuePercentile,
            'client_total_value' => $clientTotalValue,
            'avg_percentile'     => $avgPercentile,
            'badge'              => $rankBadge,
        ];

        // ── Disbursements ─────────────────────────────────────────────────
        $consignmentBLs = $consignments->pluck('MainBL')->unique()->values();
        $hblMap         = $hblEntries->pluck('HouseBL', 'HouseBL')->keys();

        $disbursements = DB::table('disbursement_analysis as da')
            ->leftJoin('ledger_account as la', 'da.AccountID', '=', 'la.AccountNo')
            ->where(function ($q) use ($consignmentBLs, $hblMap) {
                $q->whereIn('da.BL', $consignmentBLs)
                    ->orWhereIn('da.HBL', $hblMap);
            })
            ->when($dateFrom, fn($q) => $q->where('da.Date', '>=', $dateFrom))
            ->when($dateTo,   fn($q) => $q->where('da.Date', '<=', $dateTo))
            ->orderBy('da.Date', 'desc')
            ->get([
                'da.BL as MainBL',
                'da.HBL',
                'da.ReceiptNo',
                'da.Date',
                'da.Expenditure',
                'da.Revenue',
                'la.AccountName',
            ]);

        $disbursementTotals = [
            'expenditure' => round($disbursements->sum('Expenditure'), 2),
            'revenue'     => round($disbursements->sum('Revenue'), 2),
        ];

        // ── Chart data ────────────────────────────────────────────────────
        $chartFrom = $dateFrom ?? now()->subMonths(11)->startOfMonth()->toDateString();
        $chartTo   = $dateTo   ?? now()->toDateString();

        $disbChartData = DB::table('disbursement_analysis as da')
            ->join('container_main as cm', 'da.BL', '=', 'cm.BL')
            ->where('da.ConsigneeID', $consigneeId)
            ->where('cm.Status', '!=', 9)
            ->where('da.InReport', 1)
            ->whereBetween('da.Date', [$chartFrom, $chartTo])
            ->groupBy(DB::raw('YEAR(da.Date)'), DB::raw('MONTH(da.Date)'))
            ->orderBy(DB::raw('YEAR(da.Date)'))
            ->orderBy(DB::raw('MONTH(da.Date)'))
            ->get([
                DB::raw('DATE_FORMAT(da.Date, "%b %Y") as MonthLabel'),
                DB::raw('YEAR(da.Date) as Year'),
                DB::raw('MONTH(da.Date) as Month'),
                DB::raw('ROUND(SUM(da.Revenue), 2) as Revenue'),
                DB::raw('ROUND(SUM(da.Expenditure), 2) as Expenditure'),
            ]);

        $invoiceChartData = DB::table('student_fee')
            ->where('StudentID', $consigneeId)
            ->where('Status', 1)
            ->whereBetween('Date', [$chartFrom, $chartTo])
            ->groupBy(DB::raw('YEAR(Date)'), DB::raw('MONTH(Date)'))
            ->orderBy(DB::raw('YEAR(Date)'))
            ->orderBy(DB::raw('MONTH(Date)'))
            ->get([
                DB::raw('DATE_FORMAT(Date, "%b %Y") as MonthLabel'),
                DB::raw('YEAR(Date) as Year'),
                DB::raw('MONTH(Date) as Month'),
                DB::raw('ROUND(SUM(Dr), 2) as Invoiced'),
                DB::raw('ROUND(SUM(Cr), 2) as Paid'),
            ]);

        $chartData = $disbChartData->map(function ($row) use ($invoiceChartData) {
            $inv = $invoiceChartData->first(
                fn($r) => $r->Year == $row->Year && $r->Month == $row->Month
            );
            return (object) [
                'MonthLabel'  => $row->MonthLabel,
                'Revenue'     => $row->Revenue,
                'Expenditure' => $row->Expenditure,
                'Invoiced'    => $inv?->Invoiced ?? null,
                'Paid'        => $inv?->Paid ?? null,
                'hasInvoice'  => $inv !== null,
            ];
        });

        foreach ($invoiceChartData as $inv) {
            $exists = $chartData->first(fn($r) => $r->MonthLabel === $inv->MonthLabel);
            if (! $exists) {
                $chartData->push((object) [
                    'MonthLabel'  => $inv->MonthLabel,
                    'Revenue'     => 0,
                    'Expenditure' => 0,
                    'Invoiced'    => $inv->Invoiced,
                    'Paid'        => $inv->Paid,
                    'hasInvoice'  => true,
                ]);
            }
        }

        $hasInvoiceData = $invoiceChartData->isNotEmpty();

        // ── Return all data as native PHP types ───────────────────────────
        // Collections stay as Laravel Collections — view iterates with @foreach
        // Single-row objects stay as stdClass — view accesses with ->
        // Arrays stay as arrays — view accesses with []
        return [
            'consignee'           => $consignee,          // stdClass
            'memberSince'         => $memberSince,         // string|null
            'consignmentSummary'  => $consignmentSummary,  // array
            'consignments'        => $consignments,        // Collection of stdClass
            'hblEntries'          => $hblEntries,          // Collection of stdClass
            'mostUsedCarrier'     => $mostUsedCarrier,     // string
            'avgDaysToClear'      => $avgDaysToClear,      // float|null
            'invoiceSummary'      => $invoiceSummary,      // stdClass
            'invoices'            => $invoices,            // Collection of stdClass
            'disbursements'       => $disbursements,       // Collection of stdClass
            'disbursementTotals'  => $disbursementTotals,  // array
            'ranking'             => $ranking,             // array
            'chartData'           => $chartData,           // Collection of stdClass
            'hasInvoiceData'      => $hasInvoiceData,      // bool
        ];
    }
}
