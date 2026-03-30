<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HandlingCharge extends Model
{
    protected $table      = 'handling_charge';
    protected $primaryKey = 'AccountNo';
    public $incrementing  = false;
    protected $keyType    = 'int';

    public $timestamps = false;

    protected $fillable = [
        'AccountNo',
        'Amount',
        'POrder',
        'Username',
        'Time',
    ];

    protected $casts = [
        'AccountNo' => 'integer',
        'Amount'    => 'float',
        'POrder'    => 'integer',
    ];

    // ── Relationships ──

    public function account()
    {
        return $this->belongsTo(LedgerAccount::class, 'AccountNo', 'AccountNo');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'Username', 'ID');
    }
}
