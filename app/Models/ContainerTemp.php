<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContainerTemp extends Model
{
    protected $table      = 'new_container_temp';
    protected $primaryKey = null;
    public $incrementing  = false;
    public $timestamps    = false;

    protected $fillable = [
        'BOL',
        'SealNo',
        'ContainerNo',
        'ContainerSize',
        'Weight',
        'HandlingCost',
        'Username',
        'Date',
        'Time',
    ];

    protected $casts = [
        'Weight'       => 'float',
        'HandlingCost' => 'float',
    ];
}