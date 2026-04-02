<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ship_carrier', function (Blueprint $table) {
            $table->integer('Status')->default(1)->after('Username');
        });
    }

    public function down(): void
    {
        Schema::table('ship_carrier', function (Blueprint $table) {
            $table->dropColumn('Status');
        });
    }
};
