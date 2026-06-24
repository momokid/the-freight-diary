<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('client_messages', function (Blueprint $table) {
            $table->id();
            $table->string('BL', 50)->index();
            $table->unsignedInteger('ConsigneeID')->index();
            $table->string('channel', 10)->default('sms'); // sms|whatsapp
            $table->string('event', 30); // registration|gate_out|invoice_payment|manual
            $table->string('phone', 20);
            $table->text('message');
            $table->string('status', 10)->default('sent'); // sent|failed
            $table->string('sent_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_messages');
    }
};
