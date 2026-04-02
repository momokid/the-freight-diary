<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pod', function (Blueprint $table) {
            $table->integer('POD_ID')->autoIncrement()->change();
        });
    }

    public function down(): void
    {
        Schema::table('pod', function (Blueprint $table) {
            $table->integer('POD_ID')->change();
        });
    }
};
