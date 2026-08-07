<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stall_claims', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('ConsignmentID');
            $table->string('BL', 100);
            $table->string('Stage', 40);      // disbursement | gateout | return
            $table->string('Username', 50);   // matches user_auth.Username
            $table->dateTime('ClaimedAt');

            // One claim per consignment per stage — a second press updates, never duplicates
            $table->unique(['ConsignmentID', 'BL', 'Stage'], 'stall_claims_unique');

            // The bell needs unclaimed vs gone-quiet, both driven off ClaimedAt
            $table->index('ClaimedAt', 'stall_claims_claimed_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stall_claims');
    }
};
