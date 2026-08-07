<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('disbursement_analysis', function (Blueprint $table) {
            // Every existing index leads with ConsigneeID, so lookups by BL scan the table
            $table->index(['BL', 'Status'], 'disb_bl_status_idx');
        });
    }

    public function down(): void
    {
        Schema::table('disbursement_analysis', function (Blueprint $table) {
            $table->dropIndex('disb_bl_status_idx');
        });
    }
};
