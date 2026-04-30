<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hs_codes', function (Blueprint $table) {
            $table->id();

            // ── Classification levels ─────────────────────────────────────
            $table->string('Chapter', 2)->index();        // e.g. "87"
            $table->string('ChapterDesc', 255);           // e.g. "Vehicles other than railway"
            $table->string('Heading', 4)->index();        // e.g. "8703"
            $table->string('HeadingDesc', 500);           // e.g. "Motor cars and other motor vehicles"
            $table->string('HSCode', 10)->unique();       // Full code e.g. "8703"

            // ── Duty information ──────────────────────────────────────────
            // Ghana ECOWAS CET bands: 0, 5, 10, 20, 35
            $table->decimal('ImportDutyRate', 5, 2)->default(0);    // % e.g. 20.00
            $table->decimal('VATRate', 5, 2)->default(15.00);        // Ghana VAT 15%
            $table->decimal('NHILRate', 5, 2)->default(2.50);        // NHIL 2.5%
            $table->decimal('GETFundRate', 5, 2)->default(2.50);     // GETFund 2.5%
            $table->decimal('COVIDRate', 5, 2)->default(0.00);       // COVID levy 1%
            $table->decimal('ECOWASRate', 5, 2)->default(0.50);      // ECOWAS levy 0.5%
            $table->decimal('AURate', 5, 2)->default(0.20);          // AU levy 0.2%

            // ── WCO classification notes ──────────────────────────────────
            // Used by AI to construct legal arguments
            $table->text('Notes')->nullable();            // WCO explanatory notes
            $table->text('Inclusions')->nullable();       // What this heading covers
            $table->text('Exclusions')->nullable();       // What is excluded (points to other headings)

            // ── Search keywords ───────────────────────────────────────────
            // Comma-separated keywords for the local rules engine
            $table->text('Keywords')->nullable();

            // ── Metadata ─────────────────────────────────────────────────
            $table->boolean('IsActive')->default(true);
            $table->string('ECOWASBand', 10)->nullable();  // 0/5/10/20/35
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hs_codes');
    }
};
