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
        ALTER TABLE `user_auth`
        ADD COLUMN IF NOT EXISTS `ErrorLogTicket` tinyint(1) NOT NULL DEFAULT 0 AFTER `SendArrivalSms`;
    ");
    }

    public function down(): void
    {
        DB::statement("
        ALTER TABLE `user_auth`
        DROP COLUMN IF EXISTS `ErrorLogTicket`;
    ");
    }
};
