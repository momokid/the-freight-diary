<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pol extends Model
{
    protected $table      = 'pol';
    protected $primaryKey = 'POL_ID';
    public $incrementing  = true;
    protected $keyType    = 'int';
    public $timestamps    = false;

    protected $fillable = [
        'POL_Name',
        'Time',
        'Username',
    ];

    protected $casts = [
        'POL_ID' => 'integer',
    ];
}
