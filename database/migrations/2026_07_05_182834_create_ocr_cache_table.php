<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
        CREATE TABLE IF NOT EXISTS `ocr_cache` (
          `ID` int(11) NOT NULL AUTO_INCREMENT,
          `FileHash` varchar(64) NOT NULL,
          `Result` longtext NOT NULL,
          `Provider` varchar(20) NOT NULL,
          `HitCount` int(11) NOT NULL DEFAULT 1,
          `CreatedAt` datetime NOT NULL,
          `ExpiresAt` datetime NOT NULL,
          PRIMARY KEY (`ID`),
          UNIQUE KEY `ocr_cache_filehash_unique` (`FileHash`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    }

    public function down(): void
    {
        Schema::dropIfExists('ocr_cache');
    }
};
