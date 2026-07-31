<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgentAction extends Model
{
    protected $table      = 'agent_actions';
    protected $primaryKey = 'ID';
    public $incrementing  = true;
    public $timestamps    = false;

    // Step lifecycle
    public const STATUS_PENDING  = 'pending';
    public const STATUS_AWAITING = 'awaiting_approval';
    public const STATUS_RUNNING  = 'running';
    public const STATUS_DONE     = 'done';
    public const STATUS_SKIPPED  = 'skipped';
    public const STATUS_FAILED   = 'failed';

    protected $fillable = [
        'RunID',
        'StepOrder',
        'StepKey',
        'StepLabel',
        'RequiredPermission',
        'IsWrite',
        'ApprovalRequired',
        'Username',
        'ApprovedBy',
        'ApprovedAt',
        'InputJson',
        'OutputJson',
        'ActionStatus',
        'FailureReason',
        'TargetTable',
        'TargetKey',
        'DurationMs',
        'StartedAt',
        'CompletedAt',
    ];

    protected $casts = [
        'InputJson'        => 'array',
        'OutputJson'       => 'array',
        'IsWrite'          => 'boolean',
        'ApprovalRequired' => 'boolean',
        'ApprovedAt'       => 'datetime',
        'StartedAt'        => 'datetime',
        'CompletedAt'      => 'datetime',
    ];

    // ── Relations ───────────────────────────────────────────────────────────

    public function run()
    {
        return $this->belongsTo(AgentRun::class, 'RunID', 'ID');
    }

    // ── State helpers ───────────────────────────────────────────────────────

    public function isBlocking(): bool
    {
        return $this->ActionStatus === self::STATUS_AWAITING;
    }

    public function isFinished(): bool
    {
        return in_array($this->ActionStatus, [
            self::STATUS_DONE,
            self::STATUS_SKIPPED,
            self::STATUS_FAILED,
        ], true);
    }

    /** Scope: what the agent did to a specific record — the audit lookup. */
    public function scopeTouching($query, string $table, string $key)
    {
        return $query->where('TargetTable', $table)
            ->where('TargetKey', $key);
    }
}
