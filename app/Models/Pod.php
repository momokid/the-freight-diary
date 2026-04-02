<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pod extends Model
{
    protected $table      = 'pod';
    protected $primaryKey = 'POD_ID';
    public $incrementing  = true;
    protected $keyType    = 'int';
    public $timestamps    = false;

    protected $fillable = [
        'POD_Name',
        'Time',
        'Username',
    ];

    protected $casts = [
        'POD_ID' => 'integer',
    ];
}
