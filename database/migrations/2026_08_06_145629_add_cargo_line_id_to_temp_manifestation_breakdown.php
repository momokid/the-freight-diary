<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE `temp_manifestation_breakdown`
            ADD COLUMN IF NOT EXISTS `CargoLineID` int(11) DEFAULT NULL
        ");
    }

    public function down(): void
    {
        Schema::table('temp_manifestation_breakdown', function ($table) {
            $table->dropColumn('CargoLineID');
        });
    }
};
