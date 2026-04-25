<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_auth', function (Blueprint $table) {
            $table->tinyInteger('EditDisbursementAnalysis')->default(0)->after('ReverseConsignment');
        });
    }

    public function down(): void
    {
        Schema::table('user_auth', function (Blueprint $table) {
            $table->dropColumn('EditDisbursementAnalysis');
        });
    }
};
