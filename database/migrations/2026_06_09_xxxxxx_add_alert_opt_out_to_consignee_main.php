<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consignee_main', function (Blueprint $table) {
            $table->tinyInteger('AlertOptOut')->default(0)->after('Status');
            // 0 = receive alerts (default), 1 = opted out
        });
    }

    public function down(): void
    {
        Schema::table('consignee_main', function (Blueprint $table) {
            $table->dropColumn('AlertOptOut');
        });
    }
};