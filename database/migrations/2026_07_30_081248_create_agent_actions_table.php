<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
        CREATE TABLE IF NOT EXISTS `agent_actions` (
          `ID` int(11) NOT NULL AUTO_INCREMENT,
          `RunID` int(11) NOT NULL,
          `StepOrder` int(11) NOT NULL,
          `StepKey` varchar(60) NOT NULL,
          `StepLabel` varchar(120) DEFAULT NULL,
          `RequiredPermission` varchar(40) DEFAULT NULL,
          `IsWrite` tinyint(1) NOT NULL DEFAULT 0,
          `ApprovalRequired` tinyint(1) NOT NULL DEFAULT 1,
          `Username` varchar(20) NOT NULL,
          `ApprovedBy` varchar(20) DEFAULT NULL,
          `ApprovedAt` datetime DEFAULT NULL,
          `InputJson` longtext DEFAULT NULL,
          `OutputJson` longtext DEFAULT NULL,
          `ActionStatus` varchar(20) NOT NULL DEFAULT 'pending',
          `FailureReason` text DEFAULT NULL,
          `TargetTable` varchar(60) DEFAULT NULL,
          `TargetKey` varchar(120) DEFAULT NULL,
          `DurationMs` int(11) DEFAULT NULL,
          `StartedAt` datetime DEFAULT NULL,
          `CompletedAt` datetime DEFAULT NULL,
          PRIMARY KEY (`ID`),
          KEY `agent_actions_run_idx` (`RunID`,`StepOrder`),
          KEY `agent_actions_step_idx` (`StepKey`),
          KEY `agent_actions_user_idx` (`Username`),
          KEY `agent_actions_target_idx` (`TargetTable`,`TargetKey`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_actions');
    }
};
