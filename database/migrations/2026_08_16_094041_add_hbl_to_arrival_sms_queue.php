<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('arrival_sms_queue', function (Blueprint $table) {
            // Empty for FCL, the house BL for LCL. An LCL arrival is one
            // message per house BL, so BL alone can no longer be unique.
            $table->string('HBL', 50)->default('')->after('BL');

            $table->dropUnique('arrival_queue_dedup');
            $table->unique(['BL', 'HBL', 'QueueDate'], 'arrival_queue_dedup');
        });
    }

    public function down(): void
    {
        Schema::table('arrival_sms_queue', function (Blueprint $table) {
            $table->dropUnique('arrival_queue_dedup');
            $table->unique(['BL', 'QueueDate'], 'arrival_queue_dedup');
            $table->dropColumn('HBL');
        });
    }
};
