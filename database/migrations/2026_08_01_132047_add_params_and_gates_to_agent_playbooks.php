<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE `agent_playbooks`
            ADD COLUMN IF NOT EXISTS `ParamsJson` longtext DEFAULT NULL AFTER `StepsJson`,
            ADD COLUMN IF NOT EXISTS `GatesJson` longtext DEFAULT NULL AFTER `ParamsJson`
        ");
    }

    public function down(): void
    {
        Schema::table('agent_playbooks', function ($table) {
            $table->dropColumn(['ParamsJson', 'GatesJson']);
        });
    }
};
