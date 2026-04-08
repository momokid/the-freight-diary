<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CompanyService
{
    /**
     * Get company info for the current user's branch.
     * Used in all reports and shared across all views via AppServiceProvider.
     */
    public static function get(): ?object
    {
        if (!Auth::check()) return null;

        return DB::table('inst_reg as i')
            ->join('inst_branch as b', 'i.InstID', '=', 'b.InstID')
            ->where('b.BranchID', Auth::user()->BranchID)
            ->select(
                'i.InstName',
                'i.Email',
                'i.TelNo as InstTelNo',
                'i.Website',
                'b.BranchName',
                'b.Address',
                'b.TelNo',
                'b.Location'
            )
            ->first();
    }
}