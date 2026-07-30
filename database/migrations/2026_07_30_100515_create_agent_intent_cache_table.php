<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
        CREATE TABLE IF NOT EXISTS `agent_intent_cache` (
          `ID` int(11) NOT NULL AUTO_INCREMENT,
          `Fingerprint` varchar(64) NOT NULL,
          `NormalisedPattern` varchar(255) NOT NULL,
          `IntentKey` varchar(60) NOT NULL,
          `PlaybookID` int(11) DEFAULT NULL,
          `CanonicalVerb` varchar(40) DEFAULT NULL,
          `EmbeddingBlob` blob DEFAULT NULL,
          `EmbeddingModel` varchar(60) DEFAULT NULL,
          `EmbeddingDims` smallint(6) DEFAULT NULL,
          `ResolvedLayer` tinyint(4) NOT NULL,
          `Confidence` decimal(5,4) DEFAULT NULL,
          `HitCount` int(11) NOT NULL DEFAULT 1,
          `MissCount` int(11) NOT NULL DEFAULT 0,
          `LastUsedAt` datetime DEFAULT NULL,
          `CreatedAt` datetime NOT NULL,
          `Status` int(11) NOT NULL DEFAULT 1,
          PRIMARY KEY (`ID`),
          UNIQUE KEY `agent_intent_cache_fp_unique` (`Fingerprint`),
          KEY `agent_intent_cache_intent_idx` (`IntentKey`),
          KEY `agent_intent_cache_verb_idx` (`CanonicalVerb`,`Status`),
          KEY `agent_intent_cache_layer_idx` (`ResolvedLayer`),
          KEY `agent_intent_cache_used_idx` (`LastUsedAt`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_intent_cache');
    }
};
