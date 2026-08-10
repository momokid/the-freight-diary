<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContainerMain extends Model
{
    protected $table = 'container_main';

    protected $primaryKey = 'ConsignmentID';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'CarrierID',
        'Rotation',
        'ShipperID',
        'VesselName',
        'VoyageNo',
        'SealNo',
        'ETA',
        'BL',
        'ContainerNo',
        'ContainerSize',
        'ReceiptNo',
        'POIS',
        'DOIS',
        'SOB',
        'POL_ID',
        'POD_ID',
        'ContWeight',
        'Charges',
        'AgentContact',
        'Destination',
        'Username',
        'BranchID',
        'Date',
        'Time',
        'Status',
        'CmdtTypeID',
        'IsLCL',
        'ConsigneeID',
        'ReleaseType',
        'Ownership',
    ];

    protected $casts = [
        'ConsignmentID' => 'integer',
        'CarrierID' => 'integer',
        'ShipperID' => 'integer',
        'POL_ID' => 'integer',
        'POD_ID' => 'integer',
        'ContWeight' => 'float',
        'Charges' => 'float',
        'CmdtTypeID' => 'integer',
        'IsLCL' => 'boolean',
        'ConsigneeID' => 'integer',
        'Status' => 'integer',
    ];

    public function carrier()
    {
        return $this->belongsTo(Carrier::class, 'CarrierID', 'CarrierID');
    }

    public function shipper()
    {
        return $this->belongsTo(Shipper::class, 'ShipperID', 'ShipperID');
    }

    public function pol()
    {
        return $this->belongsTo(Pol::class, 'POL_ID', 'POL_ID');
    }

    public function pod()
    {
        return $this->belongsTo(Pod::class, 'POD_ID', 'POD_ID');
    }

    // ContainerDetails imported — used in containers() relationship
    public function containers()
    {
        return $this->hasMany(ContainerDetails::class, 'ConsignmentID', 'ConsignmentID');
    }

    public function isLCL(): bool
    {
        $type = app(\App\Services\ConsignmentService::class)
            ->resolveType($this->ConsignmentID, $this->BL);

        return in_array($type, [
            \App\Services\ConsignmentService::TYPE_LCL,
            \App\Services\ConsignmentService::TYPE_LCL_PENDING,
            \App\Services\ConsignmentService::TYPE_UNCONFIRMED_LCL,
        ], true);
    }
}
