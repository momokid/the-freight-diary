<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('new_comtainer_cmdts_temp', function (Blueprint $table) {
            // ADDED: SealNo — optional for FCL consignments
            $table->string('SealNo', 50)->nullable()->after('ContainerNo');
        });
    }

    public function down(): void
    {
        Schema::table('new_comtainer_cmdts_temp', function (Blueprint $table) {
            $table->dropColumn('SealNo');
        });
    }
};
