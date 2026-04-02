<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipper_main', function (Blueprint $table) {
            $table->integer('ShipperID')->autoIncrement()->change();
        });
    }

    public function down(): void
    {
        Schema::table('shipper_main', function (Blueprint $table) {
            $table->integer('ShipperID')->change();
        });
    }
};
