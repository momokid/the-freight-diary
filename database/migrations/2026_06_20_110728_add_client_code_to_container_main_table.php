<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('container_main', function (Blueprint $table) {
            // 4-digit client access code sent via SMS on consignment registration
            $table->char('ClientCode', 4)->nullable()->after('Ownership');
        });
    }

    public function down(): void
    {
        Schema::table('container_main', function (Blueprint $table) {
            $table->dropColumn('ClientCode');
        });
    }
};
