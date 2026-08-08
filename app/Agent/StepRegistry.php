<?php

namespace App\Agent;

use App\Agent\Steps\AgentStep;
use InvalidArgumentException;


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


    public function requirementsFor(array $steps): array
    {
        $out = [];

        foreach ($steps as $step) {
            $key   = $step['key'] ?? '';
            $class = $this->classFor($key);

            $out[] = [
                'key'        => $key,
                'known'      => $class !== null,
                'label'      => $class ? $class::label() : null,
                'permission' => $class ? $class::permission() : null,
                'isWrite'    => $class ? $class::isWrite() : null,
            ];
        }

        return $out;
    }

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
