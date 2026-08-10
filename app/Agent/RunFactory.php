<?php

namespace App\Agent;

use App\Models\AgentAction;
use App\Models\AgentPlaybook;
use App\Models\AgentRun;
use App\Models\UserAuth;
use Carbon\Carbon;
use App\Services\WorkflowService;

use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Builds a run and its action rows from a playbook.
 *
 * Permissions are checked here, at plan time, so an unauthorised step is
 * visible before the user approves anything rather than failing halfway.
 * The runner checks again at execution in case rights changed since.
 */
class RunFactory
{
    public function __construct(
        private StepRegistry $registry,
        private WorkflowService $workflow,
    ) {}

    /**
     * @param  array  $seed     values the planner extracted, e.g. ['Reference' => 'MEDUY9898550']
     * @param  array  $meta     RawInstruction, InputModality, IntentKey, ResolutionLayer, LLMProvider, LLMModel
     */
    public function create(
        AgentPlaybook $playbook,
        UserAuth $userAuth,
        string $branchId,
        array $seed = [],
        array $meta = []
    ): AgentRun {

        $steps = $playbook->StepsJson ?? [];

        if (empty($steps)) {
            throw new RuntimeException("Playbook '{$playbook->PlaybookKey}' has no steps.");
        }

        $this->assertPermitted($steps, $userAuth);
        $this->assertWorkflowAllows($playbook, $seed);

        return DB::transaction(function () use ($playbook, $userAuth, $branchId, $seed, $meta, $steps) {

            $run = AgentRun::create([
                'PlaybookID'            => $playbook->ID,
                'TaskType'              => $playbook->TaskType,
                'TaskLabel'             => $playbook->Title,
                'IntentKey'             => $meta['IntentKey'] ?? $playbook->PlaybookKey,
                'RawInstruction'        => $meta['RawInstruction'] ?? '',
                'NormalisedInstruction' => $meta['NormalisedInstruction'] ?? null,
                'InputModality'         => $meta['InputModality'] ?? AgentRun::INPUT_TEXT,
                'ResolutionLayer'       => $meta['ResolutionLayer'] ?? null,
                'LLMProvider'           => $meta['LLMProvider'] ?? null,
                'LLMModel'              => $meta['LLMModel'] ?? null,
                'Autonomy'              => $playbook->Autonomy,
                'RunStatus'             => AgentRun::STATUS_INTERPRETING,
                'PlanJson'              => $this->snapshot($playbook, $steps),
                'BagJson'               => $seed,
                'Username'              => $userAuth->Username,
                'BranchID'              => $branchId,
                'StartedAt'             => Carbon::now(),
                'Status'                => 1,
            ]);

            foreach ($steps as $i => $step) {
                $class = $this->registry->classFor($step['key']);

                AgentAction::create([
                    'RunID'              => $run->ID,
                    'StepOrder'          => $i + 1,
                    'StepKey'            => $step['key'],
                    'StepLabel'          => $class::label(),
                    'RequiredPermission' => $class::permission(),
                    'IsWrite'            => $class::isWrite(),
                    'ApprovalRequired'   => $this->approvalFor($step, $class),
                    'Username'           => $userAuth->Username,
                    'InputJson'          => $this->literalsFor($step),
                    'ActionStatus'       => AgentAction::STATUS_PENDING,
                ]);
            }

            return $run->fresh('actions');
        });
    }


    private function assertWorkflowAllows(AgentPlaybook $playbook, array $seed): void
    {
        $gates = $playbook->GatesJson ?? [];

        if (empty($gates)) {
            return;
        }

        // Gates stop work being done out of order. A lookup does no work, so
        // a gate on one can only ever refuse a question it should have answered.
        if (! $this->hasWriteStep($playbook)) {
            return;
        }

        $reference = $seed['Reference'] ?? null;

        if (empty($reference)) {
            return;   // nothing to check against; the resolve step will fail loudly
        }

        $match = DB::table('container_main')
            ->where('BL', strtoupper(trim($reference)))
            ->where('Status', '<>', 9)
            ->first(['ConsignmentID', 'BL']);

        if (! $match) {
            return;   // let ResolveConsignmentStep produce the not-found message
        }

        $state = $this->workflow->state($match->ConsignmentID, $match->BL);
        $check = $this->workflow->check($gates, $state);

        if ($check['result'] === WorkflowService::RESULT_STOP) {
            throw new WorkflowGateException(
                $check['failures'],
                $this->workflow->currentStage($state)
            );
        }
    }
    // ── Plan-time checks ────────────────────────────────────────────────────

    private function assertPermitted(array $steps, UserAuth $userAuth): void
    {
        $denied = [];

        foreach ($this->registry->requirementsFor($steps) as $req) {

            if (! $req['known']) {
                throw new RuntimeException("Playbook refers to an unknown step: " . ($req['key'] ?: '?'));
            }

            if ($req['permission'] !== null && ! $userAuth->hasPermission($req['permission'])) {
                $denied[] = $req['label'];
            }
        }

        if ($denied) {
            throw new RuntimeException(
                'You do not have permission for: ' . implode(', ', $denied)
            );
        }
    }
    /** Playbook may force approval on; it may never turn it off for a write step. */
    private function approvalFor(array $step, string $class): bool
    {
        if (! $class::isWrite()) {
            return false;
        }

        return true;
    }

    private function literalsFor(array $step): ?array
    {
        $inputs = $step['inputs'] ?? [];
        $inputs = is_object($inputs) ? (array) $inputs : $inputs;

        return empty($inputs) ? null : $inputs;
    }

    private function hasWriteStep(AgentPlaybook $playbook): bool
    {
        foreach ($this->registry->requirementsFor($playbook->StepsJson ?? []) as $req) {
            if ($req['isWrite'] === true) {
                return true;
            }
        }

        return false;
    }

    /** Frozen copy of the playbook as it was at run time. */
    private function snapshot(AgentPlaybook $playbook, array $steps): array
    {
        return [
            'PlaybookKey' => $playbook->PlaybookKey,
            'Version'     => $playbook->Version,
            'Autonomy'    => $playbook->Autonomy,
            'Steps'       => $steps,
        ];
    }
}
