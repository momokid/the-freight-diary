<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_auth', function (Blueprint $table) {
            $table->tinyInteger('ReportVisibility')->default(0)->after('Hashing')
                ->comment('0=restricted mode, 1=full visibility override for all users');
        });
    }

    public function down(): void
    {
        Schema::table('user_auth', function (Blueprint $table) {
            $table->dropColumn('ReportVisibility');
        });
    }
};
