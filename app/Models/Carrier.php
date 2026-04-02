<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carrier extends Model
{
    protected $table      = 'ship_carrier';
    protected $primaryKey = 'CarrierID';
    public $incrementing  = true;
    protected $keyType    = 'int';
    public $timestamps    = false;

    protected $fillable = [
        'CarrierName',
        'Time',
        'Username',
        'Status',
    ];

    protected $casts = [
        'CarrierID' => 'integer',
        'Status'    => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('Status', 1);
    }

    public function scopeInactive($query)
    {
        return $query->where('Status', 0);
    }
}
