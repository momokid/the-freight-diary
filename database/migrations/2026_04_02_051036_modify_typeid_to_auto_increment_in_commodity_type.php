<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('commodity_type', function (Blueprint $table) {
            $table->integer('TypeID')->autoIncrement()->change();
        });
    }

    public function down(): void
    {
        Schema::table('commodity_type', function (Blueprint $table) {
            $table->integer('TypeID')->change();
        });
    }
};
