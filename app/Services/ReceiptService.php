<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReceiptService
{

    public static function generate(string $date): array
    {
        $user     = Auth::user();
        $initial  = $user->Initial;
        $refDate  = str_replace('-', '', $date); // 2024-03-15 → 20240315

        // Check if this user has any receipts on this date
        $maxId = DB::table('receipt_main')
            ->where('Username', $user->ID)
            ->where('Date', $date)
            ->max('ID');

        if ($maxId === null) {
            // First receipt of the day for this user
            $id       = 1;
        } else {
            $id       = $maxId + 1;
        }

        $receiptNo = $initial . $refDate . $id;

        return [
            'id'         => $id,
            'receipt_no' => $receiptNo,
            'date'       => $date,
        ];
    }
}
