<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxComponent extends Model
{
    protected $table   = 'tax_components';
    protected $fillable = [
        'name',
        'label',
        'rate',
        'applies_on',
        'sort_order',
        'is_active',
        'effective_date',
        'Username',
    ];

    protected $casts = [
        'rate'           => 'decimal:2',
        'is_active'      => 'integer',
        'sort_order'     => 'integer',
        'effective_date' => 'date',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
