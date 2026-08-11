<?php

namespace App\Agent;

use RuntimeException;

/**
 * A playbook was chosen correctly but something it needs is missing.
 *
 * Not a failure — the run was never created. The user is asked for what is
 * missing and the instruction is retried. Distinct from WorkflowGateException,
 * which means the work is out of order rather than under-specified.
 */
class IncompleteRunException extends RuntimeException
{
    /** @param array<int, array{name: string, type: string, description: ?string}> $missing */
    public function __construct(
        public readonly array $missing,
        public readonly string $playbookKey,
        public readonly string $taskLabel,
    ) {
        parent::__construct($this->summarise());
    }

    /** @return string[] */
    public function names(): array
    {
        return array_column($this->missing, 'name');
    }

    private function summarise(): string
    {
        $names = $this->names();

        if (empty($names)) {
            return 'Something is missing for this task.';
        }

        $last  = array_pop($names);
        $lead  = $names ? implode(', ', $names) . ' and ' . $last : $last;
        $verb  = $names ? 'are' : 'is';

        return "{$lead} {$verb} needed for {$this->taskLabel}.";
    }
}
