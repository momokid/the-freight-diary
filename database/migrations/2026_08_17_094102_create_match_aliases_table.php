<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('match_aliases', function (Blueprint $table) {
            $table->id('ID');
            $table->string('SourceKey', 20);        // consignee, carrier, shipper, pol, pod
            $table->string('RawText', 255);         // what the document said
            $table->unsignedInteger('MatchedID');   // what the user chose
            $table->unsignedInteger('UseCount')->default(1);
            $table->string('Username', 15);
            $table->string('BranchID', 10);
            $table->date('Date');
            $table->dateTime('Time');

            $table->unique(['SourceKey', 'RawText'], 'match_aliases_source_text_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('match_aliases');
    }
};
