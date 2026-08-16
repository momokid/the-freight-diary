<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgentPlaybook extends Model
{
    protected $table      = 'agent_playbooks';
    protected $primaryKey = 'ID';
    public $incrementing  = true;
    public $timestamps    = false;

    public const STATUS_ACTIVE   = 1;
    public const STATUS_DISABLED = 0;
    public const STATUS_DELETED  = 9;

    protected $fillable = [
        'PlaybookKey',
        'TaskType',
        'Title',
        'Description',
        'IntentExamples',
        'StepsJson',
        'ParamsJson',
        'GatesJson',
        'Autonomy',
        'IsSystem',
        'Version',
        'Username',
        'BranchID',
        'CreatedAt',
        'UpdatedAt',
        'CardTitle',
        'SortOrder',
        'Status',
    ];

    protected $casts = [
        'StepsJson' => 'array',
        'ParamsJson' => 'array',
        'GatesJson'  => 'array',
        'IsSystem'  => 'boolean',
        'CreatedAt' => 'datetime',
        'UpdatedAt' => 'datetime',
    ];

    // ── State helpers ───────────────────────────────────────────────────────

    public function isAutonomous(): bool
    {
        return $this->Autonomy === AgentRun::FILL_COMMIT;
    }

    /** System playbooks ship with the code — admins may not edit their steps. */
    public function isEditable(): bool
    {
        return ! $this->IsSystem;
    }

    /** Seed phrasings for Layer 2 intent matching, one per line. */
    public function intentExamples(): array
    {
        if (empty($this->IntentExamples)) {
            return [];
        }

        return array_values(array_filter(array_map(
            'trim',
            preg_split('/\r\n|\r|\n/', $this->IntentExamples)
        )));
    }

    // ── Scopes ──────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('Status', self::STATUS_ACTIVE);
    }

    public function scopeOfTask($query, string $taskType)
    {
        return $query->where('TaskType', $taskType);
    }
}
