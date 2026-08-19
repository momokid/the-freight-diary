<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('system_settings')->updateOrInsert(
            ['key' => 'stall_days_to_type'],
            [
                'value'      => '0',
                'label'      => 'Days After Arrival Before Unconfirmed Type Is Flagged',
                'group'      => 'stall_monitor',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        DB::table('system_settings')->where('key', 'stall_days_to_type')->delete();
    }
};
