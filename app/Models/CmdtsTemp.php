<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CmdtsTemp extends Model
{
    protected $table      = 'new_comtainer_cmdts_temp';
    protected $primaryKey = null;
    public $incrementing  = false;
    public $timestamps    = false;

    protected $fillable = [
        'BL',
        'ContainerNo',
        'SealNo',
        'Size',
        'ItemDetails',
        'Username',
        'createdAt',
    ];
}
