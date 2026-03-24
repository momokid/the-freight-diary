<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserAuth;
use Illuminate\Http\Request;

class UserPrivilegeController extends Controller
{
    public function index()
    {
        // Load all users from kaina, ordered by name
        $users = User::orderBy('FullName')->get();

        // ADDED: permission groups for the UI
        // Defines how permissions are grouped and labelled in the view
        $permissionGroups = [
            'Setup & Config' => [
                'BasicConfig' => 'Basic Config',
            ],
            'Consignment' => [
                'ConsignmentRegister'      => 'Consignment Register',
                'AssignConsignmentOfficer' => 'Assign Consignment Officer',
            ],
            'Transactions' => [
                'GenerateInvoice'    => 'Generate Invoice',
                'PaymentTransaction' => 'Payment Transaction',
                'GLTransaction'      => 'GL Transaction',
                'ReverseTransaction' => 'Reverse Transaction',
            ],
            'Disbursement' => [
                'Disbursement'             => 'Disbursement',
                'DisbursementAnalysis'     => 'Disbursement Analysis',
                'DisbursementRevenue'      => 'Disbursement Revenue',
                'DisbursementOtherExpense' => 'Disbursement Other Expense',
                'DisbursementApproval'     => 'Disbursement Approval',
            ],
            'Expenses' => [
                'ConsignmentExpense' => 'Consignment Expense',
                'CashExpenditure'    => 'Cash Expenditure',
                'PettyCash'          => 'Petty Cash',
            ],
            'Transport' => [
                'Transport'        => 'Transport',
                'TransportTrip'    => 'Transport Trip',
                'TransportExpense' => 'Transport Expense',
            ],
            'Reports' => [
                'AccountingReport' => 'Accounting Report',
            ],
            'Dashboard Widgets' => [
                'CnsAwaitingClearance'    => 'Consignments Awaiting Clearance',
                'PendingGateOutDashboard' => 'Pending Gate Out',
                'VehicleHubDashboard'     => 'Vehicle Hub',
            ],
            'Admin' => [
                'UserPrivilege' => 'User Privilege',
                'EditData'      => 'Edit Data',
                'Hashing'       => 'Hashing',
            ],
        ];

        return view('settings.user-privilege', compact('users', 'permissionGroups'));
    }

    //returns a user's permissions as JSON for the AJAX call in the view
    public function show(string $userId)
    {
        $userAuth = UserAuth::where('Username', $userId)->first();

        // No user_auth row found — tell the view to show the initialise prompt
        if (! $userAuth) {
            return response()->json([
                'initialised' => false,
            ]);
        }

        // Build permissions array from the model — only the permission columns
        $permissions = collect(UserAuth::PERMISSIONS)
            ->mapWithKeys(fn($permission) => [$permission => $userAuth->$permission])
            ->toArray();

        return response()->json([
            'initialised' => true,
            'permissions' => $permissions,
        ]);
    }

    //Initialise permissions for a user — creates a new row in user_auth with all permissions set to 0
    public function initialise(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string', 'exists:kaina,ID'],
        ]);

        // Check if user_auth row already exists — prevent duplicates
        $exists = UserAuth::where('Username', $request->username)->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Permissions already initialised for this user.',
            ], 409);
        }

        // Create a new user_auth row with all permissions set to 0
        UserAuth::create(array_merge(
            ['Username' => $request->username],
            array_fill_keys(UserAuth::PERMISSIONS, 0)
        ));

        return response()->json([
            'success' => true,
            'message' => 'Access permissions initialised successfully.',
        ]);
    }

    public function toggle(Request $request)
    {
        $request->validate([
            'username'   => ['required', 'string', 'exists:user_auth,Username'],
            'permission' => ['required', 'string', 'in:' . implode(',', UserAuth::PERMISSIONS)],
        ]);

        $userAuth = UserAuth::where('Username', $request->username)->firstOrFail();

        $permission    = $request->permission;
        $currentValue  = $userAuth->$permission;

        // Flip the value — true becomes false, false becomes true
        $userAuth->$permission = ! $currentValue;
        $userAuth->save();

        return response()->json([
            'success'   => true,
            'permission' => $permission,
            'value'     => $userAuth->$permission,
        ]);
    }
}
