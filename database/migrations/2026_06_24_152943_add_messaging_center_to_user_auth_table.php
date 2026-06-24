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
        Schema::table('user_auth', function (Blueprint $table) {
            $table->integer('MessagingCenter')->default(0)->after('Hashing');
        });
    }

    public function down(): void
    {
        Schema::table('user_auth', function (Blueprint $table) {
            $table->dropColumn('MessagingCenter');
        });
    }
};
