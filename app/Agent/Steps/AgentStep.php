<?php

namespace App\Agent\Steps;

use App\Agent\AgentContext;

/**
 * Contract for every agent step.
 *
 * A step is a single vetted operation. Admins compose ordered sequences of
 * these into playbooks; they cannot author new ones. Adding a capability is
 * always a code change plus a registry entry.
 *
 * The static methods describe the step without instantiating it, so the
 * planner and the admin composition UI can inspect the library cheaply.
 */
interface AgentStep
{
    /** Stable registry key, e.g. 'consignment.resolve'. Never reused. */
    public static function key(): string;

    /** Human-readable label shown in plans, audit rows and the composer. */
    public static function label(): string;

    /**
     * Permission the requesting user must hold, or null if read-only.
     * Checked at plan time and again at execution.
     */
    public static function permission(): ?string;

    /** True if the step writes to the database. Drives approval gating. */
    public static function isWrite(): bool;

    /**
     * Declared inputs, keyed by name:
     *   ['BL' => ['type' => 'string', 'required' => true]]
     *
     * The runner fills these from the shared bag, so names must match the
     * outputs of earlier steps.
     */
    public static function inputs(): array;

    /** Output names this step contributes to the shared bag. */
    public static function outputs(): array;

    /**
     * Execute the step.
     *
     * Returns the declared outputs, keyed by name. Two reserved keys may also
     * be returned by write steps and are stripped before merging into the bag:
     *
     *   _targetTable — table written to, e.g. 'manifestation_breakdown'
     *   _targetKey   — identifying value, e.g. the BL or receipt number
     *
     * Failure is signalled by throwing. The runner catches it, records the
     * message against the action, halts the run and marks remaining steps
     * skipped. Steps never catch their own failures to continue silently.
     */
    public function run(array $input, AgentContext $context): array;
}