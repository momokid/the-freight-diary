<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_auth', function (Blueprint $table) {
            $table->tinyInteger('OperationsReport')->default(0)->after('AccountingReport');
            $table->tinyInteger('ClientReport')->default(0)->after('OperationsReport');
            $table->tinyInteger('DisbursementReport')->default(0)->after('ClientReport');
            $table->tinyInteger('ManagementReport')->default(0)->after('DisbursementReport');
        });
    }

    public function down(): void
    {
        Schema::table('user_auth', function (Blueprint $table) {
            $table->dropColumn([
                'OperationsReport',
                'ClientReport',
                'DisbursementReport',
                'ManagementReport',
            ]);
        });
    }
};
