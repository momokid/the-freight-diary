<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class LedgerControl extends Model
{
    protected $table      = 'ledger_control';
    protected $primaryKey = 'ControlID';
    public $incrementing  = true;
    protected $keyType    = 'int';

    // No created_at or updated_at — table uses Date and Time columns instead
    public $timestamps = false;

    protected $fillable = [
        'ControlName',
        'Username',
        'Date',
        'Time',
        'Status',
    ];

    protected $casts = [
        'Status' => 'integer',
    ];

    // ── Scopes ──

    // Default scope — only active controls. Usage: LedgerControl::active()->get()
    public function scopeActive($query)
    {
        return $query->where('Status', 1);
    }

    // Inactive controls
    // Usage: LedgerControl::inactive()->get()
    public function scopeInactive($query)
    {
        return $query->where('Status', 0);
    }

    // ── Relationships ──

    // The user who created this control
    public function creator()
    {
        return $this->belongsTo(User::class, 'Username', 'ID');
    }
}
