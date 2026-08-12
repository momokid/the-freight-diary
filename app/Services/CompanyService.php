<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CompanyService
{
    /**
     * Company info for the logged-in user's branch.
     * Shared across all views via AppServiceProvider.
     */
    public static function get(): ?object
    {
        if (!Auth::check()) return null;

        return self::forBranch(Auth::user()->BranchID) ?? self::institution();
    }

    /**
     * Company info for a named branch, with no logged-in user.
     * Scheduled commands and queued mail run with no auth context, so they
     * must pass the branch themselves.
     */
    public static function forBranch(?string $branchId): ?object
    {
        if (empty($branchId)) return null;

        return DB::table('inst_reg as i')
            ->join('inst_branch as b', 'i.InstID', '=', 'b.InstID')
            ->where('b.BranchID', $branchId)
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

    /**
     * Institution details with no branch context. For scheduled commands and
     * queued mail, which run with no logged-in user and are not branch-scoped.
     */
    public static function institution(): ?object
    {
        return DB::table('inst_reg')
            ->select(
                'InstName',
                'Email',
                'TelNo as InstTelNo',
                'TelNo',
                'Website',
                DB::raw('NULL as BranchName'),
                DB::raw('NULL as Address'),
                DB::raw('NULL as Location')
            )
            ->first();
    }
}
