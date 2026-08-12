<?php

namespace App\Services;

use App\Models\UserAuth;

/**
 * Who may see which disbursement rows.
 *
 * disbursement_analysis.Restricted: 0 visible to all, 1 needs
 * DisbursementOtherExpense, 2 needs DisbursementRevenue.
 *
 * One definition because the reports and the agent must agree. If the agent
 * were more permissive, asking it would be a way around the report.
 */
class DisbursementVisibility
{
    /** @return int[] Restricted values this user may see. */
    public function allowedFor(?UserAuth $userAuth): array
    {
        $allowed = [0];

        if ($userAuth?->hasPermission('DisbursementOtherExpense')) {
            $allowed[] = 1;
        }

        if ($userAuth?->hasPermission('DisbursementRevenue')) {
            $allowed[] = 2;
        }

        return $allowed;
    }

    /** @return int[] */
    public function allowedForUsername(?string $username): array
    {
        if ($username === null) {
            return [0];
        }

        return $this->allowedFor(UserAuth::where('Username', $username)->first());
    }
}
