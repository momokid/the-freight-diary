<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContainerDetails extends Model
{
    protected $table = 'container_details';

    protected $primaryKey = null;

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'ConsignmentID',
        'BL',
        'SealNo',
        'ContainerNo',
        'ContainerSize',
        'Weight',
        'ItemDetails',
        'HandlingCost',
        'GateOutDate',
        'ReturnDate',
        'Username',
        'BranchID',
        'Date',
        'Time',
        'Status',
    ];

    protected $casts = [
        'ConsignmentID' => 'integer',
        'Weight' => 'float',
        'HandlingCost' => 'float',
        'Status' => 'integer',
    ];

    public function consignment()
    {
        return $this->belongsTo(ContainerMain::class, 'ConsignmentID', 'ConsignmentID');
    }
}
