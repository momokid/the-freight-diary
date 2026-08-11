<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * The consignment workflow, and the gate that stops tasks running out of order.
 *
 * The chain is code, not configuration. Which stages a task requires — and
 * whether a failure stops or warns — is declared per playbook in GatesJson.
 * An admin can make a gate advisory on their own task; they cannot remove a
 * requirement from a system playbook.
 *
 * register → ETA → arrive → manifest (LCL only) → disburse → gate out → return
 */
class WorkflowService
{
    public const STAGE_REGISTERED = 'registered';
    public const STAGE_ARRIVED    = 'arrived';
    public const STAGE_MANIFESTED = 'manifested';
    public const STAGE_DISBURSED  = 'disbursed';
    public const STAGE_GATED_OUT  = 'gated_out';
    public const STAGE_RETURNED   = 'returned';

    public const MODE_STOP = 'stop';
    public const MODE_WARN = 'warn';

    public const RESULT_PASS = 'pass';
    public const RESULT_WARN = 'warn';
    public const RESULT_STOP = 'stop';

    private const ORDER = [
        self::STAGE_REGISTERED,
        self::STAGE_ARRIVED,
        self::STAGE_MANIFESTED,
        self::STAGE_DISBURSED,
        self::STAGE_GATED_OUT,
        self::STAGE_RETURNED,
    ];

    public function __construct(
        private ConsignmentService $consignments
    ) {}

    // ── State ───────────────────────────────────────────────────────────────

    /** Everything the gates need, read once. */
    public function state(int $consignmentId, string $bl): array
    {
        $row = DB::table('container_main')
            ->where('ConsignmentID', $consignmentId)
            ->where('BL', $bl)
            ->first(['Status', 'ETA', 'CmdtTypeID']);

        if (! $row) {
            throw new InvalidArgumentException("Consignment not found: {$bl}");
        }

        $type = $this->consignments->resolveType($consignmentId, $bl);
        $isLcl = in_array($type, [
            ConsignmentService::TYPE_LCL,
            ConsignmentService::TYPE_LCL_PENDING,
        ], true);

        // Arrival is derived: an ETA in the past that nobody moved means it landed
        $eta          = $row->ETA ? Carbon::parse($row->ETA)->startOfDay() : null;
        $today        = Carbon::now()->startOfDay();
        $hasArrived   = $eta ? $eta->lessThanOrEqualTo($today) : false;
        $daysSinceEta = $eta ? (int) round($today->diffInDays($eta, false) * -1) : null;

        $breakdownCount = DB::table('manifestation_breakdown')
            ->where('ConsignmentID', $consignmentId)
            ->where('MainBL', $bl)
            ->where('Status', 1)
            ->count();

        $stamps = DB::table('disbursement_analysis')
            ->where('BL', $bl)
            ->where('Status', '<>', 9)
            ->distinct()
            ->pluck('Stamp')
            ->filter()
            ->values()
            ->all();

        $containers = DB::table('container_details')
            ->where('ConsignmentID', $consignmentId)
            ->where('BL', $bl)
            ->where('Status', '<>', 9)
            ->get(['GateOutDate', 'ReturnDate']);

        return [
            'ConsignmentID'   => $consignmentId,
            'BL'              => $bl,
            'Status'          => (int) $row->Status,
            'ETA'             => $row->ETA,
            'DaysSinceEta'    => $daysSinceEta,
            'HasArrived'      => $hasArrived,
            'IsLcl'           => $isLcl,
            'Type'            => $type,
            'BreakdownCount'  => $breakdownCount,
            'Stamps'          => $stamps,
            'ContainerCount'  => $containers->count(),
            'GatedOutCount'   => $containers->whereNotNull('GateOutDate')->count(),
            'ReturnedCount'   => $containers->whereNotNull('ReturnDate')->count(),
        ];
    }

    /** Furthest stage the consignment has actually reached. */
    public function currentStage(array $state): string
    {
        if ($state['ContainerCount'] > 0 && $state['ReturnedCount'] >= $state['ContainerCount']) {
            return self::STAGE_RETURNED;
        }

        if ($state['ContainerCount'] > 0 && $state['GatedOutCount'] > 0) {
            return self::STAGE_GATED_OUT;
        }

        if (! empty($state['Stamps'])) {
            return self::STAGE_DISBURSED;
        }

        if ($state['IsLcl'] && $state['BreakdownCount'] > 0) {
            return self::STAGE_MANIFESTED;
        }

        if ($state['HasArrived']) {
            return self::STAGE_ARRIVED;
        }

        return self::STAGE_REGISTERED;
    }

    // ── Gates ───────────────────────────────────────────────────────────────

    /**
     * Evaluate a playbook's declared gates.
     *
     * Returns ['result' => pass|warn|stop, 'failures' => [...]]. A stop is
     * final: the run does not start. A warn pauses for the user to decide.
     */
    public function check(array $gates, array $state): array
    {
        $failures = [];
        $result   = self::RESULT_PASS;

        foreach ($gates as $gate) {
            $stage = $gate['stage'] ?? null;
            $mode  = $gate['mode'] ?? self::MODE_STOP;

            if (! in_array($stage, self::ORDER, true)) {
                throw new InvalidArgumentException("Unknown workflow stage: {$stage}");
            }

            if (($gate['onlyIf'] ?? null) === 'lcl' && ! $state['IsLcl']) {
                continue;   // manifest gates do not apply to FCL
            }

            if ($this->satisfied($stage, $state)) {
                continue;
            }

            $failures[] = [
                'stage'   => $stage,
                'mode'    => $mode,
                'message' => $this->message($stage, $state),
                'fix'     => $this->fix($stage),
            ];

            if ($mode === self::MODE_STOP) {
                $result = self::RESULT_STOP;
            } elseif ($result !== self::RESULT_STOP) {
                $result = self::RESULT_WARN;
            }
        }

        return ['result' => $result, 'failures' => $failures];
    }

    private function satisfied(string $stage, array $state): bool
    {
        return match ($stage) {
            self::STAGE_REGISTERED => true,
            self::STAGE_ARRIVED    => $state['HasArrived'],
            self::STAGE_MANIFESTED => ! $state['IsLcl'] || $state['BreakdownCount'] > 0,
            self::STAGE_DISBURSED  => ! empty($state['Stamps']),
            self::STAGE_GATED_OUT  => $state['ContainerCount'] > 0
                && $state['GatedOutCount'] >= $state['ContainerCount'],
            self::STAGE_RETURNED   => $state['ContainerCount'] > 0
                && $state['ReturnedCount'] >= $state['ContainerCount'],
            default => false,
        };
    }

    /** What is wrong, in the user's terms. */
    private function message(string $stage, array $state): string
    {
        return match ($stage) {
            self::STAGE_ARRIVED => $state['ETA']
                ? 'Not arrived yet — ETA is ' . Carbon::parse($state['ETA'])->format('j M Y') .
                ' (' . abs($state['DaysSinceEta']) . ' ' .
                (abs($state['DaysSinceEta']) === 1 ? 'day' : 'days') . ' away).'
                : 'Not arrived — no ETA recorded.',

            self::STAGE_MANIFESTED => 'This is an LCL consignment with no manifest breakdown yet.',

            self::STAGE_DISBURSED  => 'No disbursement has been raised for this consignment.',

            self::STAGE_GATED_OUT  => ($state['ContainerCount'] - $state['GatedOutCount']) .
                ' of ' . $state['ContainerCount'] . ' containers have not been gated out.',

            self::STAGE_RETURNED   => ($state['ContainerCount'] - $state['ReturnedCount']) .
                ' of ' . $state['ContainerCount'] . ' containers have not been returned.',

            default => 'Workflow stage not satisfied.',
        };
    }

    /** Where to go to put it right — no override, only a route forward. */
    private function fix(string $stage): array
    {
        return match ($stage) {
            self::STAGE_ARRIVED => [
                'label' => 'Check or update the ETA',
                'route' => 'edit-data.consignment.index',
            ],
            self::STAGE_MANIFESTED => [
                'label' => 'Do the manifest breakdown',
                'route' => 'manifest.index',
            ],
            self::STAGE_DISBURSED => [
                'label' => 'Raise the disbursement',
                'route' => 'disbursement.analysis.index',
            ],
            default => ['label' => null, 'route' => null],
        };
    }

    public function stageLabel(string $stage): string
    {
        return match ($stage) {
            self::STAGE_REGISTERED => 'Registered',
            self::STAGE_ARRIVED    => 'Arrived',
            self::STAGE_MANIFESTED => 'Manifested',
            self::STAGE_DISBURSED  => 'Disbursed',
            self::STAGE_GATED_OUT  => 'Gated out',
            self::STAGE_RETURNED   => 'Returned',
            default => 'Unknown',
        };
    }

    /**
     * What is owed next, in the user's terms.
     *
     * Expects the same state array as currentStage(), plus DaysSinceEta and
     * IsConfirmed. Kept here rather than in the reply so there is one ladder,
     * not one per caller.
     */
    public function nextAction(array $state): string
    {
        $days = $state['DaysSinceEta'] ?? null;

        if (empty($state['HasArrived'])) {
            return $days === null
                ? 'No ETA recorded — set one so arrival can be tracked.'
                : 'Awaiting arrival — ETA in ' . abs($days) . ' ' . $this->plural(abs($days), 'day') . '.';
        }

        // An FCL owes no breakdown, an LCL does. Until the type is settled we
        // cannot say which, so asking is the only honest next step.
        if (empty($state['IsConfirmed'])) {
            return 'Cargo type not confirmed — set it so the system knows whether a breakdown is due.';
        }

        $breakdown = (int) ($state['BreakdownCount'] ?? 0);

        if (! empty($state['IsLcl']) && $breakdown === 0) {
            return 'Manifest breakdown not yet done.';
        }

        $stamps = $state['Stamps'] ?? [];

        if (empty($stamps)) {
            return ($breakdown > 0 ? "{$breakdown} house " . $this->plural($breakdown, 'BL') . ' manifested — ' : '') .
                'no disbursement raised yet.';
        }

        $containers = (int) ($state['ContainerCount'] ?? 0);
        $gatedOut   = (int) ($state['GatedOutCount'] ?? 0);
        $returned   = (int) ($state['ReturnedCount'] ?? 0);

        if ($containers > 0 && $gatedOut < $containers) {
            return 'Disbursement raised (' . implode(', ', $stamps) . ') — ' .
                ($containers - $gatedOut) . ' of ' . $containers . ' ' .
                $this->plural($containers, 'container') . ' still to gate out.';
        }

        if ($containers > 0 && $returned < $containers) {
            return ($containers - $returned) . ' of ' . $containers . ' ' .
                $this->plural($containers, 'container') . ' gated out and not yet returned.';
        }

        return 'All containers gated out and returned — nothing outstanding.';
    }

    /**
     * Whether the consignment has sat too long at its current stage.
     * Thresholds are cumulative from the ETA, so a late stage inherits the
     * time allowed for the ones before it.
     */
    public function isDelayed(array $state): bool
    {
        if (empty($state['HasArrived'])) {
            return false;
        }

        $days = $state['DaysSinceEta'] ?? 0;
        $t    = config('agent.thresholds');

        if (! empty($state['IsLcl']) && (int) ($state['BreakdownCount'] ?? 0) === 0) {
            return $days > $t['arrival_to_manifest'];
        }

        if (empty($state['Stamps'])) {
            return $days > ($t['arrival_to_manifest'] + $t['manifest_to_disbursement']);
        }

        $containers = (int) ($state['ContainerCount'] ?? 0);

        if ($containers > 0 && (int) ($state['GatedOutCount'] ?? 0) < $containers) {
            return $days > ($t['arrival_to_manifest'] + $t['manifest_to_disbursement'] + $t['disbursement_to_gateout']);
        }

        if ($containers > 0 && (int) ($state['ReturnedCount'] ?? 0) < $containers) {
            return $days > array_sum($t);
        }

        return false;
    }

    private function plural(int $n, string $word): string
    {
        return $n === 1 ? $word : $word . 's';
    }
}
