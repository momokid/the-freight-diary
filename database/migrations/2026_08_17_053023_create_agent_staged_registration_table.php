<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agent_staged_registration', function (Blueprint $table) {
            $table->id('ID');
            $table->string('Username', 15)->unique();   // one staged registration per user
            $table->string('BL', 50);
            $table->unsignedInteger('ConsigneeID')->default(0);
            $table->string('ConsigneeGuess', 150)->nullable();
            $table->date('ETA')->nullable();
            $table->unsignedInteger('CarrierID')->default(0);
            $table->unsignedInteger('CmdtCategoryID')->default(0);
            $table->unsignedInteger('CmdtTypeID')->default(0);
            $table->unsignedInteger('ReleaseType')->default(0);
            $table->text('Destination')->nullable();
            $table->string('ShipmentType', 10)->nullable();
            $table->json('InferenceJson')->nullable();
            $table->string('BranchID', 10);
            $table->date('Date');
            $table->dateTime('Time');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_staged_registration');
    }
};
