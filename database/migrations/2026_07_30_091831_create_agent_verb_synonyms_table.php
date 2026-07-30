<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
        CREATE TABLE IF NOT EXISTS `agent_verb_synonyms` (
          `ID` int(11) NOT NULL AUTO_INCREMENT,
          `Verb` varchar(40) NOT NULL,
          `CanonicalVerb` varchar(40) NOT NULL,
          `IntentKey` varchar(60) DEFAULT NULL,
          `Weight` decimal(4,2) NOT NULL DEFAULT 1.00,
          `IsSystem` tinyint(1) NOT NULL DEFAULT 0,
          `HitCount` int(11) NOT NULL DEFAULT 0,
          `Username` varchar(20) DEFAULT NULL,
          `CreatedAt` datetime NOT NULL,
          `Status` int(11) NOT NULL DEFAULT 1,
          PRIMARY KEY (`ID`),
          UNIQUE KEY `agent_verb_synonyms_verb_unique` (`Verb`),
          KEY `agent_verb_synonyms_intent_idx` (`IntentKey`),
          KEY `agent_verb_synonyms_canonical_idx` (`CanonicalVerb`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_verb_synonyms');
    }
};
