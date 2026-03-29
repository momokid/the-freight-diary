<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ledger_control', function (Blueprint $table) {
            // CHANGED: ControlID is now auto increment
            $table->integer('ControlID')->autoIncrement()->change();
        });
    }

    public function down(): void
    {
        Schema::table('ledger_control', function (Blueprint $table) {
            // Revert back to regular integer if rolled back
            $table->integer('ControlID')->change();
        });
    }
};
