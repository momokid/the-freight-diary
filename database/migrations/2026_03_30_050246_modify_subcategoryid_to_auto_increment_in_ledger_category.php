<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ledger_category', function (Blueprint $table) {
            // SubCategoryID is now auto increment
            $table->integer('SubCategoryID')->autoIncrement()->change();
        });
    }

    public function down(): void
    {
        Schema::table('ledger_category', function (Blueprint $table) {
            $table->integer('SubCategoryID')->change();
        });
    }
};
