<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AgentStagedRegistration extends Model
{
    protected $table      = 'agent_staged_registration';
    protected $primaryKey = 'ID';
    public $timestamps    = false;

    protected $guarded = [];

    protected $casts = [
        'InferenceJson' => 'array',
        'ETA'           => 'date',
    ];

    public static function forUser(string $username): ?self
    {
        return static::where('Username', $username)->first();
    }
}
