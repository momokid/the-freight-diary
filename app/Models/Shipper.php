<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shipper extends Model
{
    protected $table      = 'shipper_main';
    protected $primaryKey = 'ShipperID';
    public $incrementing  = true;
    protected $keyType    = 'int';
    public $timestamps    = false;

    protected $fillable = [
        'ShipperName',
        'AddressLine1',
        'AddressLine2',
        'AddressLine3',
        'AddressLine4',
        'Username',
        'Date',
        'Time',
        'Status',
    ];

    protected $casts = [
        'ShipperID' => 'integer',
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
