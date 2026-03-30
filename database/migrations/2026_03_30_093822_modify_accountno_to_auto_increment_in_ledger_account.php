<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ledger_account', function (Blueprint $table) {
            // CHANGED: AccountNo is now auto increment
            $table->integer('AccountNo')->autoIncrement()->change();
        });
    }

    public function down(): void
    {
        Schema::table('ledger_account', function (Blueprint $table) {
            $table->integer('AccountNo')->change();
        });
    }
};