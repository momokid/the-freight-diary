<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'kaina';        // ✅ correct table

    protected $primaryKey = 'ID';      // ✅ username is primary key

    public $incrementing = false;      // ✅ not auto increment

    protected $keyType = 'string';     // ✅ ID is varchar
    public $timestamps = false;

    protected $casts = [
        'reset_requested'      => 'boolean',
        'must_change_password' => 'boolean',
    ];

    protected $fillable = [
        'ID',
        'FullName',
        'HashPassword',
        'Nature',
        'Stats',
        'BranchID',
        'remember_token',
        'reset_requested',      // ADDED: flags when user has requested a password reset
        'must_change_password',
    ];

    protected $hidden = [
        'HashPassword',
        'remember_token',
    ];

    // Tell Laravel which column is the password
    public function getAuthPassword()
    {
        return $this->HashPassword;
    }

    public function getAuthPasswordName()
    {
        return 'HashPassword';
    }

    public function setPasswordAttribute($password)
    {
        // Prevent Laravel from auto-writing to `password` column
    }
}
