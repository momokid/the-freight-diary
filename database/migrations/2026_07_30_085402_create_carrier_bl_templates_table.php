<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
        CREATE TABLE IF NOT EXISTS `carrier_bl_templates` (
          `ID` int(11) NOT NULL AUTO_INCREMENT,
          `CarrierID` int(11) NOT NULL,
          `TemplateName` varchar(60) DEFAULT NULL,
          `BLPrefix` varchar(10) DEFAULT NULL,
          `FieldHintsJson` longtext NOT NULL,
          `PromptSupplement` text DEFAULT NULL,
          `SampleHash` varchar(64) DEFAULT NULL,
          `SuccessCount` int(11) NOT NULL DEFAULT 0,
          `FailCount` int(11) NOT NULL DEFAULT 0,
          `LastUsedAt` datetime DEFAULT NULL,
          `Username` varchar(20) NOT NULL,
          `CreatedAt` datetime NOT NULL,
          `UpdatedAt` datetime DEFAULT NULL,
          `Status` int(11) NOT NULL DEFAULT 1,
          PRIMARY KEY (`ID`),
          KEY `carrier_bl_templates_carrier_idx` (`CarrierID`),
          KEY `carrier_bl_templates_prefix_idx` (`BLPrefix`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    }

    public function down(): void
    {
        Schema::dropIfExists('carrier_bl_templates');
    }
};
