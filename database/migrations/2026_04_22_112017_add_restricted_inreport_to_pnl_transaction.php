<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pnl_transaction', function (Blueprint $table) {
            $table->tinyInteger('Restricted')->default(0)->after('Status')
                ->comment('0=visible to all, 1=DisbursementOtherExpense only, 2=DisbursementRevenue only');
            $table->tinyInteger('InReport')->default(1)->after('Restricted')
                ->comment('1=include in reports, 0=exclude from reports');
        });
    }

    public function down(): void
    {
        Schema::table('pnl_transaction', function (Blueprint $table) {
            $table->dropColumn(['Restricted', 'InReport']);
        });
    }
};
