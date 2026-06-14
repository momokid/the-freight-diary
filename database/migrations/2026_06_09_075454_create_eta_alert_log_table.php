<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('eta_alert_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('ConsignmentID');           // matches container_main.ConsignmentID int(11)
            $table->string('BL', 50);                   // matches container_main.BL varchar(50)
            $table->integer('ConsigneeID');             // matches consignee_main.ConsigneeID int(11)
            $table->string('AlertType', 20);            // BASELINE | ETA_CHANGE | ARRIVAL
            $table->string('Channel', 10);              // SMS | WHATSAPP | EMAIL | SYSTEM
            $table->string('Recipient', 30)->nullable(); // normalised number/email; null for BASELINE
            $table->date('ETASnapshot');                // ETA value at time of this event
            $table->string('Status', 10);              // SENT | FAILED | SEEN
            $table->string('ProviderRef', 100)->nullable(); // Arkesel message ID (never API key)
            $table->text('Message')->nullable();        // body sent (audit); null for digest/baseline
            $table->dateTime('SentAt');                 // set by application on insert

            // Dedup key — prevents same alert firing twice for the same BL + event + ETA + channel
            $table->unique(['BL', 'AlertType', 'ETASnapshot', 'Channel'], 'eta_alert_dedup');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eta_alert_log');
    }
};
