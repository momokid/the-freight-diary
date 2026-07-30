<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_auth', function (Blueprint $table) {
            $table->tinyInteger('AgentAutonomyControl')->default(0)->after('ManagementReport');
            $table->tinyInteger('AgentViewAll')->default(0)->after('AgentAutonomyControl');
        });
    }

    public function down(): void
    {
        Schema::table('user_auth', function (Blueprint $table) {
            $table->dropColumn([
                'AgentAutonomyControl',
                'AgentViewAll',
            ]);
        });
    }
};
