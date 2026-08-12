<?php

namespace App\Agent\Steps;

use App\Agent\AgentContext;
use App\Services\DisbursementVisibility;
use App\Services\StallService;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Read tasks with no reference — the standing questions about the branch
 * rather than about one consignment.
 *
 * Abstract because permission belongs to the step, not to a playbook row an
 * admin could edit. Each subclass owns one report permission and the filters
 * that permission covers, so a disbursement playbook cannot pin an operations
 * filter.
 *
 * The filter is pinned as a literal in the playbook. Nothing the user types
 * reaches the query.
 */
abstract class ListConsignmentsStep implements AgentStep
{
    protected const CAP = 20;

    /** Filters this step is allowed to run. */
    abstract public static function filters(): array;

    public function __construct(
        private StallService $stalls,
        private DisbursementVisibility $visibility,
    ) {}

    public static function isWrite(): bool
    {
        return false;
    }

    public static function inputs(): array
    {
        return [
            'Filter' => [
                'type'        => 'string',
                'required'    => true,
                'description' => 'Which list to produce. Pinned by the playbook, never supplied by the user.',
            ],
        ];
    }

    public static function outputs(): array
    {
        return ['Rows', 'RowCount', 'Truncated', 'Filter', 'MoreUrl'];
    }

    public function run(array $input, AgentContext $context): array
    {
        $filter = $input['Filter'] ?? '';

        if (! in_array($filter, static::filters(), true)) {
            throw new InvalidArgumentException(
                "Filter '{$filter}' is not available to " . static::label() . '.'
            );
        }

        $rows = $filter === 'overdue'
            ? $this->overdue($context)
            : $this->fromQuery($filter, $context);

        return [
            'Rows'      => array_slice($rows, 0, static::CAP),
            'RowCount'  => count($rows),
            'Truncated' => count($rows) > static::CAP,
            'Filter'    => $filter,
            'MoreUrl'   => $this->moreUrl($filter),
        ];
    }

    /** Where the full list lives, when somewhere exists. */
    private function moreUrl(string $filter): ?string
    {
        $route = match ($filter) {
            'overdue' => 'stalled.index',
            default   => null,
        };

        if ($route === null) {
            return null;
        }

        try {
            return route($route);
        } catch (\Throwable) {
            return null;   // a renamed route must not break the reply
        }
    }
    // ── Overdue ─────────────────────────────────────────────────────────────

    /**
     * Delegated to StallService so the agent and the stall monitor can never
     * disagree about what is overdue.
     */
    private function overdue(AgentContext $context): array
    {
        $out = [];

        foreach ($this->stalls->stalled((int) $context->branchId) as $items) {
            foreach ($items as $item) {
                $out[] = [
                    'BL'            => $item['BL'],
                    'ConsigneeName' => $item['ConsigneeName'] ?: null,
                    'Days'          => $item['Days'],
                    'NextAction'    => $this->stageAction($item['Stage']),
                    'ClaimedBy'     => $item['ClaimedBy'],
                ];
            }
        }

        usort($out, fn($a, $b) => $b['Days'] <=> $a['Days']);

        return $out;
    }

    private function stageAction(string $stage): string
    {
        return match ($stage) {
            StallService::STAGE_DISBURSEMENT => 'Awaiting disbursement',
            StallService::STAGE_GATEOUT      => 'Awaiting gate out',
            StallService::STAGE_RETURN       => 'Awaiting container return',
            default                          => 'Unknown',
        };
    }

    // ── The other three ─────────────────────────────────────────────────────

    private function fromQuery(string $filter, AgentContext $context): array
    {
        $q = DB::table('container_main as cm')
            ->leftJoin('consignee_main as co', 'cm.ConsigneeID', '=', 'co.ConsigneeID')
            ->where('cm.Status', '<>', 9)
            ->where('cm.BranchID', $context->branchId)
            ->select([
                'cm.BL',
                'co.FullName as ConsigneeName',
                DB::raw('DATEDIFF(CURDATE(), cm.ETA) as Days'),
            ])
            ->orderByDesc(DB::raw('DATEDIFF(CURDATE(), cm.ETA)'));

        $q = match ($filter) {
            'not_disbursed'    => $this->notDisbursed($q, $context),
            'not_invoiced'     => $this->notInvoiced($q, $context),
            'unconfirmed_type' => $this->unconfirmedType($q),
        };

        return $q->get()->map(fn($r) => [
            'BL'            => $r->BL,
            'ConsigneeName' => $r->ConsigneeName ?: null,
            'Days'          => $r->Days === null ? null : (int) $r->Days,
            'NextAction'    => $this->action($filter),
            'ClaimedBy'     => null,
        ])->all();
    }

    private function action(string $filter): string
    {
        return match ($filter) {
            'not_disbursed'    => 'No disbursement raised',
            'not_invoiced'     => 'Not yet invoiced',
            'unconfirmed_type' => 'Type not confirmed',
        };
    }

    private function notDisbursed($q, AgentContext $context)
    {
        return $this->arrived($q)
            ->whereNotNull('cm.IsLCL')
            ->whereNotExists(fn($e) => $this->disbursementExists($e, $context));
    }

    private function notInvoiced($q, AgentContext $context)
    {
        return $q->whereExists(fn($e) => $this->disbursementExists($e, $context))
            ->whereNotExists(function ($e) {
                $e->select(DB::raw(1))
                    ->from('student_fee as sf')
                    ->whereColumn('sf.CouponID', 'cm.BL')
                    ->where('sf.Stamp', 'BL')
                    ->where('sf.Status', 1);
            });
    }

    private function unconfirmedType($q)
    {
        return $this->arrived($q)
            ->whereNull('cm.IsLCL')
            ->whereNotExists(function ($e) {
                $e->select(DB::raw(1))
                    ->from('manifestation_breakdown as mb')
                    ->whereColumn('mb.MainBL', 'cm.BL');
            });
    }

    /** Legacy rows carry 0000-00-00 rather than null. */
    private function arrived($q)
    {
        return $q->whereNotNull('cm.ETA')
            ->where('cm.ETA', '<>', '0000-00-00')
            ->whereRaw('cm.ETA <= CURDATE()');
    }

    /**
     * Restricted rows are filtered inside the subquery on purpose. A user who
     * cannot see a restricted disbursement should see a consistent world, not
     * a gap that reveals one exists.
     */
    private function disbursementExists($e, AgentContext $context): void
    {
        $e->select(DB::raw(1))
            ->from('disbursement_analysis as da')
            ->whereColumn('da.BL', 'cm.BL')
            ->where('da.Status', '<>', 9)
            ->whereIn('da.Restricted', $this->visibility->allowedFor($context->userAuth));
    }
}
