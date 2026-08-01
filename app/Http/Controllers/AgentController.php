<?php

namespace App\Http\Controllers;

use App\Agent\AgentRunner;
use App\Agent\Intent\IntentRouter;
use App\Agent\RunFactory;
use App\Models\AgentPlaybook;
use App\Models\AgentRun;
use App\Models\UserAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgentController extends Controller
{
    public function __construct(
        private IntentRouter $router,
        private RunFactory $factory,
        private AgentRunner $runner,
    ) {}

    public function run(Request $request)
    {
        $validated = $request->validate([
            'instruction' => ['required', 'string', 'max:500'],
            'modality'    => ['nullable', 'in:text,speech,document'],
        ]);

        $instruction = trim($validated['instruction']);
        $modality    = $validated['modality'] ?? AgentRun::INPUT_TEXT;

        $user     = Auth::user();
        $userAuth = UserAuth::where('Username', $user->ID)->first();

        if (! $userAuth) {
            return response()->json([
                'outcome' => 'error',
                'message' => 'No permission record found for your account.',
            ], 403);
        }

        $decision = $this->router->route($instruction);

        // ── Bare reference: let the Command Center search instead ──
        if ($decision['decision'] === IntentRouter::SEARCH) {
            return response()->json([
                'outcome' => 'search',
                'query'   => $decision['references'][0] ?? $instruction,
            ]);
        }

        // ── Nothing matched: Layer 3 will handle this once GLM is wired ──
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
                ['Reference' => $decision['references'][0] ?? null],
                [
                    'RawInstruction'  => $instruction,
                    'InputModality'   => $modality,
                    'IntentKey'       => $decision['intentKey'],
                    'ResolutionLayer' => $decision['resolutionLayer'],
                ]
            );

            $run = $this->runner->execute($run);
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

    /** Shape a finished run for the thread view. */
    private function present(AgentRun $run): array
    {
        $bag = $run->BagJson ?? [];

        return [
            'outcome'   => $run->RunStatus === AgentRun::STATUS_FAILED ? 'failed' : 'done',
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
}
