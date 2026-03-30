<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DisbursementAccount extends Model
{
    protected $table      = 'disbursement_accounts';
    protected $primaryKey = 'AccountNo';
    public $incrementing  = false;
    protected $keyType    = 'int';

    public $timestamps = false;

    protected $fillable = [
        'AccountNo',
        'Username',
        'Date',
    ];

    protected $casts = [
        'AccountNo' => 'integer',
    ];

    // ── Relationships ──

    public function account()
    {
        return $this->belongsTo(LedgerAccount::class, 'AccountNo', 'AccountNo');
    }
}
