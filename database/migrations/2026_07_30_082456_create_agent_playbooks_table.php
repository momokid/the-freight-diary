<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
        CREATE TABLE IF NOT EXISTS `agent_playbooks` (
          `ID` int(11) NOT NULL AUTO_INCREMENT,
          `PlaybookKey` varchar(60) NOT NULL,
          `TaskType` varchar(40) NOT NULL,
          `Title` varchar(120) NOT NULL,
          `Description` varchar(255) DEFAULT NULL,
          `IntentExamples` text DEFAULT NULL,
          `StepsJson` longtext NOT NULL,
          `Autonomy` varchar(12) NOT NULL DEFAULT 'fill_stop',
          `IsSystem` tinyint(1) NOT NULL DEFAULT 0,
          `Version` int(11) NOT NULL DEFAULT 1,
          `Username` varchar(20) NOT NULL,
          `BranchID` varchar(10) NOT NULL,
          `CreatedAt` datetime NOT NULL,
          `UpdatedAt` datetime DEFAULT NULL,
          `Status` int(11) NOT NULL DEFAULT 1,
          PRIMARY KEY (`ID`),
          UNIQUE KEY `agent_playbooks_key_unique` (`PlaybookKey`),
          KEY `agent_playbooks_task_idx` (`TaskType`),
          KEY `agent_playbooks_status_idx` (`Status`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_playbooks');
    }
};
