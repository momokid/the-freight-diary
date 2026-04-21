<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DisbursementAnalysis extends Model
{
    public $timestamps = false;

    protected $table = 'disbursement_analysis';

    protected $fillable = [
        'ConsigneeID',
        'BL',
        'HBL',
        'ContainerNo',
        'TotalCashReceipt',
        'ReceiptNo',
        'AccountID',
        'Revenue',
        'Expenditure',
        'Stamp',
        'Username',
        'Date',
        'Time',
        'Status',
        'Type',
    ];

    // ── Relationships ──────────────────────────────────────────

    public function account()
    {
        return $this->belongsTo(LedgerAccount::class, 'AccountID', 'AccountNo');
    }

    public function consignee()
    {
        return $this->belongsTo(Consignee::class, 'ConsigneeID', 'ConsigneeID');
    }

    // ── Scopes ─────────────────────────────────────────────────

    public function scopeForBL($query, string $bl)
    {
        return $query->where('BL', $bl);
    }

    public function scopeForHBL($query, string $hbl)
    {
        return $query->where('HBL', $hbl);
    }

    public function scopePending($query)
    {
        return $query->where('Status', '1');
    }

    public function scopeApproved($query)
    {
        return $query->where('Status', '0');
    }
}
