<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StallService
{
    public const STAGE_TYPE         = 'type';
    public const STAGE_MANIFEST     = 'manifest';
    public const STAGE_DISBURSEMENT = 'disbursement';
    public const STAGE_GATEOUT      = 'gateout';
    public const STAGE_RETURN       = 'return';

    private const DEFAULTS = [
        'stall_monitor_enabled'      => 1,
        'stall_days_to_type'         => 0,
        'stall_days_to_manifest'     => 1,
        'stall_days_to_disbursement' => 4,
        'stall_days_to_gateout'      => 1,
        'stall_days_to_return'       => 3,
        'stall_claim_quiet_days'     => 1,
        'stall_lookback_days'        => 90,
    ];

    private ?array $settings = null;

    // ── Settings ────────────────────────────────────────────────────────────

    /** Read once per request. A missing row falls back rather than crashing. */
    public function settings(): array
    {
        if ($this->settings !== null) {
            return $this->settings;
        }

        $rows = DB::table('system_settings')
            ->where('group', 'stall_monitor')
            ->pluck('value', 'key')
            ->all();

        $out = [];
        foreach (self::DEFAULTS as $key => $fallback) {
            $value = $rows[$key] ?? null;
            $out[$key] = is_numeric($value) ? (int) $value : $fallback;
        }

        return $this->settings = $out;
    }

    public function enabled(): bool
    {
        return $this->settings()['stall_monitor_enabled'] === 1;
    }

    // ── The list ────────────────────────────────────────────────────────────

    /**
     * Every stalled consignment for the branch, grouped by stage.
     *
     * One query. Container dates and disbursement dates are rolled up in
     * subqueries so a consignment produces exactly one row.
     */
    public function stalled(int $branchId): array
    {
        $empty = [
            self::STAGE_TYPE         => [],
            self::STAGE_RETURN       => [],
            self::STAGE_GATEOUT      => [],
            self::STAGE_DISBURSEMENT => [],
            self::STAGE_MANIFEST     => [],
        ];

        if (! $this->enabled()) {
            return $empty;
        }

        $s       = $this->settings();
        $today   = Carbon::now()->startOfDay();
        $cutoff  = $today->copy()->subDays($s['stall_lookback_days'])->toDateString();

        // Container dates. 0000-00-00 is legacy for "never happened".
        $containers = DB::table('container_details')
            ->selectRaw('
                ConsignmentID,
                BL,
                COUNT(*) AS ContainerCount,
                SUM(CASE WHEN GateOutDate IS NOT NULL AND GateOutDate <> "0000-00-00" THEN 1 ELSE 0 END) AS GatedOutCount,
                SUM(CASE WHEN ReturnDate  IS NOT NULL AND ReturnDate  <> "0000-00-00" THEN 1 ELSE 0 END) AS ReturnedCount,
                MAX(CASE WHEN GateOutDate <> "0000-00-00" THEN GateOutDate END) AS LastGateOut
            ')
            ->where('Status', '<>', 9)
            ->groupBy('ConsignmentID', 'BL');

        // Any disbursement counts, approved or not. Only deleted rows are ignored.
        $disbursements = DB::table('disbursement_analysis')
            ->selectRaw('
                BL,
                COUNT(*) AS DisbCount,
                MAX(CASE WHEN Date <> "0000-00-00" THEN Date END) AS LastDisb
            ')
            ->where('Status', '<>', 9)
            ->groupBy('BL');

        // Breakdown rows are the only proof a manifest has been done.
        $breakdown = DB::table('manifestation_breakdown')
            ->selectRaw('ConsignmentID, MainBL, COUNT(*) AS BreakdownCount')
            ->where('Status', 1)
            ->groupBy('ConsignmentID', 'MainBL');

        $rows = DB::table('container_main as cm')
            ->leftJoin('consignee_main as co', 'cm.ConsigneeID', '=', 'co.ConsigneeID')
            ->leftJoinSub($containers, 'cd', function ($j) {
                $j->on('cd.ConsignmentID', '=', 'cm.ConsignmentID')
                    ->on('cd.BL', '=', 'cm.BL');
            })
            ->leftJoinSub($disbursements, 'da', 'da.BL', '=', 'cm.BL')
            ->leftJoinSub($breakdown, 'mb', function ($j) {
                $j->on('mb.ConsignmentID', '=', 'cm.ConsignmentID')
                    ->on('mb.MainBL', '=', 'cm.BL');
            })
            ->where('cm.BranchID', $branchId)
            ->where('cm.Status', '<>', 9)
            ->whereNotNull('cm.ETA')
            ->where('cm.ETA', '<>', '0000-00-00')
            ->where('cm.ETA', '>=', $cutoff)
            ->where('cm.ETA', '<=', $today->toDateString())
            ->select([
                'cm.ConsignmentID',
                'cm.BL',
                'cm.ETA',
                'cm.VesselName',
                'cm.IsLCL',
                'co.FullName as ConsigneeName',
                DB::raw('COALESCE(cd.ContainerCount, 0) AS ContainerCount'),
                DB::raw('COALESCE(cd.GatedOutCount, 0)  AS GatedOutCount'),
                DB::raw('COALESCE(cd.ReturnedCount, 0)  AS ReturnedCount'),
                'cd.LastGateOut',
                DB::raw('COALESCE(da.DisbCount, 0) AS DisbCount'),
                'da.LastDisb',
                'cm.IsLCL',
                DB::raw('COALESCE(mb.BreakdownCount, 0) AS BreakdownCount'),
            ])
            ->get();

        $claims = $this->claims();
        $out    = $empty;

        foreach ($rows as $r) {
            $flag = $this->classify($r, $today, $s);

            if ($flag === null) {
                continue;
            }

            [$stage, $since] = $flag;

            $out[$stage][] = $this->decorate($r, $stage, $since, $today, $claims, $s);
        }

        // Longest wait first, within each group
        foreach ($out as $stage => $items) {
            usort($items, fn($a, $b) => $b['Days'] <=> $a['Days']);
            $out[$stage] = $items;
        }

        return $out;
    }

    /**
     * First match wins, so a consignment never appears twice.
     * Returns [stage, date the clock started] or null.
     */
    private function classify(object $r, Carbon $today, array $s): ?array
    {
        // Nothing downstream can be assessed until someone says what this is.
        // Only worth asking while it still blocks work — once money has moved,
        // whoever disbursed it plainly knew.
        if ($r->IsLCL === null && $r->DisbCount == 0) {
            $since = Carbon::parse($r->ETA)->startOfDay();
            if ($since->diffInDays($today, false) >= $s['stall_days_to_type']) {
                return [self::STAGE_TYPE, $since];
            }
            return null;
        }

        // 1. Everything gated out, something still not returned
        if (
            $r->ContainerCount > 0
            && $r->GatedOutCount >= $r->ContainerCount
            && $r->ReturnedCount < $r->ContainerCount
            && $r->LastGateOut
        ) {
            $since = Carbon::parse($r->LastGateOut)->startOfDay();
            if ($since->diffInDays($today, false) >= $s['stall_days_to_return']) {
                return [self::STAGE_RETURN, $since];
            }
            return null;
        }

        // 2. Disbursed, nothing gated out yet
        if ($r->DisbCount > 0 && $r->GatedOutCount === 0 && $r->LastDisb) {
            $since = Carbon::parse($r->LastDisb)->startOfDay();
            if ($since->diffInDays($today, false) >= $s['stall_days_to_gateout']) {
                return [self::STAGE_GATEOUT, $since];
            }
            return null;
        }

        // 3. LCL arrived with nothing broken down — the gate before disbursement
        if ($r->IsLCL !== null && (int) $r->IsLCL === 1 && $r->BreakdownCount == 0) {
            $since = Carbon::parse($r->ETA)->startOfDay();
            if ($since->diffInDays($today, false) >= $s['stall_days_to_manifest']) {
                return [self::STAGE_MANIFEST, $since];
            }
            return null;
        }

        // 4. Arrived, no disbursement at all
        if ($r->DisbCount === 0) {
            $since = Carbon::parse($r->ETA)->startOfDay();
            if ($since->diffInDays($today, false) > $s['stall_days_to_disbursement']) {
                return [self::STAGE_DISBURSEMENT, $since];
            }
        }

        return null;
    }

    /** Attach the claim, the age and the phrase the runner will receive. */
    private function decorate(object $r, string $stage, Carbon $since, Carbon $today, array $claims, array $s): array
    {
        $key   = $r->ConsignmentID . '|' . $r->BL . '|' . $stage;
        $claim = $claims[$key] ?? null;
        $quiet = false;

        if ($claim) {
            $quiet = Carbon::parse($claim->ClaimedAt)->startOfDay()
                ->diffInDays($today, false) >= $s['stall_claim_quiet_days'];
        }

        return [
            'ConsignmentID' => (int) $r->ConsignmentID,
            'BL'            => $r->BL,
            'Stage'         => $stage,
            'ConsigneeName' => $r->ConsigneeName ?? '—',
            'VesselName'    => $r->VesselName,
            'Since'         => $since->toDateString(),
            'Days'          => (int) $since->diffInDays($today, false),
            'ClaimedBy'     => $claim->Username ?? null,
            'ClaimedAt'     => $claim->ClaimedAt ?? null,
            'CanRelease'    => $claim && (
                $claim->Username === Auth::id()
                || Auth::user()?->Nature === 'Admin-0'
            ),
            'GoneQuiet'     => $quiet,
            'Type'          => $r->IsLCL === null ? null : ((int) $r->IsLCL === 1 ? 'LCL' : 'FCL'),
        ];
    }

    // ── Claims ──────────────────────────────────────────────────────────────

    /** Keyed by consignment|bl|stage for a cheap lookup while building the list. */
    private function claims(): array
    {
        return DB::table('stall_claims')
            ->get(['ConsignmentID', 'BL', 'Stage', 'Username', 'ClaimedAt'])
            ->keyBy(fn($c) => $c->ConsignmentID . '|' . $c->BL . '|' . $c->Stage)
            ->all();
    }

    /** Insert or move the claim to whoever pressed it last. */
    public function claim(int $consignmentId, string $bl, string $stage, string $username): void
    {
        DB::table('stall_claims')->updateOrInsert(
            ['ConsignmentID' => $consignmentId, 'BL' => $bl, 'Stage' => $stage],
            ['Username' => $username, 'ClaimedAt' => Carbon::now()]
        );
    }

    /** Deleted, not flagged — the item goes straight back to unclaimed. */
    public function release(int $consignmentId, string $bl, string $stage): void
    {
        DB::table('stall_claims')
            ->where('ConsignmentID', $consignmentId)
            ->where('BL', $bl)
            ->where('Stage', $stage)
            ->delete();
    }

    // ── Bell ────────────────────────────────────────────────────────────────

    /** Two counts: nobody on it, and claimed but gone quiet. */
    public function counts(int $branchId): array
    {
        $unclaimed = 0;
        $quiet     = 0;

        foreach ($this->stalled($branchId) as $items) {
            foreach ($items as $i) {
                if ($i['ClaimedBy'] === null) {
                    $unclaimed++;
                } elseif ($i['GoneQuiet']) {
                    $quiet++;
                }
            }
        }

        return ['unclaimed' => $unclaimed, 'quiet' => $quiet, 'total' => $unclaimed + $quiet];
    }
}
