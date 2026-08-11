<?php

namespace App\Http\Controllers;

use App\Agent\AgentRunner;
use App\Agent\Intent\IntentNormaliser;
use App\Agent\Intent\IntentRouter;
use App\Agent\PlaybookCatalogue;
use App\Agent\RunFactory;
use App\Agent\WorkflowGateException;
use App\Models\AgentPlaybook;
use App\Models\AgentRun;
use App\Models\UserAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Agent\IncompleteRunException;

class AgentController extends Controller
{
    public function __construct(
        private IntentRouter $router,
        private RunFactory $factory,
        private AgentRunner $runner,
        private PlaybookCatalogue $catalogue,
        private IntentNormaliser $normaliser,
    ) {}

    public function run(Request $request)
    {
        $validated = $request->validate([
            'instruction' => ['required', 'string', 'max:500'],
            'modality'    => ['nullable', 'in:text,speech,document'],
            'playbook'    => ['nullable', 'string', 'max:60'],
        ]);

        $instruction = trim($validated['instruction']);
        $modality    = $validated['modality'] ?? AgentRun::INPUT_TEXT;
        $chosen      = $validated['playbook'] ?? null;

        $user     = Auth::user();
        $userAuth = UserAuth::where('Username', $user->ID)->first();

        if (! $userAuth) {
            return response()->json([
                'outcome' => 'error',
                'message' => 'No permission record found for your account.',
            ], 403);
        }

        // A pick from the suggestion list replaces resolution entirely.
        $decision = $chosen
            ? $this->fromChoice($chosen, $instruction, $userAuth)
            : $this->router->route($instruction, $userAuth);

        if ($decision === null) {
            return response()->json([
                'outcome' => 'unresolved',
                'message' => 'That task is not available.',
            ]);
        }

        // ── Bare reference: let the Command Center search instead ──
        if ($decision['decision'] === IntentRouter::SEARCH) {
            return response()->json([
                'outcome' => 'search',
                'query'   => $decision['references'][0] ?? $instruction,
            ]);
        }

        // ── Below the confidence floor: let the user pick rather than guess ──
        if ($decision['decision'] === IntentRouter::SUGGEST) {
            return response()->json([
                'outcome'     => 'suggest',
                'message'     => 'Did you mean one of these?',
                'suggestions' => $decision['suggestions'],
                'instruction' => $instruction,
            ]);
        }

        // ── Nothing matched at any layer ──
        if ($decision['decision'] === IntentRouter::UNRESOLVED) {
            return response()->json([
                'outcome' => 'unresolved',
                'message' => "I don't know how to do that yet.",
                'pattern' => $decision['pattern'],
            ]);
        }

        $playbook = AgentPlaybook::active()->find($decision['playbookId']);

        if (! $playbook) {
            return response()->json([
                'outcome' => 'unresolved',
                'message' => 'That task is not available.',
            ]);
        }

        try {
            $run = $this->factory->create(
                $playbook,
                $userAuth,
                $user->BranchID ?? '',
                array_merge(
                    $decision['params'] ?? [],
                    ['Reference' => $decision['reference'] ?? ($decision['references'][0] ?? null)]
                ),
                [
                    'RawInstruction'  => $instruction,
                    'InputModality'   => $modality,
                    'IntentKey'       => $decision['intentKey'],
                    'ResolutionLayer' => $decision['resolutionLayer'],
                    'LLMProvider'     => $decision['llm']['provider'] ?? null,
                    'LLMModel'        => $decision['llm']['model'] ?? null,
                ]
            );

            $run = $this->runner->execute($run);

            // A Layer 3 guess earns its cache row only once the run succeeds.
            if (($decision['resolutionLayer'] ?? null) === 3
                && $run->RunStatus === AgentRun::STATUS_COMPLETED
            ) {
                $this->router->confirm($instruction, $playbook, $decision['confidence'] ?? 0.0);
            }
        } catch (WorkflowGateException $e) {

            // Not an error — the workflow guard doing its job
            return response()->json([
                'outcome'      => 'blocked',
                'message'      => $e->getMessage(),
                'currentStage' => $e->currentStage,
                'failures'     => array_map(fn($f) => [
                    'message' => $f['message'],
                    'label'   => $f['fix']['label'] ?? null,
                    'url'     => $this->fixUrl($f['fix']['route'] ?? null),
                ], $e->failures),
            ]);
        } catch (IncompleteRunException $e) {

            // The task was understood; something it needs was not supplied.
            return response()->json([
                'outcome'     => 'incomplete',
                'message'     => $e->getMessage(),
                'playbook'    => $e->playbookKey,
                'taskLabel'   => $e->taskLabel,
                'missing'     => $e->missing,
                'instruction' => $instruction,
            ]);
        } catch (IncompleteRunException $e) {

            // The task was understood; something it needs was not supplied.
            return response()->json([
                'outcome'     => 'incomplete',
                'message'     => $e->getMessage(),
                'playbook'    => $e->playbookKey,
                'taskLabel'   => $e->taskLabel,
                'missing'     => $e->missing,
                'instruction' => $instruction,
            ]);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'outcome' => 'error',
                'message' => $e->getMessage(),
                'debug'   => app()->environment('local') ? $e->getTraceAsString() : null,
            ], 500);
        }

        return response()->json($this->present($run));
    }

    /**
     * The user picked from the suggestion list. Their choice replaces
     * resolution — but only from playbooks they are actually permitted, so a
     * forged key from the browser cannot reach RunFactory.
     */
    private function fromChoice(string $key, string $instruction, UserAuth $userAuth): ?array
    {
        $permitted = array_column($this->catalogue->forPrompt($userAuth), 'key');

        if (! in_array($key, $permitted, true)) {
            return null;
        }

        $playbook = AgentPlaybook::active()->where('PlaybookKey', $key)->first();

        if (! $playbook) {
            return null;
        }

        $n = $this->normaliser->normalise($instruction);

        return [
            'decision'        => IntentRouter::AGENT,
            'intentKey'       => $playbook->PlaybookKey,
            'playbookId'      => $playbook->ID,
            'resolutionLayer' => 3,
            'confidence'      => 1.0,   // the user told us directly
            'params'          => [],
            'reference'       => $n['references'][0] ?? null,
            'references'      => $n['references'],
            'pattern'         => $n['pattern'],
            'fingerprint'     => $n['fingerprint'],
            'llm'             => null,
        ];
    }

    /** Shape a finished run for the thread view. */
    private function present(AgentRun $run): array
    {
        $bag = $run->BagJson ?? [];

        return [
            'outcome'   => match ($run->RunStatus) {
                AgentRun::STATUS_FAILED   => 'failed',
                AgentRun::STATUS_AWAITING => 'awaiting_approval',
                default                   => 'done',
            },
            'runId'     => $run->ID,
            'taskLabel' => $run->TaskLabel,
            'status'    => $run->RunStatus,
            'reply'     => $bag['Reply'] ?? null,
            'facts'     => $bag['ReplyFacts'] ?? [],
            'delayed'   => (bool) ($bag['IsDelayed'] ?? false),
            'message'   => $run->FailureReason,
            'steps'     => $run->actions->map(fn($a) => [
                'label'  => $a->StepLabel,
                'status' => $a->ActionStatus,
                'ms'     => $a->DurationMs,
            ])->all(),
        ];
    }

    /** Route name to URL, tolerating a name that no longer exists. */
    private function fixUrl(?string $routeName): ?string
    {
        if ($routeName === null) {
            return null;
        }

        try {
            return route($routeName);
        } catch (\Throwable $e) {
            report($e);
            return null;
        }
    }
}
