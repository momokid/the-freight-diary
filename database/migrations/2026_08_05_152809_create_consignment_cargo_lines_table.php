<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
        CREATE TABLE IF NOT EXISTS `consignment_cargo_lines` (
          `ID` int(11) NOT NULL AUTO_INCREMENT,
          `ConsignmentID` int(11) NOT NULL,
          `BL` varchar(50) NOT NULL,
          `ContainerNo` varchar(50) DEFAULT NULL,
          `LineNo` int(11) NOT NULL DEFAULT 1,
          `VIN` varchar(30) DEFAULT NULL,
          `Description` varchar(255) DEFAULT NULL,
          `Make` varchar(50) DEFAULT NULL,
          `Model` varchar(80) DEFAULT NULL,
          `Year` varchar(4) DEFAULT NULL,
          `Weight` decimal(12,3) DEFAULT NULL,
          `ItemTypeGuess` varchar(15) DEFAULT NULL,
          `Confidence` decimal(5,4) DEFAULT NULL,
          `Source` varchar(10) NOT NULL DEFAULT 'ocr',
          `UsedInManifest` tinyint(1) NOT NULL DEFAULT 0,
          `Username` varchar(20) NOT NULL,
          `CreatedAt` datetime NOT NULL,
          `Status` int(11) NOT NULL DEFAULT 1,
          PRIMARY KEY (`ID`),
          KEY `cargo_lines_consignment_idx` (`ConsignmentID`,`BL`,`Status`),
          KEY `cargo_lines_vin_idx` (`VIN`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    }

    public function down(): void
    {
        Schema::dropIfExists('consignment_cargo_lines');
    }
};