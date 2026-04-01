<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Consignee extends Model
{
    protected $table      = 'consignee_main';
    protected $primaryKey = 'ConsigneeID';
    public $incrementing  = true;
    protected $keyType    = 'int';

    public $timestamps = false;

    protected $fillable = [
        'FullName',
        'TelNo',
        'Address1',
        'Address2',
        'Address3',
        'Date',
        'Time',
        'Username',
        'Status',
    ];

    protected $casts = [
        'ConsigneeID' => 'integer',
        'Status'      => 'integer',
    ];

    // ── Scopes ──

    public function scopeActive($query)
    {
        return $query->where('Status', 1);
    }

    public function scopeInactive($query)
    {
        return $query->where('Status', 0);
    }

    // ── Relationships ──

    public function creator()
    {
        return $this->belongsTo(User::class, 'Username', 'ID');
    }
}
