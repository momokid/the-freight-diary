<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgentRun extends Model
{
    protected $table      = 'agent_runs';
    protected $primaryKey = 'ID';
    public $incrementing  = true;
    public $timestamps    = false;

    // Run lifecycle
    public const STATUS_INTERPRETING = 'interpreting';
    public const STATUS_AWAITING     = 'awaiting_approval';
    public const STATUS_RUNNING      = 'running';
    public const STATUS_COMPLETED    = 'completed';
    public const STATUS_FAILED       = 'failed';
    public const STATUS_CANCELLED    = 'cancelled';

    // Autonomy
    public const FILL_STOP   = 'fill_stop';
    public const FILL_COMMIT = 'fill_commit';

    // Input modality — recorded from day one, drives TTS replies later
    public const INPUT_TEXT     = 'text';
    public const INPUT_SPEECH   = 'speech';
    public const INPUT_DOCUMENT = 'document';

    protected $fillable = [
        'PlaybookID',
        'TaskType',
        'TaskLabel',
        'IntentKey',
        'RawInstruction',
        'NormalisedInstruction',
        'InputModality',
        'ResolutionLayer',
        'LLMProvider',
        'LLMModel',
        'PromptTokens',
        'CompletionTokens',
        'Autonomy',
        'RunStatus',
        'PlanJson',
        'FailureReason',
        'Username',
        'BranchID',
        'StartedAt',
        'CompletedAt',
        'Status',
    ];

    protected $casts = [
        'PlanJson'    => 'array',
        'StartedAt'   => 'datetime',
        'CompletedAt' => 'datetime',
    ];

    // ── Relations ───────────────────────────────────────────────────────────

    public function actions()
    {
        return $this->hasMany(AgentAction::class, 'RunID', 'ID')
            ->orderBy('StepOrder');
    }

    public function playbook()
    {
        return $this->belongsTo(AgentPlaybook::class, 'PlaybookID', 'ID');
    }

    // ── State helpers ───────────────────────────────────────────────────────

    public function isFinished(): bool
    {
        return in_array($this->RunStatus, [
            self::STATUS_COMPLETED,
            self::STATUS_FAILED,
            self::STATUS_CANCELLED,
        ], true);
    }

    public function isAutonomous(): bool
    {
        return $this->Autonomy === self::FILL_COMMIT;
    }

    /** Scope: runs visible to a user — own runs unless they hold AgentViewAll. */
    public function scopeVisibleTo($query, UserAuth $userAuth)
    {
        if ($userAuth->hasPermission('AgentViewAll')) {
            return $query;
        }

        return $query->where('Username', $userAuth->Username);
    }
}
