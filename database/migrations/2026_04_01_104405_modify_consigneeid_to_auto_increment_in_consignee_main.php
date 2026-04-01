<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consignee_main', function (Blueprint $table) {
            // CHANGED: ConsigneeID is now auto increment
            $table->integer('ConsigneeID')->autoIncrement()->change();
        });
    }

    public function down(): void
    {
        Schema::table('consignee_main', function (Blueprint $table) {
            $table->integer('ConsigneeID')->change();
        });
    }
};
