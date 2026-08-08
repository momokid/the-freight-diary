<?php

namespace App\Agent;

use App\Models\AgentRun;
use App\Models\UserAuth;

/**
 * Everything a step is allowed to know about the run it belongs to.
 *
 * Passed to every step. Deliberately narrow: steps get the requesting user,
 * their permissions, and values produced by earlier steps — nothing else.
 * A step that needs more than this is probably doing too much.
 */
class AgentContext
{
    /** Values produced by earlier steps, keyed by output name. */
    private array $bag = [];

    public function __construct(
        public readonly AgentRun $run,
        public readonly string $username,
        public readonly string $branchId,
        public readonly UserAuth $userAuth,
    ) {}

    // ── Shared bag ──────────────────────────────────────────────────────────

    public function get(string $key, $default = null)
    {
        return $this->bag[$key] ?? $default;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->bag);
    }

    public function merge(array $values): void
    {
        foreach ($values as $key => $value) {
            if (str_starts_with($key, '_')) {
                continue; // reserved: _targetTable, _targetKey
            }
            $this->bag[$key] = $this->clean($value);
        }
    }


    private function clean($value)
    {
        if (is_string($value)) {
            return mb_check_encoding($value, 'UTF-8')
                ? $value
                : mb_convert_encoding($value, 'UTF-8', 'Windows-1252');
        }

        if (is_array($value)) {
            return array_map(fn($v) => $this->clean($v), $value);
        }

        return $value;
    }

    /** Whole bag — for logging and for building the plan preview. */
    public function all(): array
    {
        return $this->bag;
    }

    // ── Permissions ─────────────────────────────────────────────────────────

    /**
     * Always the requesting user's rights. The agent has no identity of its
     * own and can never do anything the user could not do by hand.
     */
    public function can(?string $permission): bool
    {
        return $permission === null || $this->userAuth->hasPermission($permission);
    }

    // ── Autonomy ────────────────────────────────────────────────────────────

    /** True when this run may commit writes without per-step approval. */
    public function isAutonomous(): bool
    {
        return $this->run->Autonomy === 'fill_commit';
    }
}
