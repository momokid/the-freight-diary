<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManifestTemp extends Model
{
    protected $table      = 'temp_manifestation_breakdown';
    protected $primaryKey = null;
    public $incrementing  = false;
    public $timestamps    = false;

    protected $fillable = [
        'ConsignmentID',
        'MainBL',
        'ContainerNo',
        'HouseBL',
        'CargoLineID',
        'CosigneeID',
        'Cosignee2_ID',
        'Description',
        'ItemType',
        'VIN',
        'OtherInfo',
        'Weight',
        'Package',
        'Unit',
        'Username',
        'Time',
    ];

    protected $casts = [
        'ConsignmentID' => 'integer',
        'CosigneeID'    => 'integer',
        'Cosignee2_ID'  => 'integer',
        'Weight'        => 'float',
    ];

    public function consignee()
    {
        return $this->belongsTo(Consignee::class, 'CosigneeID', 'ConsigneeID');
    }

    public function notifyParty()
    {
        return $this->belongsTo(Consignee::class, 'Cosignee2_ID', 'ConsigneeID');
    }
}
