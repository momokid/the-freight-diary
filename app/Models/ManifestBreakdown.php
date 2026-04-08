<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManifestBreakdown extends Model
{
    protected $table      = 'manifestation_breakdown';
    protected $primaryKey = null;
    public $incrementing  = false;
    public $timestamps    = false;

    protected $fillable = [
        'ConsignmentID',
        'MainBL',
        'ContainerNo',
        'HouseBL',
        'ConsigneeID',
        'Consigenee2_ID',
        'Description',
        'ItemType',
        'VIN',
        'OtherInfo',
        'Weight',
        'Package',
        'Unit',
        'Username',
        'Date',
        'Time',
        'Status',
    ];

    protected $casts = [
        'ConsignmentID'  => 'integer',
        'ConsigneeID'    => 'integer',
        'Consigenee2_ID' => 'integer',
        'Weight'         => 'float',
        'Status'         => 'integer',
    ];

    public function consignee()
    {
        return $this->belongsTo(Consignee::class, 'ConsigneeID', 'ConsigneeID');
    }

    public function notifyParty()
    {
        return $this->belongsTo(Consignee::class, 'Consigenee2_ID', 'ConsigneeID');
    }
}
