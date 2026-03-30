<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LedgerAccount extends Model
{
    protected $table      = 'ledger_account';
    protected $primaryKey = 'AccountNo';
    public $incrementing  = true;
    protected $keyType    = 'int';

    public $timestamps = false;

    protected $fillable = [
        'ControlID',
        'CategoryID',
        'Class',
        'Nature',
        'Type',
        'AccountName',
        'Date',
        'Time',
        'Status',
        'Visible',
        'Username',
    ];

    protected $casts = [
        'ControlID'  => 'integer',
        'CategoryID' => 'integer',
        'AccountNo'  => 'integer',
        'Status'     => 'integer',
        'Visible'    => 'integer',
    ];

    // ── Scopes ──

    public function scopeActive($query)
    {
        return $query->where('Status', 1);
    }

    public function scopeInactive($query)
    {
        return $query->where('Status', 0);
    }

    // ── Relationships ──

    public function control()
    {
        return $this->belongsTo(LedgerControl::class, 'ControlID', 'ControlID');
    }

    public function category()
    {
        return $this->belongsTo(LedgerCategory::class, 'CategoryID', 'SubCategoryID');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'Username', 'ID');
    }
}
