<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommodityCategory extends Model
{
    protected $table      = 'commodity_category';
    protected $primaryKey = 'ID';
    public $incrementing  = true;
    protected $keyType    = 'int';
    public $timestamps    = false;

    protected $fillable = [
        'CategoryName',
    ];

    protected $casts = [
        'ID' => 'integer',
    ];

    public function types()
    {
        return $this->hasMany(CommodityType::class, 'CategoryID', 'ID');
    }
}
