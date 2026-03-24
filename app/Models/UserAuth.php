<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAuth extends Model
{

    // ADDED: single source of truth for all permission columns
    // To add a new permission in future, only add it here
    const PERMISSIONS = [
        'BasicConfig',
        'ConsignmentRegister',
        'GenerateInvoice',
        'PaymentTransaction',
        'GLTransaction',
        'Disbursement',
        'ConsignmentExpense',
        'Transport',
        'EditData',
        'AssignConsignmentOfficer',
        'DisbursementAnalysis',
        'DisbursementRevenue',
        'DisbursementOtherExpense',
        'DisbursementApproval',
        'ReverseTransaction',
        'AccountingReport',
        'CashExpenditure',
        'TransportTrip',
        'TransportExpense',
        'PettyCash',
        'CnsAwaitingClearance',
        'PendingGateOutDashboard',
        'VehicleHubDashboard',
        'UserPrivilege',
        'Hashing',
    ];

    protected $table = 'user_auth';

    protected $primaryKey = 'Username';
    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'Username',
        ...self::PERMISSIONS
    ];

    // Cast all permission columns to boolean so we get true/false instead of 1/0
    protected $casts = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // Build casts dynamically from PERMISSIONS constant
        $this->casts = array_fill_keys(self::PERMISSIONS, 'boolean');
    }

    // Relationship back to the user in kaina
    public function user()
    {
        return $this->belongsTo(User::class, 'Username', 'ID');
    }

    // Check a specific permission by name
    // Usage: $userAuth->can('BasicConfig')
    public function hasPermission(string $permission): bool
    {
        return isset($this->$permission) && $this->$permission === true;
    }
}
