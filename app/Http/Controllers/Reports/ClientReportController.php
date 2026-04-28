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
    // Only consignees with at least one consignment
    // ════════════════════════════════════════════════════════════════════════

    public function search(Request $request)
    {
        $request->validate([
            'q' => ['required', 'string', 'min:2', 'max:100'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
        ]);

        $q = $request->q;
        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;

        $query = DB::table('consignee_main as co')
            ->join('container_main as cm', 'co.ConsigneeID', '=', 'cm.ConsigneeID')
            ->where('cm.Status', '!=', 9)
            ->where('co.FullName', 'like', '%'.$q.'%')
            ->groupBy('co.ConsigneeID', 'co.FullName', 'co.TelNo');

        if ($dateFrom) {
            $query->where('cm.Date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->where('cm.Date', '<=', $dateTo);
        }

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
    // Returns all data for the 4 cards, 3 tabs and chart
    // ════════════════════════════════════════════════════════════════════════

    public function profile(Request $request, int $consigneeId)
    {
        $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
        ]);

        $dateFrom = $request->date_from;
        $dateTo = $request->date_to;

        // ── Card 1: Consignee details ────────────────────────────────────
        $consignee = DB::table('consignee_main')
            ->where('ConsigneeID', $consigneeId)
            ->first();

        if (! $consignee) {
            return response()->json([
                'success' => false,
                'message' => 'Consignee not found.',
            ], 404);
        }

        // Member since — earliest consignment date
        $memberSince = DB::table('container_main')
            ->where('ConsigneeID', $consigneeId)
            ->where('Status', '!=', 9)
            ->min('Date');

        // ── Card 2: Consignment summary ──────────────────────────────────
        $consignmentQuery = DB::table('container_main as cm')
            ->leftJoin('ship_carrier as sc', 'cm.CarrierID', '=', 'sc.CarrierID')
            ->leftJoin('commodity_type as ct', 'cm.CmdtTypeID', '=', 'ct.TypeID')
            ->leftJoin('container_details as cd', function ($join) {
                $join->on('cm.ConsignmentID', '=', 'cd.ConsignmentID')
                    ->on('cm.BL', '=', 'cd.BL');
            })
            ->where('cm.ConsigneeID', $consigneeId)
            ->where('cm.Status', '!=', 9);

        // Also include LCL consignments where consignee appears as HBL consignee
        $hblConsignmentIds = DB::table('manifestation_breakdown')
            ->where('ConsigneeID', $consigneeId)
            ->where('Status', '!=', 9)
            ->pluck('ConsignmentID')
            ->unique();

        if ($dateFrom) {
            $consignmentQuery->where('cm.Date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $consignmentQuery->where('cm.Date', '<=', $dateTo);
        }

        $consignments = $consignmentQuery
            ->groupBy(
                'cm.ConsignmentID', 'cm.BL', 'cm.ETA', 'cm.Date',
                'cm.Status', 'cm.CmdtTypeID', 'sc.CarrierName', 'ct.TypeName'
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

        // HBL entries for LCL consignments
        $hblEntries = DB::table('manifestation_breakdown as mb')
            ->leftJoin('container_main as cm', function ($join) {
                $join->on('mb.ConsignmentID', '=', 'cm.ConsignmentID');
            })
            ->leftJoin('ship_carrier as sc', 'cm.CarrierID', '=', 'sc.CarrierID')
            ->where('mb.ConsigneeID', $consigneeId)
            ->where('mb.Status', '!=', 9)
            ->when($dateFrom, fn ($q) => $q->where('cm.Date', '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->where('cm.Date', '<=', $dateTo))
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

        $consignmentSummary = [
            'total' => $consignments->count(),
            'hbl_total' => $hblEntries->count(),
            'not_arrived' => $consignments->where('Status', 1)->count(),
            'pending' => $consignments->where('Status', 2)->count(),
            'gated_out' => $consignments->where('Status', 3)->count(),
            'cleared' => $consignments->where('Status', 0)->count(),
        ];

        // Most used carrier
        $mostUsedCarrier = $consignments
            ->groupBy('CarrierName')
            ->map(fn ($g) => $g->count())
            ->sortDesc()
            ->keys()
            ->first() ?? '—';

        // Avg days to clear
        $clearedConsignments = $consignments->where('Status', 0);
        $avgDaysToClear = $clearedConsignments->count() > 0
            ? round($clearedConsignments->avg('AgeDays'), 1)
            : null;

        // ── Card 3: Invoice & revenue summary ────────────────────────────
        $invoiceSummary = DB::table('student_fee')
            ->where('StudentID', $consigneeId)
            ->where('Status', 1)
            ->when($dateFrom, fn ($q) => $q->where('Date', '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->where('Date', '<=', $dateTo))
            ->selectRaw('
                ROUND(SUM(Dr), 2) as TotalInvoiced,
                ROUND(SUM(Cr), 2) as TotalPaid,
                ROUND(SUM(Dr) - SUM(Cr), 2) as Outstanding
            ')
            ->first();

        // Invoice line items
        $invoices = DB::table('student_fee as sf')
            ->leftJoin('ledger_account as la', 'sf.AccountNo', '=', 'la.AccountNo')
            ->where('sf.StudentID', $consigneeId)
            ->where('sf.Status', 1)
            ->when($dateFrom, fn ($q) => $q->where('sf.Date', '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->where('sf.Date', '<=', $dateTo))
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

        // ── Card 4: Customer ranking ─────────────────────────────────────
        $rankingData = DB::table('container_main')
            ->where('Status', '!=', 9)
            ->whereNotNull('ConsigneeID')
            ->groupBy('ConsigneeID')
            ->orderByRaw('COUNT(*) DESC')
            ->get([
                'ConsigneeID',
                DB::raw('COUNT(*) as Total'),
            ]);

        $totalClients = $rankingData->count();
        $consigneeRank = $rankingData->search(fn ($r) => $r->ConsigneeID == $consigneeId);
        $consigneeRank = $consigneeRank !== false ? $consigneeRank + 1 : $totalClients;
        $rankPercentile = $totalClients > 0 ? round(($consigneeRank / $totalClients) * 100) : 0;

        $rankBadge = $rankPercentile <= 10 ? ['label' => 'Premium Client',    'icon' => '🥇', 'cls' => 'gold']
                   : ($rankPercentile <= 25 ? ['label' => 'Regular Client',    'icon' => '🥈', 'cls' => 'silver']
                   : ($rankPercentile <= 50 ? ['label' => 'Occasional Client', 'icon' => '🥉', 'cls' => 'bronze']
                   : ['label' => 'Infrequent Client', 'icon' => '⭐', 'cls' => 'standard']));

        $ranking = [
            'rank' => $consigneeRank,
            'total' => $totalClients,
            'percentile' => $rankPercentile,
            'badge' => $rankBadge,
        ];

        // ── Disbursement tab ─────────────────────────────────────────────
        // FCL — match by MainBL
        // LCL — match by MainBL + HBL where HBL belongs to this consignee
        $consignmentBLs = $consignments->pluck('MainBL')->unique()->values();
        $hblMap = $hblEntries->pluck('HouseBL', 'HouseBL')->keys();

        $disbursements = DB::table('disbursement_analysis as da')
            ->leftJoin('ledger_account as la', 'da.AccountID', '=', 'la.AccountNo')
            ->where(function ($q) use ($consignmentBLs, $hblMap) {
                // FCL consignments — all disbursements for their BLs
                $q->whereIn('da.BL', $consignmentBLs)
                  // LCL — only HBL entries belonging to this consignee
                    ->orWhereIn('da.HBL', $hblMap);
            })
            ->when($dateFrom, fn ($q) => $q->where('da.Date', '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->where('da.Date', '<=', $dateTo))
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
            'revenue' => round($disbursements->sum('Revenue'), 2),
        ];

        // ── Chart data — monthly revenue vs payments ─────────────────────
        // Last 12 months or within date range
        $chartFrom = $dateFrom ?? now()->subMonths(11)->startOfMonth()->toDateString();
        $chartTo = $dateTo ?? now()->toDateString();

        $chartData = DB::table('student_fee')
            ->where('StudentID', $consigneeId)
            ->where('Status', 1)
            ->whereBetween('Date', [$chartFrom, $chartTo])
            ->groupBy(
                DB::raw('YEAR(Date)'),
                DB::raw('MONTH(Date)')
            )
            ->orderBy(DB::raw('YEAR(Date)'))
            ->orderBy(DB::raw('MONTH(Date)'))
            ->get([
                DB::raw('DATE_FORMAT(Date, "%b %Y") as MonthLabel'),
                DB::raw('ROUND(SUM(Dr), 2) as Invoiced'),
                DB::raw('ROUND(SUM(Cr), 2) as Paid'),
            ]);

        return response()->json([
            'success' => true,
            'consignee' => $consignee,
            'memberSince' => $memberSince,
            'consignmentSummary' => $consignmentSummary,
            'consignments' => $consignments,
            'hblEntries' => $hblEntries,
            'mostUsedCarrier' => $mostUsedCarrier,
            'avgDaysToClear' => $avgDaysToClear,
            'invoiceSummary' => $invoiceSummary,
            'invoices' => $invoices,
            'disbursements' => $disbursements,
            'disbursementTotals' => $disbursementTotals,
            'ranking' => $ranking,
            'chartData' => $chartData,
        ]);
    }

    // ════════════════════════════════════════════════════════════════════════
    // PROFILE PRINT
    // ════════════════════════════════════════════════════════════════════════

    public function profilePrint(Request $request, int $consigneeId)
    {
        $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
        ]);

        // Re-use the same profile data
        $profileData = $this->profile($request, $consigneeId);
        $data = json_decode($profileData->getContent(), true);

        if (! $data['success']) {
            abort(404, 'Consignee not found.');
        }

        $dateFrom = $request->date_from
            ? \Carbon\Carbon::parse($request->date_from)->format('d M Y')
            : 'All Time';
        $dateTo = $request->date_to
            ? \Carbon\Carbon::parse($request->date_to)->format('d M Y')
            : now()->format('d M Y');

        return view('reports.client.client-profile-print', array_merge($data, [
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]));
    }
}
