<?php

namespace App\Agent;

use RuntimeException;

/**
 * Thrown when a task is attempted out of workflow order.
 *
 * Not an error — the system working. Carries the failures so the UI can show
 * what is missing and where to go and fix it.
 */
class WorkflowGateException extends RuntimeException
{
    public function __construct(
        public readonly array $failures,
        public readonly string $currentStage,
        string $message = 'This task cannot run yet.'
    ) {
        parent::__construct($message);
    }

    public function toArray(): array
    {
        return [
            'currentStage' => $this->currentStage,
            'failures'     => $this->failures,
        ];
    }
}
