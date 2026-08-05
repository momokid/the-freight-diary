<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE `consignment_cargo_lines`
            MODIFY COLUMN `ConsignmentID` int(11) NOT NULL DEFAULT 0,
            ADD COLUMN IF NOT EXISTS `IsStaged` tinyint(1) NOT NULL DEFAULT 1 AFTER `Source`
        ");

        DB::statement("
            ALTER TABLE `consignment_cargo_lines`
            ADD INDEX IF NOT EXISTS `cargo_lines_staged_idx` (`Username`,`BL`,`IsStaged`)
        ");
    }

    public function down(): void
    {
        Schema::table('consignment_cargo_lines', function ($table) {
            $table->dropColumn('IsStaged');
        });
    }
};
