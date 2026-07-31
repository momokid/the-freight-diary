<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE `agent_runs`
            ADD COLUMN IF NOT EXISTS `BagJson` longtext DEFAULT NULL AFTER `PlanJson`
        ");
    }

    public function down(): void
    {
        Schema::table('agent_runs', function ($table) {
            $table->dropColumn('BagJson');
        });
    }
};
