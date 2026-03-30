<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LedgerCategory extends Model
{
    protected $table      = 'ledger_category';
    protected $primaryKey = 'SubCategoryID';
    public $incrementing  = true;
    protected $keyType    = 'int';

    // No created_at or updated_at
    public $timestamps = false;

    protected $fillable = [
        'CategoryID',
        'CategoryName',
        'SubCategoryName',
        'Class',
        'Type',
        'Username',
        'Date',
        'Time',
        'Status',
    ];

    protected $casts = [
        'CategoryID'    => 'integer',
        'SubCategoryID' => 'integer',
        'Status'        => 'integer',
    ];

    //

    public function scopeActive($query)
    {
        return $query->where('Status', 1);
    }

    public function scopeInactive($query)
    {
        return $query->where('Status', 0);
    }

    // Get distinct categories from existing data
    // Returns CategoryID, CategoryName, Class, Type
    public static function getDistinctCategories()
    {
        return self::select('CategoryID', 'CategoryName', 'Class', 'Type')
            ->distinct()
            ->orderBy('CategoryID')
            ->get();
    }

    // Get next CategoryID — increments by 10 to maintain spacing pattern
    public static function getNextCategoryID()
    {
        $max = self::max('CategoryID') ?? 0;
        return $max + 10;
    }

    // Relationships

    public function creator()
    {
        return $this->belongsTo(User::class, 'Username', 'ID');
    }
}
