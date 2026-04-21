<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DisbursementTempAnalysis extends Model
{
    public $timestamps = false;

    protected $table = 'disbursement_temp_analysis';

    protected $fillable = [
        'AccountNo',
        'BL',
        'HouseBL',
        'ContainerNo',
        'ConsigneeID',
        'Amount',
        'Type',
        'Status',
        'Username',
        'Time',
    ];

    // ── Relationships ──────────────────────────────────────────

    public function account()
    {
        return $this->belongsTo(LedgerAccount::class, 'AccountNo', 'AccountNo');
    }

    // ── Scopes ─────────────────────────────────────────────────

    public function scopeForUser($query, string $username)
    {
        return $query->where('Username', $username);
    }

    public function scopeReady($query)
    {
        return $query->where('Status', '2')->where('Amount', '>', 0);
    }
}
