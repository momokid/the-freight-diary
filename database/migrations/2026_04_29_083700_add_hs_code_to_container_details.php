<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('container_details', function (Blueprint $table) {
            $table->string('HSCode', 10)->nullable()->after('ItemDetails');
            $table->string('HSDescription', 255)->nullable()->after('HSCode');
            $table->decimal('EstimatedDutyRate', 5, 2)->nullable()->after('HSDescription');
            $table->string('HSRecommendedBy', 20)->nullable()->after('EstimatedDutyRate');
        });
    }

    public function down(): void
    {
        Schema::table('container_details', function (Blueprint $table) {
            $table->dropColumn(['HSCode', 'HSDescription', 'EstimatedDutyRate', 'HSRecommendedBy']);
        });
    }
};
