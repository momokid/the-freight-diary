<?php

namespace App\Agent;

use App\Models\AgentPlaybook;
use App\Models\UserAuth;

/**
 * The playbooks a given user may run, described for the two consumers that
 * need them: the Layer 3 prompt and the command picker.
 *
 * This is a read path. A playbook naming a step key that is not registered is
 * excluded and recorded in problems(), never thrown — RunFactory throws on the
 * same condition because it is about to execute. Disabling a step in config
 * must not take the Command Center down with it.
 */
class PlaybookCatalogue
{
    private const DESCRIPTION_LIMIT = 200;
    private const MAX_EXAMPLES      = 3;

    private ?array $playbooks = null;

    /** @var array<string, AgentPlaybook[]> */
    private array $memo = [];

    private array $problems = [];

    public function __construct(
        private StepRegistry $registry
    ) {}

    /** Active playbooks the user holds every required permission for. */
    public function permitted(UserAuth $userAuth): array
    {
        $memoKey = (string) $userAuth->Username;

        if (isset($this->memo[$memoKey])) {
            return $this->memo[$memoKey];
        }

        $out = [];

        foreach ($this->all() as $playbook) {
            $steps = $playbook->StepsJson ?? [];

            if (empty($steps)) {
                $this->problems[] = "{$playbook->PlaybookKey}: no steps declared";
                continue;
            }

            $requirements = $this->registry->requirementsFor($steps);
            $unknown      = array_filter($requirements, fn($r) => ! $r['known']);

            if ($unknown) {
                $this->problems[] = "{$playbook->PlaybookKey}: unregistered step(s) "
                    . implode(', ', array_column($unknown, 'key'));
                continue;
            }

            if ($this->userHolds($requirements, $userAuth)) {
                $out[] = $playbook;
            }
        }

        return $this->memo[$memoKey] = $out;
    }

    /** Payload for the Layer 3 prompt. No steps, gates or permission names. */
    public function forPrompt(UserAuth $userAuth): array
    {
        return array_map(fn(AgentPlaybook $p) => [
            'key'         => $p->PlaybookKey,
            'title'       => $p->Title,
            'description' => $this->shortDescription($p->Description),
            'params'      => $this->paramsFor($p),
            'examples'    => array_slice($p->intentExamples(), 0, self::MAX_EXAMPLES),
        ], $this->permitted($userAuth));
    }

    /** Payload for the command picker and admin surfaces. */
    public function forPicker(UserAuth $userAuth): array
    {
        return array_map(fn(AgentPlaybook $p) => [
            'key'         => $p->PlaybookKey,
            'title'       => $p->Title,
            'description' => $this->shortDescription($p->Description),
            'taskType'    => $p->TaskType,
            'autonomy'    => $p->Autonomy,
            'isWrite'     => $this->isWrite($p),
            'params'      => $this->paramsFor($p),
        ], $this->permitted($userAuth));
    }

    /** Playbooks skipped, and why. For admins — never sent to the model. */
    public function problems(): array
    {
        return array_values(array_unique($this->problems));
    }

    // ── Derivation ──────────────────────────────────────────────────────────

    /**
     * What the caller must supply.
     *
     * Each step's declared inputs, less anything an earlier step already puts
     * in the bag and anything pinned as a literal in the playbook. Derived so
     * a step signature change cannot leave a stale parameter behind; ParamsJson
     * overrides the description only.
     *
     * Public because RunFactory checks a seed against this before planning.
     * Two derivations of the same thing would drift.
     */
    public function paramsFor(AgentPlaybook $playbook): array
    {
        $available = [];
        $params    = [];
        $overrides = $playbook->ParamsJson ?? [];

        foreach ($playbook->StepsJson ?? [] as $step) {
            $class = $this->registry->classFor(is_array($step) ? ($step['key'] ?? '') : '');

            if ($class === null) {
                continue;
            }

            $literals = (array) ($step['inputs'] ?? []);

            foreach ($class::inputs() as $name => $spec) {

                if (isset($available[$name]) || isset($params[$name]) || array_key_exists($name, $literals)) {
                    continue;
                }

                $params[$name] = [
                    'type'        => $spec['type'] ?? 'string',
                    'required'    => (bool) ($spec['required'] ?? false),
                    'description' => $overrides[$name]['description'] ?? $spec['description'] ?? null,
                ];
            }

            foreach ($class::outputs() as $output) {
                $available[$output] = true;
            }
        }

        return $params;
    }

    // ── Helpers ─────────────────────────────────────────────────────────────

    /** @return AgentPlaybook[] */
    private function all(): array
    {
        return $this->playbooks ??= AgentPlaybook::active()
            ->orderBy('PlaybookKey')
            ->get()
            ->all();
    }

    private function userHolds(array $requirements, UserAuth $userAuth): bool
    {
        foreach ($requirements as $requirement) {
            $permission = $requirement['permission'];

            if ($permission !== null && ! $userAuth->hasPermission($permission)) {
                return false;
            }
        }

        return true;
    }

    private function isWrite(AgentPlaybook $playbook): bool
    {
        foreach ($this->registry->requirementsFor($playbook->StepsJson ?? []) as $requirement) {
            if ($requirement['isWrite'] === true) {
                return true;
            }
        }

        return false;
    }

    /** Admin-editable text goes into every prompt — one sentence, hard capped. */
    private function shortDescription(?string $text): string
    {
        $text = trim(preg_replace('/\s+/', ' ', (string) $text));

        if ($text === '') {
            return '';
        }

        if (preg_match('/^(.+?[.!?])(\s|$)/u', $text, $m)) {
            $text = $m[1];
        }

        return mb_strlen($text) > self::DESCRIPTION_LIMIT
            ? mb_substr($text, 0, self::DESCRIPTION_LIMIT - 1) . '…'
            : $text;
    }
}
