<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('commodity_category', function (Blueprint $table) {
            $table->integer('ID')->autoIncrement()->change();
        });
    }

    public function down(): void
    {
        Schema::table('commodity_category', function (Blueprint $table) {
            $table->integer('ID')->change();
        });
    }
};
