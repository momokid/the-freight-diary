<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('duty_predictions', function (Blueprint $table) {
            $table->id();

            // ── Source ────────────────────────────────────────────────────
            $table->string('SourceType', 10);           // 'LCL' or 'FCL'
            $table->integer('ConsignmentID');
            $table->string('BL', 50);
            $table->string('HouseBL', 50)->nullable();  // LCL only
            $table->string('ItemDescription', 500);

            // ── Prediction ────────────────────────────────────────────────
            $table->string('PredictedHSCode', 10);
            $table->string('PredictedHSDesc', 255);
            $table->decimal('PredictedDutyRate', 5, 2);
            $table->decimal('PredictedDutyAmt', 15, 2)->nullable(); // if CIF known
            $table->tinyInteger('Confidence')->default(0);           // 0-100
            $table->string('PredictionSource', 20);                  // 'rules' or 'gemini'
            $table->text('Justification')->nullable();               // AI-generated argument
            $table->json('AllCandidates')->nullable();               // Full list of candidates

            // ── Accepted outcome ─────────────────────────────────────────
            // Filled when officer accepts or overrides
            $table->string('AcceptedHSCode', 10)->nullable();
            $table->string('AcceptedHSDesc', 255)->nullable();
            $table->decimal('AcceptedDutyRate', 5, 2)->nullable();
            $table->boolean('WasPredictionAccepted')->nullable();    // true = officer used our code
            $table->string('AcceptedBy', 20)->nullable();            // Username
            $table->timestamp('AcceptedAt')->nullable();

            // ── Actual declared outcome ───────────────────────────────────
            // Filled after customs declaration (for model improvement)
            $table->string('DeclaredHSCode', 10)->nullable();
            $table->decimal('ActualDutyPaid', 15, 2)->nullable();
            $table->decimal('DutySaved', 15, 2)->nullable();        // vs highest candidate
            $table->string('CustomsOutcome', 20)->nullable();        // 'accepted', 'disputed', 'overridden'

            // ── Metadata ─────────────────────────────────────────────────
            $table->string('Username', 20);
            $table->string('BranchID', 10);
            $table->timestamps();

            $table->index(['BL', 'HouseBL']);
            $table->index(['ConsignmentID', 'SourceType']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('duty_predictions');
    }
};
