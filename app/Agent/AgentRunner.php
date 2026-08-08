<?php

namespace App\Agent;

use App\Agent\Steps\AgentStep;
use App\Models\AgentAction;
use App\Models\AgentRun;
use App\Models\UserAuth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

/**
 * Executes a run's steps in order.
 *
 * Stops on the first step that fails, or on the first write step that needs
 * approval. Both leave the run resumable: the shared bag is persisted to
 * agent_runs.BagJson after every step.
 */
class AgentRunner
{
    public function __construct(
        private StepRegistry $registry
    ) {}

    /**
     * Run from the beginning, or continue where a previous call stopped.
     * Safe to call repeatedly — completed steps are skipped.
     */
    public function execute(AgentRun $run): AgentRun
    {
        if ($run->isFinished()) {
            return $run;
        }

        $userAuth = UserAuth::where('Username', $run->Username)->first();

        if (! $userAuth) {
            return $this->failRun($run, 'The requesting user no longer has a permission record.');
        }

        $context = new AgentContext($run, $run->Username, $run->BranchID, $userAuth);
        $context->merge($run->BagJson ?? []);

        $run->RunStatus = AgentRun::STATUS_RUNNING;
        $run->save();

        foreach ($run->actions as $action) {

            if ($action->isFinished()) {
                continue;   // already done on an earlier pass
            }

            // ── Approval gate ──
            if ($this->needsApproval($action, $run)) {
                $action->ActionStatus = AgentAction::STATUS_AWAITING;
                $action->save();

                $run->RunStatus = AgentRun::STATUS_AWAITING;
                $run->BagJson   = $context->all();
                $run->save();

                return $run;
            }

            $outcome = $this->runStep($action, $context, $userAuth);

            if ($outcome !== null) {
                $run->BagJson = $context->all();
                return $this->failRun($run, $outcome, $action);
            }

            $run->BagJson = $context->all();
            $run->save();
        }

        $run->RunStatus   = AgentRun::STATUS_COMPLETED;
        $run->CompletedAt = Carbon::now();
        $run->BagJson     = $context->all();
        $run->save();

        return $run;
    }

    // ── One step ────────────────────────────────────────────────────────────

    /** Returns null on success, or a failure message. */
    private function runStep(AgentAction $action, AgentContext $context, UserAuth $userAuth): ?string
    {
        $class = $this->registry->classFor($action->StepKey);

        if ($class === null) {
            return "Unknown step: {$action->StepKey}";
        }

        // Re-check permission at execution — rights may have changed since planning
        if (! $context->can($class::permission())) {
            return "You do not have permission for: {$class::label()}";
        }

        $action->ActionStatus = AgentAction::STATUS_RUNNING;
        $action->StartedAt    = Carbon::now();
        $action->save();

        $started = microtime(true);

        try {
            $step  = app($class);                       // container-resolved: steps may inject services
            $input = $this->gatherInputs($class, $context, $action);

            $output = DB::transaction(fn() => $step->run($input, $context));
        } catch (Throwable $e) {
            report($e);

            $action->ActionStatus  = AgentAction::STATUS_FAILED;
            $action->FailureReason = $e->getMessage();
            $action->DurationMs    = (int) ((microtime(true) - $started) * 1000);
            $action->CompletedAt   = Carbon::now();
            $action->save();

            return $e->getMessage();
        }

        $context->merge($output);

        $action->ActionStatus = AgentAction::STATUS_DONE;
        $action->OutputJson   = $this->summarise($output);
        $action->TargetTable  = $output['_targetTable'] ?? null;
        $action->TargetKey    = $output['_targetKey'] ?? null;
        $action->DurationMs   = (int) ((microtime(true) - $started) * 1000);
        $action->CompletedAt  = Carbon::now();
        $action->save();

        return null;
    }

    /**
     * Fill a step's declared inputs from the shared bag.
     * Values already stored on the action take precedence — that is how the
     * planner passes a literal, and how an edited approval overrides a value.
     */
    private function gatherInputs(string $class, AgentContext $context, AgentAction $action): array
    {
        $stored = $action->InputJson ?? [];
        $input  = [];

        foreach ($class::inputs() as $name => $spec) {
            $value = $stored[$name] ?? $context->get($name);

            if ($value === null && ($spec['required'] ?? false)) {
                throw new RuntimeException("Missing required input '{$name}' for {$class::label()}");
            }

            $input[$name] = $value;
        }

        return $input;
    }

    // ── Approval ────────────────────────────────────────────────────────────

    private function needsApproval(AgentAction $action, AgentRun $run): bool
    {
        if (! $action->IsWrite || ! $action->ApprovalRequired) {
            return false;
        }

        if ($action->ApprovedBy !== null) {
            return false;   // already approved on an earlier pass
        }

        return ! $run->isAutonomous();
    }

    // ── Failure ─────────────────────────────────────────────────────────────

    private function failRun(AgentRun $run, string $reason, ?AgentAction $failed = null): AgentRun
    {
        $run->RunStatus     = AgentRun::STATUS_FAILED;
        $run->FailureReason = $reason;
        $run->CompletedAt   = Carbon::now();
        $run->save();

        // Everything after the failure never ran
        $query = AgentAction::where('RunID', $run->ID)
            ->whereIn('ActionStatus', [AgentAction::STATUS_PENDING, AgentAction::STATUS_AWAITING]);

        if ($failed) {
            $query->where('StepOrder', '>', $failed->StepOrder);
        }

        $query->update(['ActionStatus' => AgentAction::STATUS_SKIPPED]);

        return $run->fresh();
    }

    /**
     * Audit rows record what happened, not a second copy of your data.
     * Scalars are kept; arrays and long strings are reduced to a shape note.
     * Strings are also repaired — legacy rows carry bytes that are not valid
     * UTF-8 and would fail the JSON cast on save.
     */
    private function summarise(array $output): array
    {
        $summary = [];

        foreach ($output as $key => $value) {
            if (str_starts_with($key, '_')) {
                continue;
            }

            if (is_array($value)) {
                $summary[$key] = '[' . count($value) . ' items]';
            } elseif (is_string($value)) {
                $clean = mb_check_encoding($value, 'UTF-8')
                    ? $value
                    : mb_convert_encoding($value, 'UTF-8', 'Windows-1252');

                $summary[$key] = mb_strlen($clean) > 200
                    ? mb_substr($clean, 0, 200) . '…'
                    : $clean;
            } else {
                $summary[$key] = $value;
            }
        }

        return $summary;
    }
}
