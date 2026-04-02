<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommodityType extends Model
{
    protected $table      = 'commodity_type';
    protected $primaryKey = 'TypeID';
    public $incrementing  = true;
    protected $keyType    = 'int';
    public $timestamps    = false;

    protected $fillable = [
        'CategoryID',
        'TypeName',
    ];

    protected $casts = [
        'TypeID'     => 'integer',
        'CategoryID' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(CommodityCategory::class, 'CategoryID', 'ID');
    }
}
