<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * CmdtTypeID = 1 only suggests LCL — roughly one in five is actually FCL.
 * Guessing at read time told users manifest work was outstanding when it
 * was not. IsLCL records what a human confirmed.
 *
 * NULL means nobody has said yet, and is deliberately left in place on the
 * backfill rather than guessed. Breakdown rows are the one proof we have.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE `container_main`
            ADD COLUMN IF NOT EXISTS `IsLCL` tinyint(1) DEFAULT NULL AFTER `CmdtTypeID`
        ");

        // Breakdown rows exist — certain LCL, whatever CmdtTypeID says
        DB::statement("
            UPDATE `container_main` cm
            SET cm.`IsLCL` = 1
            WHERE EXISTS (
                SELECT 1 FROM `manifestation_breakdown` mb
                WHERE mb.`ConsignmentID` = cm.`ConsignmentID`
                  AND mb.`MainBL` = cm.`BL`
                  AND mb.`Status` = 1
            )
        ");

        // Not flagged as LCL commodity and never broken down — treat as FCL
        DB::statement("
            UPDATE `container_main`
            SET `IsLCL` = 0
            WHERE `IsLCL` IS NULL
              AND `CmdtTypeID` <> 1
        ");
    }

    public function down(): void
    {
        Schema::table('container_main', function ($table) {
            $table->dropColumn('IsLCL');
        });
    }
};
