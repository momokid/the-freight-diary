<?php

namespace App\Agent;

use App\Agent\Steps\AgentStep;
use InvalidArgumentException;

/**
 * The list of operations the agent can perform.
 *
 * Reads config/agent.php. Nothing becomes executable by living in a folder —
 * a class must be registered by key. This is the enforcement point for
 * "admins compose playbooks, they do not author steps".
 */
class StepRegistry
{
    private array $steps;

    public function __construct()
    {
        $this->steps = config('agent.steps', []);
    }

    /** Class name for a step key, or null if unregistered. */
    public function classFor(string $key): ?string
    {
        return $this->steps[$key] ?? null;
    }

    public function has(string $key): bool
    {
        return isset($this->steps[$key]);
    }

    public function keys(): array
    {
        return array_keys($this->steps);
    }

    /**
     * Every step described for the admin composition UI and the planner.
     * Static methods, so nothing is instantiated.
     */
    public function describeAll(): array
    {
        $out = [];

        foreach ($this->steps as $key => $class) {
            $out[$key] = $this->describe($key);
        }

        return $out;
    }

    public function describe(string $key): array
    {
        $class = $this->classFor($key);

        if ($class === null) {
            throw new InvalidArgumentException("Unknown step: {$key}");
        }

        return [
            'key'        => $class::key(),
            'label'      => $class::label(),
            'permission' => $class::permission(),
            'isWrite'    => $class::isWrite(),
            'inputs'     => $class::inputs(),
            'outputs'    => $class::outputs(),
        ];
    }

    /**
     * Validate the registry itself. Catches the mistakes that would otherwise
     * surface mid-run: a missing class, a class that does not implement the
     * contract, or a key that disagrees with the class it points at.
     */
    public function validate(): array
    {
        $problems = [];

        foreach ($this->steps as $key => $class) {

            if (! class_exists($class)) {
                $problems[] = "{$key}: class {$class} does not exist";
                continue;
            }

            if (! is_subclass_of($class, AgentStep::class)) {
                $problems[] = "{$key}: {$class} does not implement AgentStep";
                continue;
            }

            if ($class::key() !== $key) {
                $problems[] = "{$key}: class reports key '{$class::key()}'";
            }

            foreach ($class::inputs() as $name => $spec) {
                if (! is_array($spec) || ! isset($spec['type'])) {
                    $problems[] = "{$key}: input '{$name}' has no declared type";
                }
            }
        }

        return $problems;
    }
}
