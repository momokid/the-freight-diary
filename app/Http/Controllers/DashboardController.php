<?php

namespace App\Http\Controllers;

use App\Services\ConsignmentService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\UserAuth;

class DashboardController extends Controller
{
    private int $trackerPageSize = 8;
    private ConsignmentService $consignmentService;

    public function __construct(ConsignmentService $consignmentService)
    {
        $this->consignmentService = $consignmentService;
    }

    public function index()
    {
        $user = Auth::user();
        return view('dashboard', compact('user'));
    }

    public function refresh(Request $request)
    {
        $widget   = $request->input('widget');
        $user     = Auth::user();
        $userAuth = UserAuth::where('Username', $user->ID)->first();
        $branch   = $user->Nature === 'Admin-0' ? null : $user->BranchID;

        [$view, $data] = match ($widget) {
            'financial' => $userAuth->hasPermission('AccountingReport')
                ? ['dashboard._financial', array_merge(
                    $this->buildFinancial($branch),
                    $userAuth->hasPermission('ManagementReport')
                        ? $this->buildCashPosition($branch)
                        : ['accounts' => []]
                )]
                : [null, null],

            'collections' => $userAuth->hasPermission('ManagementReport')
                ? ['dashboard._collections', $this->buildCollections()]
                : [null, null],

            'disbursements' => $userAuth->hasPermission('DisbursementApproval')
                ? ['dashboard._disbursements', $this->buildDisbursements($branch)]
                : [null, null],

            'transactions' => $userAuth->hasPermission('PaymentTransaction')
                ? ['dashboard._transactions', $this->buildTransactions($branch)]
                : [null, null],

            'vision' => $userAuth->hasPermission('ManagementReport')
                ? ['dashboard._vision', $this->buildVision($branch)]
                : [null, null],

            default => [null, null],
        };

        if ($view === null) {
            return response('Unauthorised', 403);
        }

        return response(view($view, $data)->render());
    }

    public function chartData(Request $request)
    {
        $user     = Auth::user();
        $userAuth = UserAuth::where('Username', $user->ID)->first();

        if (!$userAuth || !$userAuth->hasPermission('ConsignmentRegister')) {
            return response()->json(['error' => 'Unauthorised'], 403);
        }

        $branch = $user->Nature === 'Admin-0' ? null : $user->BranchID;
        $bf     = $this->branchFilter($branch, 'cd');

        $rows = DB::select("
            SELECT
                DATE_FORMAT(cd.Date, '%b %Y') AS label,
                DATE_FORMAT(cd.Date, '%Y-%m') AS month_key,
                COUNT(*)                       AS total
            FROM container_details cd
            WHERE cd.Date >= DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 11 MONTH), '%Y-%m-01')
            {$bf['sql']}
            GROUP BY DATE_FORMAT(cd.Date, '%Y-%m'), DATE_FORMAT(cd.Date, '%b %Y')
            ORDER BY month_key ASC
        ", $bf['bindings']);

        $indexed = [];
        foreach ($rows as $row) {
            $indexed[$row->month_key] = ['label' => $row->label, 'total' => (int) $row->total];
        }

        $labels = [];
        $values = [];

        for ($i = 11; $i >= 0; $i--) {
            $key      = Carbon::now()->subMonths($i)->format('Y-m');
            $label    = Carbon::now()->subMonths($i)->format('M Y');
            $labels[] = $label;
            $values[] = isset($indexed[$key]) ? $indexed[$key]['total'] : 0;
        }

        return response()->json(['labels' => $labels, 'values' => $values]);
    }

    public function trackerData(Request $request)
    {
        $user     = Auth::user();
        $userAuth = UserAuth::where('Username', $user->ID)->first();

        if (!$userAuth || !$userAuth->hasPermission('ConsignmentRegister')) {
            return response()->json(['error' => 'Unauthorised'], 403);
        }

        $branch   = $user->Nature === 'Admin-0' ? null : $user->BranchID;
        $canEdit  = $userAuth->hasPermission('EditData');
        $search   = trim($request->input('search', ''));
        $priority = $this->consignmentService->prioritySql();

        $selectFields = "
            cm.ConsignmentID,
            cm.BL,
            cm.ETA,
            cm.Status,
            cm.Destination,
            cm.Date                               AS RegisteredDate,
            COALESCE(co.FullName, '—')            AS ConsigneeName,
            TO_DAYS(cm.ETA) - TO_DAYS(CURDATE())  AS ETADays,
            EXISTS (
                SELECT 1 FROM disbursement_analysis da
                WHERE da.BL = cm.BL AND da.Stamp = 'IN-HARBOR'
            ) AS HasDisbursement,
            (
                SELECT COUNT(*) FROM container_details cd
                WHERE cd.ConsignmentID = cm.ConsignmentID
            ) AS TotalContainers,
            (
                SELECT COUNT(*) FROM container_details cd
                WHERE cd.ConsignmentID = cm.ConsignmentID
                  AND cd.ReturnDate IS NOT NULL
            ) AS ReturnedContainers,
            ({$priority}) AS Priority";

        $baseFrom = "
            FROM container_main cm
            LEFT JOIN consignee_main co ON co.ConsigneeID = cm.ConsigneeID
            WHERE cm.Status IN (1, 2, 3)
              AND cm.Ownership = 1";

        if ($search !== '') {
            $term = '%' . $search . '%';

            $rows = DB::select("
                SELECT {$selectFields}
                {$baseFrom}
                  AND (cm.BL LIKE ? OR co.FullName LIKE ? OR cm.Destination LIKE ?)
                ORDER BY Priority ASC, cm.ETA ASC
                LIMIT 10
            ", [$term, $term, $term]);

            $html = view('dashboard._tracker', compact('rows', 'canEdit'))->render();

            return response()->json([
                'html'        => $html,
                'currentPage' => 1,
                'totalPages'  => 1,
                'total'       => count($rows),
                'isSearch'    => true,
                'count'       => count($rows),
            ]);
        }

        $page    = max(1, (int) $request->input('page', 1));
        $perPage = max(1, (int) $request->input('perPage', 10));
        $offset  = ($page - 1) * $perPage;
        $bf      = $this->branchFilter($branch, 'cm');

        $total = DB::selectOne("
            SELECT COUNT(*) as total
            FROM container_main cm
            LEFT JOIN consignee_main co ON co.ConsigneeID = cm.ConsigneeID
            WHERE cm.Status IN (1, 2, 3)
            AND cm.Ownership = 1
            {$bf['sql']}
        ", $bf['bindings'])->total;

        $rows = DB::select("
            SELECT {$selectFields}
            {$baseFrom}
            {$bf['sql']}
            ORDER BY Priority ASC, cm.ETA ASC
            LIMIT ? OFFSET ?
        ", array_merge($bf['bindings'], [$perPage, $offset]));

        $totalPages = max(1, (int) ceil($total / $perPage));

        $html = view('dashboard._tracker', compact('rows', 'canEdit'))->render();

        return response()->json([
            'html'        => $html,
            'currentPage' => $page,
            'totalPages'  => $totalPages,
            'total'       => (int) $total,
            'isSearch'    => false,
            'count'       => count($rows),
        ]);
    }

    public function trackerContainers(Request $request)
    {
        $user     = Auth::user();
        $userAuth = UserAuth::where('Username', $user->ID)->first();

        if (!$userAuth || !$userAuth->hasPermission('ConsignmentRegister')) {
            return response()->json(['error' => 'Unauthorised'], 403);
        }

        $request->validate([
            'consignmentId' => 'required|integer',
            'bl'            => 'required|string|max:100',
        ]);

        $consignmentId = (int) $request->input('consignmentId');
        $bl            = $request->input('bl');

        $containers = DB::table('container_details')
            ->where('ConsignmentID', $consignmentId)
            ->select('ContainerNo', 'ContainerSize', 'Status', 'GateOutDate', 'ReturnDate')
            ->orderBy('ContainerNo')
            ->get();

        $html = view('dashboard._tracker_containers', compact('containers', 'consignmentId', 'bl'))->render();

        return response()->json(['html' => $html]);
    }

    public function gateOut(Request $request, int $consignmentId, string $bl)
    {
        $user     = Auth::user();
        $userAuth = UserAuth::where('Username', $user->ID)->first();

        if (!$userAuth || !$userAuth->hasPermission('ConsignmentRegister')) {
            return response()->json(['error' => 'Unauthorised'], 403);
        }

        $today = Carbon::today()->toDateString();

        DB::table('container_details')
            ->where('ConsignmentID', $consignmentId)
            ->update(['Status' => 3, 'GateOutDate' => $today]);

        DB::table('container_main')
            ->where('ConsignmentID', $consignmentId)
            ->where('BL', $bl)
            ->update(['Status' => 3]);

        return response()->json(['success' => true]);
    }

    public function containerClear(Request $request, int $consignmentId, string $containerNo)
    {
        $user     = Auth::user();
        $userAuth = UserAuth::where('Username', $user->ID)->first();

        if (!$userAuth || !$userAuth->hasPermission('ConsignmentRegister')) {
            return response()->json(['error' => 'Unauthorised'], 403);
        }

        $today = Carbon::today()->toDateString();

        DB::table('container_details')
            ->where('ConsignmentID', $consignmentId)
            ->where('ContainerNo', $containerNo)
            ->update(['Status' => 4, 'ReturnDate' => $today]);

        $pending = DB::table('container_details')
            ->where('ConsignmentID', $consignmentId)
            ->where('Status', '!=', 4)
            ->count();

        if ($pending === 0) {
            DB::table('container_main')
                ->where('ConsignmentID', $consignmentId)
                ->update(['Status' => 0]);
        }

        return response()->json(['success' => true, 'allCleared' => $pending === 0]);
    }

    public function drawerDisbs(Request $request)
    {
        $user     = Auth::user();
        $userAuth = UserAuth::where('Username', $user->ID)->first();

        if (!$userAuth || !$userAuth->hasPermission('DisbursementApproval')) {
            return response()->json(['error' => 'Unauthorised'], 403);
        }

        $branch   = $user->Nature === 'Admin-0' ? null : $user->BranchID;
        $bf       = $this->branchFilter($branch, 'cm');
        $priority = $this->consignmentService->prioritySql();

        $rows = DB::select("
            SELECT
                cm.ConsignmentID,
                cm.BL,
                cm.ETA,
                cm.Destination,
                COALESCE(co.FullName, '—')           AS ConsigneeName,
                DATEDIFF(CURDATE(), cm.ETA)           AS DaysOverdue,
                ({$priority})                         AS Priority
            FROM container_main cm
            LEFT JOIN consignee_main co ON co.ConsigneeID = cm.ConsigneeID
            WHERE cm.Ownership = 1
              AND cm.Status IN (1, 2)
              AND cm.ETA < CURDATE()
              AND NOT EXISTS (
                  SELECT 1 FROM disbursement_analysis da
                  WHERE da.BL = cm.BL AND da.Stamp = 'IN-HARBOR'
              )
            {$bf['sql']}
            ORDER BY cm.ETA ASC
        ", $bf['bindings']);

        return response()->json($rows);
    }

    public function drawerPendingConsignments(Request $request)
    {
        $user     = Auth::user();
        $userAuth = UserAuth::where('Username', $user->ID)->first();

        if (!$userAuth || !$userAuth->hasPermission('ConsignmentRegister')) {
            return response()->json(['error' => 'Unauthorised'], 403);
        }

        $branch   = $user->Nature === 'Admin-0' ? null : $user->BranchID;
        $bf       = $this->branchFilter($branch, 'cm');
        $canEdit  = $userAuth->hasPermission('EditData');
        $priority = $this->consignmentService->prioritySql();

        $rows = DB::select("
            SELECT
                cm.ConsignmentID,
                cm.BL,
                cm.ETA,
                cm.Destination,
                COALESCE(co.FullName, '—')            AS ConsigneeName,
                TO_DAYS(cm.ETA) - TO_DAYS(CURDATE())  AS ETADays,
                EXISTS (
                    SELECT 1 FROM disbursement_analysis da
                    WHERE da.BL = cm.BL AND da.Stamp = 'IN-HARBOR'
                )                                     AS DisbursementApproved,
                ({$priority})                         AS Priority
            FROM container_main cm
            LEFT JOIN consignee_main co ON co.ConsigneeID = cm.ConsigneeID
            WHERE cm.Status IN (1, 2, 3)
              AND cm.Ownership = 1
            {$bf['sql']}
            ORDER BY Priority ASC, cm.ETA ASC
        ", $bf['bindings']);

        return response()->json(['rows' => $rows, 'canEdit' => $canEdit]);
    }

    public function updateEta(Request $request)
    {
        $user = Auth::user();
        $userAuth = UserAuth::where('Username', $user->ID)->first();

        if (!$userAuth || !$userAuth->hasPermission('EditData')) {
            return response()->json(['error' => 'Unauthorised'], 403);
        }

        $request->validate([
            'consignmentId' => 'required|integer',
            'bl'            => 'required|string',
            'eta'           => 'required|date',
        ]);

        // Capture current ETA + consignee details before the update
        $current = DB::table('container_main as cm')
            ->leftJoin('consignee_main as co', 'co.ConsigneeID', '=', 'cm.ConsigneeID')
            ->where('cm.ConsignmentID', $request->consignmentId)
            ->where('cm.BL', $request->bl)
            ->select('cm.ETA as OldETA', 'co.TelNo', 'co.FullName')
            ->first();

        $etaChanged = $current && substr($current->OldETA, 0, 10) !== $request->eta;

        DB::table('container_main')
            ->where('ConsignmentID', $request->consignmentId)
            ->where('BL', $request->bl)
            ->update(['ETA' => $request->eta]);

        $result = DB::selectOne(
            "SELECT TO_DAYS(?) - TO_DAYS(CURDATE()) AS etaDays",
            [$request->eta]
        );

        return response()->json([
            'success'     => true,
            'eta'         => $request->eta,
            'etaDays'     => (int) $result->etaDays,
            'eta_changed' => $etaChanged,
            'phone'       => $etaChanged ? ($current->TelNo ?? '') : null,
            'consignee'   => $etaChanged ? ($current->FullName ?? '') : null,
        ]);
    }

    private function branchFilter(?int $branch, string $alias = ''): array
    {
        if ($branch === null) return ['sql' => '', 'bindings' => []];
        $col = $alias ? "{$alias}.BranchID" : 'BranchID';
        return ['sql' => "AND {$col} = ?", 'bindings' => [$branch]];
    }

    private function buildFinancial(?int $branch): array
    {
        $monthStart = Carbon::now()->startOfMonth()->toDateString();
        $today      = Carbon::today()->toDateString();
        $ie         = DB::table('active_ie')->first();

        if (!$ie) {
            return ['revenue' => 0, 'expenditure' => 0, 'net' => 0, 'trend' => [], 'monthLabel' => ''];
        }

        $revenue = DB::table('journal')
            ->join('ledger_account', 'journal.SubAccountID', '=', 'ledger_account.AccountNo')
            ->where('journal.AccountID', $ie->AccountID)
            ->where('journal.Reversed', 0)
            ->when($branch !== null, fn($q) => $q->where('journal.BranchID', $branch))
            ->whereBetween('journal.Date', [$monthStart, $today])
            ->where('ledger_account.Type', 'INCOME')
            ->selectRaw('COALESCE(SUM(journal.Cr), 0) - COALESCE(SUM(journal.Dr), 0) AS total')
            ->value('total') ?? 0;

        $expenditure = DB::table('journal')
            ->join('ledger_account', 'journal.SubAccountID', '=', 'ledger_account.AccountNo')
            ->where('journal.AccountID', $ie->AccountID)
            ->where('journal.Reversed', 0)
            ->when($branch !== null, fn($q) => $q->where('journal.BranchID', $branch))
            ->whereBetween('journal.Date', [$monthStart, $today])
            ->where('ledger_account.Type', 'EXPENDITURE')
            ->selectRaw('COALESCE(SUM(journal.Dr), 0) - COALESCE(SUM(journal.Cr), 0) AS total')
            ->value('total') ?? 0;

        $bf    = $this->branchFilter($branch, 'j');
        $trend = DB::select("
            SELECT
                DATE_FORMAT(j.Date, '%b %Y') AS month_label,
                DATE_FORMAT(j.Date, '%Y-%m') AS month_key,
                COALESCE(SUM(CASE WHEN la.Type = 'INCOME'
                    THEN j.Cr - j.Dr ELSE 0 END), 0) AS revenue,
                COALESCE(SUM(CASE WHEN la.Type = 'EXPENDITURE'
                    THEN j.Dr - j.Cr ELSE 0 END), 0) AS expenditure
            FROM journal j
            JOIN ledger_account la ON la.AccountNo = j.SubAccountID
            JOIN active_ie ai      ON ai.AccountID = j.AccountID
            WHERE j.Date >= DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 2 MONTH), '%Y-%m-01')
              AND la.Type IN ('INCOME', 'EXPENDITURE')
              AND j.Reversed = 0
            {$bf['sql']}
            GROUP BY DATE_FORMAT(j.Date, '%Y-%m'), DATE_FORMAT(j.Date, '%b %Y')
            ORDER BY month_key ASC
        ", $bf['bindings']);

        return [
            'revenue'     => $revenue,
            'expenditure' => $expenditure,
            'net'         => $revenue - $expenditure,
            'trend'       => $trend,
            'monthLabel'  => Carbon::now()->format('M Y'),
        ];
    }

    private function buildCashPosition(?int $branch): array
    {
        $bf = $this->branchFilter($branch, 'j');

        $accounts = DB::select("
            SELECT
                la.AccountName,
                COALESCE(SUM(j.Dr), 0) - COALESCE(SUM(j.Cr), 0) AS balance
            FROM active_bank_cash abc
            JOIN ledger_account la ON la.AccountNo  = abc.AccountID
            LEFT JOIN journal j
                ON  j.AccountID    = abc.AccountID
                AND j.SubAccountID = abc.AccountID
                {$bf['sql']}
            GROUP BY abc.AccountID, la.AccountName
            ORDER BY la.AccountName
        ", $bf['bindings']);

        return ['accounts' => $accounts];
    }

    private function buildCollections(): array
    {
        $today = Carbon::today()->toDateString();

        $summary = DB::selectOne("
            SELECT
                COALESCE(SUM(Outstanding), 0)                    AS total,
                COUNT(DISTINCT StudentID)                         AS clientCount,
                COALESCE(SUM(CASE WHEN DaysOutstanding <= 30
                    THEN Outstanding END), 0)                    AS bucket_30,
                COALESCE(SUM(CASE WHEN DaysOutstanding BETWEEN 31 AND 60
                    THEN Outstanding END), 0)                    AS bucket_60,
                COALESCE(SUM(CASE WHEN DaysOutstanding BETWEEN 61 AND 90
                    THEN Outstanding END), 0)                    AS bucket_90,
                COALESCE(SUM(CASE WHEN DaysOutstanding > 90
                    THEN Outstanding END), 0)                    AS bucket_90plus
            FROM (
                SELECT
                    sf.StudentID,
                    COALESCE(SUM(sf.Dr), 0) - COALESCE(SUM(sf.Cr), 0) AS Outstanding,
                    DATEDIFF(?, MIN(CASE WHEN sf.Dr > 0 THEN sf.Date END)) AS DaysOutstanding
                FROM student_fee sf
                WHERE sf.Date <= ?
                GROUP BY sf.StudentID, sf.SubClassID, sf.CouponID, sf.Stamp
                HAVING COALESCE(SUM(sf.Dr), 0) - COALESCE(SUM(sf.Cr), 0) > 0
            ) AS aged
        ", [$today, $today]);

        return ['summary' => $summary];
    }

    private function buildTransactions(?int $branch): array
    {
        $bf = $this->branchFilter($branch, 'j');

        $rows = DB::select("
            SELECT
                j.Date,
                j.ReceiptNo,
                la.AccountName,
                j.Dr,
                j.Cr,
                j.Description
            FROM journal j
            JOIN ledger_account la ON la.AccountNo = j.SubAccountID
            WHERE j.Reversed = 0
            {$bf['sql']}
            ORDER BY j.Date DESC, j.Time DESC
            LIMIT 10
        ", $bf['bindings']);

        return ['rows' => $rows];
    }

    private function buildDisbursements(?int $branch): array
    {
        $bf = $this->branchFilter($branch, 'cm');

        $pendingResult = DB::selectOne("
            SELECT
                SUM(CASE WHEN cm.Status = 1 THEN 1 ELSE 0 END) AS arrived,
                SUM(CASE WHEN cm.Status = 2 THEN 1 ELSE 0 END) AS inHarbor
            FROM container_main cm
            WHERE cm.Ownership = 1
              AND cm.Status IN (1, 2)
              AND DATEDIFF(CURDATE(), cm.ETA) > 0
              AND COALESCE((
                  SELECT SUM(da.Expenditure)
                  FROM disbursement_analysis da
                  WHERE da.BL = cm.BL
              ), 0) = 0
            {$bf['sql']}
        ", $bf['bindings']);

        $gatedOutResult = DB::selectOne("
            SELECT COUNT(*) AS gatedOut
            FROM container_main cm
            WHERE cm.Ownership = 1
              AND cm.Status = 3
              AND COALESCE((
                  SELECT SUM(da.Expenditure)
                  FROM disbursement_analysis da
                  WHERE da.BL = cm.BL
              ), 0) > 0
            {$bf['sql']}
        ", $bf['bindings']);

        $summary = (object) [
            'total'    => (int) $pendingResult->arrived + (int) $pendingResult->inHarbor,
            'arrived'  => (int) $pendingResult->arrived,
            'inHarbor' => (int) $pendingResult->inHarbor,
            'gatedOut' => (int) $gatedOutResult->gatedOut,
        ];

        return ['summary' => $summary, 'overdue' => $summary->total];
    }

    private function buildVision(?int $branch): array
    {
        $target = DB::table('vision_targets')->where('IsActive', 1)->first();
        $ie     = DB::table('active_ie')->first();

        if (!$target || !$ie) {
            return [
                'target'      => (object) ['TargetAmount' => 0],
                'cumulative'  => 0,
                'progressPct' => 0,
                'rag'         => 'default',
            ];
        }

        $cumIncome = DB::table('journal')
            ->join('ledger_account', 'journal.SubAccountID', '=', 'ledger_account.AccountNo')
            ->where('journal.AccountID', $ie->AccountID)
            ->whereYear('journal.Date', '>=', $target->StartYear)
            ->where('journal.Reversed', 0)
            ->when($branch !== null, fn($q) => $q->where('journal.BranchID', $branch))
            ->where('ledger_account.Type', 'INCOME')
            ->sum('journal.Cr');

        $cumExpend = DB::table('journal')
            ->join('ledger_account', 'journal.SubAccountID', '=', 'ledger_account.AccountNo')
            ->where('journal.AccountID', $ie->AccountID)
            ->whereYear('journal.Date', '>=', $target->StartYear)
            ->where('journal.Reversed', 0)
            ->when($branch !== null, fn($q) => $q->where('journal.BranchID', $branch))
            ->where('ledger_account.Type', 'EXPENDITURE')
            ->sum('journal.Dr');

        $cumulative  = round($cumIncome - $cumExpend, 2);
        $progressPct = $target->TargetAmount > 0
            ? round(($cumulative / $target->TargetAmount) * 100, 2)
            : 0;

        return [
            'target'      => $target,
            'cumulative'  => $cumulative,
            'progressPct' => $progressPct,
            'rag'         => $progressPct >= 100 ? 'green'
                : ($progressPct >= 75 ? 'amber' : 'red'),
        ];
    }
}
