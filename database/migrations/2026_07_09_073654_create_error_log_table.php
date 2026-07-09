<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
        CREATE TABLE IF NOT EXISTS `error_log` (
          `ID` int(11) NOT NULL AUTO_INCREMENT,
          `Signature` varchar(64) NOT NULL,
          `ExceptionClass` varchar(255) NOT NULL,
          `Message` text NOT NULL,
          `File` varchar(500) NOT NULL,
          `Line` int(11) NOT NULL,
          `Trace` longtext NOT NULL,
          `Route` varchar(255) DEFAULT NULL,
          `Username` varchar(20) DEFAULT NULL,
          `Status` enum('new','acknowledged','resolved') NOT NULL DEFAULT 'new',
          `OccurrenceCount` int(11) NOT NULL DEFAULT 1,
          `FirstSeenAt` datetime NOT NULL,
          `LastSeenAt` datetime NOT NULL,
          PRIMARY KEY (`ID`),
          UNIQUE KEY `error_log_signature_unique` (`Signature`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    }

    public function down(): void
    {
        Schema::dropIfExists('error_log');
    }
};
