<?php

namespace App\Http\Controllers;

use App\Services\CompanyService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\UserAuth;

class DashboardController extends Controller
{
    private int $perPage = 10;

    // ─────────────────────────────────────────────────────────────────────────
    // Main page load — widget data loads via AJAX, not here
    // ─────────────────────────────────────────────────────────────────────────
    public function index()
    {
        $user     = Auth::user();
        return view('dashboard', compact('user'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // AJAX refresh — returns rendered Blade partial for one widget
    // JS injects the HTML directly into the widget container
    // ─────────────────────────────────────────────────────────────────────────
    public function refresh(Request $request)
    {
        $widget   = $request->input('widget');
        $user     = Auth::user();
        $userAuth = UserAuth::query()->where('Username', $user->ID)->first();
        // Admin-0 sees all branches — null means no branch filter
        $branch = $user->Nature === 'Admin-0' ? null : $user->BranchID;

        [$view, $data] = match ($widget) {

            'tracker' => $userAuth->hasPermission('ConsignmentRegister')
                ? ['dashboard._tracker', $this->buildTracker(
                    $branch,
                    max(1, (int) $request->input('left_page',  1)),
                    max(1, (int) $request->input('right_page', 1))
                )]
                : [null, null],

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

    // ─────────────────────────────────────────────────────────────────────────
    // Gate-out — marks all containers under a consignment as gated out
    // ─────────────────────────────────────────────────────────────────────────
    public function gateOut(Request $request, int $consignmentId, string $bl)
    {
        $today = Carbon::today()->toDateString();

        DB::table('container_details')
            ->where('ConsignmentID', $consignmentId)
            ->update([
                'Status'      => 3,
                'GateOutDate' => $today,
            ]);

        DB::table('container_main')
            ->where('ConsignmentID', $consignmentId)
            ->where('BL', $bl)
            ->update(['Status' => 3]);

        return response()->json(['success' => true]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Container clear — marks one container as returned
    // If all containers cleared → marks consignment as cleared too
    // ─────────────────────────────────────────────────────────────────────────
    public function containerClear(Request $request, int $consignmentId, string $containerNo)
    {
        $today = Carbon::today()->toDateString();

        DB::table('container_details')
            ->where('ConsignmentID', $consignmentId)
            ->where('ContainerNo', $containerNo)
            ->update([
                'Status'     => 4,
                'ReturnDate' => $today,
            ]);

        // Check if all containers under this consignment are now returned
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

    // ─────────────────────────────────────────────────────────────────────────
    // Branch filter helper
    // Returns SQL fragment + bindings for raw queries
    // Returns empty when branch is null (Admin-0 — sees all branches)
    // ─────────────────────────────────────────────────────────────────────────
    private function branchFilter(?int $branch, string $alias = ''): array
    {
        if ($branch === null) return ['sql' => '', 'bindings' => []];
        $col = $alias ? "{$alias}.BranchID" : 'BranchID';
        return ['sql' => "AND {$col} = ?", 'bindings' => [$branch]];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Widget builders
    // ─────────────────────────────────────────────────────────────────────────

    private function buildTracker(?int $branch, int $leftPage, int $rightPage): array
    {
        $bfLeft  = $this->branchFilter($branch, 'ip');
        $bfRight = $this->branchFilter($branch, 'cm');
        $offset  = fn(int $page) => ($page - 1) * $this->perPage;

        // ── Left panel — uses inharbor_pending_1 view ─────────────────────────
        // View already filters: Status = 1, Ownership = 1, ConsigneeName joined,
        // ETADays pre-calculated as TO_DAYS(ETA) - TO_DAYS(CURDATE())

        $leftTotal = DB::selectOne("
        SELECT COUNT(*) AS total
        FROM inharbor_pending_1 ip
        WHERE 1=1
        {$bfLeft['sql']}
    ", $bfLeft['bindings'])->total;

        $leftRows = DB::select("
        SELECT
            ip.ConsignmentID,
            ip.BL,
            ip.ETA,
            ip.ETADays,
            ip.Status,
            ip.Destination,
            ip.ConsigneeName,
            EXISTS (
                SELECT 1 FROM disbursement_analysis da
                WHERE da.BL = ip.BL
                  AND da.Stamp = 'IN-HARBOR'
            )                           AS DisbursementApproved
        FROM inharbor_pending_1 ip
        WHERE 1=1
        {$bfLeft['sql']}
        ORDER BY ip.ETADays ASC
        LIMIT ? OFFSET ?
    ", array_merge($bfLeft['bindings'], [$this->perPage, $offset($leftPage)]));

        // ── Right panel — gated-out containers awaiting return ────────────────

        $rightTotal = DB::selectOne("
        SELECT COUNT(*) AS total
        FROM container_details cd
        JOIN container_main cm ON cm.ConsignmentID = cd.ConsignmentID
        WHERE cd.Status = 3
        AND cm.Ownership = 1
        {$bfRight['sql']}
    ", $bfRight['bindings'])->total;

        $rightRows = DB::select("
        SELECT
            cd.ContainerNo,
            cd.ContainerSize,
            cd.GateOutDate,
            cm.BL,
            cm.ConsignmentID,
            cm.Destination
        FROM container_details cd
        JOIN container_main cm ON cm.ConsignmentID = cd.ConsignmentID
        WHERE cd.Status = 3
        {$bfRight['sql']}
        ORDER BY cd.GateOutDate ASC
        LIMIT ? OFFSET ?
    ", array_merge($bfRight['bindings'], [$this->perPage, $offset($rightPage)]));

        return [
            'left'  => [
                'rows'     => $leftRows,
                'total'    => (int) $leftTotal,
                'page'     => $leftPage,
                'lastPage' => max(1, (int) ceil($leftTotal / $this->perPage)),
            ],
            'right' => [
                'rows'     => $rightRows,
                'total'    => (int) $rightTotal,
                'page'     => $rightPage,
                'lastPage' => max(1, (int) ceil($rightTotal / $this->perPage)),
            ],
        ];
    }

    private function buildFinancial(?int $branch): array
    {
        $monthStart = Carbon::now()->startOfMonth()->toDateString();
        $today      = Carbon::today()->toDateString();

        $ie = DB::table('active_ie')->first();
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

        // Last 6 months trend for bar chart
        $bf    = $this->branchFilter($branch, 'j');
        $trend = DB::select("
            SELECT
                DATE_FORMAT(j.Date, '%b %Y')                                          AS month_label,
                DATE_FORMAT(j.Date, '%Y-%m')                                          AS month_key,
                COALESCE(SUM(CASE WHEN la.Type = 'INCOME'
                    THEN j.Cr - j.Dr ELSE 0 END), 0)                                 AS revenue,
                COALESCE(SUM(CASE WHEN la.Type = 'EXPENDITURE'
                    THEN j.Dr - j.Cr ELSE 0 END), 0)                                 AS expenditure
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
        // student_fee has no BranchID — all users see all outstanding collections
        $today = Carbon::today()->toDateString();

        $summary = DB::selectOne("
            SELECT
                COALESCE(SUM(Outstanding), 0)                                               AS total,
                COUNT(DISTINCT StudentID)                                                    AS clientCount,
                COALESCE(SUM(CASE WHEN DaysOutstanding <= 30
                    THEN Outstanding END), 0)                                               AS bucket_30,
                COALESCE(SUM(CASE WHEN DaysOutstanding BETWEEN 31 AND 60
                    THEN Outstanding END), 0)                                               AS bucket_60,
                COALESCE(SUM(CASE WHEN DaysOutstanding BETWEEN 61 AND 90
                    THEN Outstanding END), 0)                                               AS bucket_90,
                COALESCE(SUM(CASE WHEN DaysOutstanding > 90
                    THEN Outstanding END), 0)                                               AS bucket_90plus
            FROM (
                SELECT
                    sf.StudentID,
                    COALESCE(SUM(sf.Dr), 0) - COALESCE(SUM(sf.Cr), 0)                     AS Outstanding,
                    DATEDIFF(?, MIN(CASE WHEN sf.Dr > 0 THEN sf.Date END))                 AS DaysOutstanding
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
                : ($progressPct >= 75  ? 'amber' : 'red'),
        ];
    }

    private function buildDisbursements(?int $branch): array
    {
        $bf = $this->branchFilter($branch, 'cm');

        // Arrived (Status=1) + In Harbor (Status=2) — ETA overdue, no expenditure
        $pendingResult = DB::selectOne("
        SELECT
            SUM(CASE WHEN cm.Status = 1 THEN 1 ELSE 0 END)   AS arrived,
            SUM(CASE WHEN cm.Status = 2 THEN 1 ELSE 0 END)   AS inHarbor
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

        // Gated Out (Status=3) — has expenditure recorded (compliance monitoring)
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

    public function drawerDisbs(Request $request)
    {
        $user     = Auth::user();
        $userAuth = UserAuth::where('Username', '=', $user->ID)->first();

        if (!$userAuth || !$userAuth->hasPermission('DisbursementApproval')) {
            return response()->json(['error' => 'Unauthorised'], 403);
        }

        $branch = $user->Nature === 'Admin-0' ? null : $user->BranchID;
        $bf     = $this->branchFilter($branch, 'cm');

        $rows = DB::select("
        SELECT
            cm.ConsignmentID,
            cm.BL,
            cm.Status,
            cm.ETA,
            cm.Destination,
            COALESCE(co.FullName, '—') AS ConsigneeName,
            DATEDIFF(CURDATE(), cm.ETA)         AS DaysOverdue
        FROM container_main cm
        LEFT JOIN consignee_main co ON co.ConsigneeID = cm.ConsigneeID
        WHERE cm.Ownership = 1
          AND cm.Status IN (1, 2)
          AND DATEDIFF(CURDATE(), cm.ETA) > 0
          AND COALESCE((
              SELECT SUM(da.Expenditure)
              FROM disbursement_analysis da
              WHERE da.BL = cm.BL
          ), 0) = 0
        {$bf['sql']}
        ORDER BY cm.Status ASC, cm.ETA ASC
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

        $branch  = $user->Nature === 'Admin-0' ? null : $user->BranchID;
        $bf      = $this->branchFilter($branch, 'ip');
        $canEdit = $userAuth->hasPermission('EditData');

        $rows = DB::select("
    SELECT
        ip.ConsignmentID,
        ip.BL,
        ip.ConsigneeName,
        ip.Destination,
        ip.ETA,
        ip.ETADays,
        EXISTS (
            SELECT 1 FROM disbursement_analysis da
            WHERE da.BL = ip.BL
              AND da.Stamp = 'IN-HARBOR'
        ) AS DisbursementApproved
        FROM inharbor_pending_1 ip
        WHERE 1=1
        {$bf['sql']}
        ORDER BY ip.ETADays ASC
        ", $bf['bindings']);

        return response()->json(['rows' => $rows, 'canEdit' => $canEdit]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // ETA update — EditData permission required
    // ─────────────────────────────────────────────────────────────────────────
    public function updateEta(Request $request)
    {
        $user     = Auth::user();
        $userAuth = UserAuth::where('Username', $user->ID)->first();

        if (!$userAuth || !$userAuth->hasPermission('EditData')) {
            return response()->json(['error' => 'Unauthorised'], 403);
        }

        $request->validate([
            'consignmentId' => 'required|integer',
            'bl'            => 'required|string',
            'eta'           => 'required|date',
        ]);

        DB::table('container_main')
            ->where('ConsignmentID', $request->consignmentId)
            ->where('BL', $request->bl)
            ->update(['ETA' => $request->eta]);

        // Recalculate ETADays using same formula as the view
        $result = DB::selectOne(
            "SELECT TO_DAYS(?) - TO_DAYS(CURDATE()) AS etaDays",
            [$request->eta]
        );

        return response()->json([
            'success' => true,
            'eta'     => $request->eta,
            'etaDays' => (int) $result->etaDays,
        ]);
    }
}
