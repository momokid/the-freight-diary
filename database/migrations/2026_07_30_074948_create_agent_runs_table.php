<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
        CREATE TABLE IF NOT EXISTS `agent_runs` (
          `ID` int(11) NOT NULL AUTO_INCREMENT,
          `PlaybookID` int(11) DEFAULT NULL,
          `TaskType` varchar(40) DEFAULT NULL,
          `TaskLabel` varchar(120) DEFAULT NULL,
          `IntentKey` varchar(60) DEFAULT NULL,
          `RawInstruction` text NOT NULL,
          `NormalisedInstruction` text DEFAULT NULL,
          `InputModality` varchar(10) NOT NULL DEFAULT 'text',
          `ResolutionLayer` tinyint(4) DEFAULT NULL,
          `LLMProvider` varchar(20) DEFAULT NULL,
          `LLMModel` varchar(60) DEFAULT NULL,
          `PromptTokens` int(11) DEFAULT NULL,
          `CompletionTokens` int(11) DEFAULT NULL,
          `Autonomy` varchar(12) NOT NULL DEFAULT 'fill_stop',
          `RunStatus` varchar(20) NOT NULL DEFAULT 'interpreting',
          `PlanJson` longtext DEFAULT NULL,
          `FailureReason` text DEFAULT NULL,
          `Username` varchar(20) NOT NULL,
          `BranchID` varchar(10) NOT NULL,
          `StartedAt` datetime NOT NULL,
          `CompletedAt` datetime DEFAULT NULL,
          `Status` int(11) NOT NULL DEFAULT 1,
          PRIMARY KEY (`ID`),
          KEY `agent_runs_user_idx` (`Username`,`StartedAt`),
          KEY `agent_runs_status_idx` (`RunStatus`),
          KEY `agent_runs_task_idx` (`TaskType`),
          KEY `agent_runs_playbook_idx` (`PlaybookID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_runs');
    }
};
