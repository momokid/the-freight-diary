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
        Schema::table('journal', function (Blueprint $table) {
            $table->tinyInteger('Reversed')->default(0)->after('InReport');
        });
    }

    public function down(): void
    {
        Schema::table('journal', function (Blueprint $table) {
            $table->dropColumn('Reversed');
        });
    }
};
