<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Payment details printed on client-facing documents.
 *
 * Returns null when nothing is active. Callers must render nothing rather
 * than empty labels — a blank account number on an invoice is worse than
 * no payment instruction at all.
 */
class BankDetailsService
{
    public static function active(): ?object
    {
        $rows = DB::table('bank_details')
            ->where('is_active', 1)
            ->orderByDesc('updated_at')
            ->limit(2)
            ->get();

        if ($rows->count() > 1) {
            Log::warning('[BankDetails] More than one active row; using the most recent.');
        }

        return $rows->first();
    }
}
