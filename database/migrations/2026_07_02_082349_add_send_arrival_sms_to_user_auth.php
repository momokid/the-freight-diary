<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('arrival_sms_queue', function (Blueprint $table) {
            $table->bigIncrements('ID');
            $table->integer('ConsignmentID');
            $table->string('BL', 50);
            $table->integer('ConsigneeID');
            $table->string('ConsigneeName', 500);
            $table->string('Phone', 30);
            $table->date('ETA');
            $table->integer('ContainerCount')->default(0);
            $table->text('Message');
            $table->tinyInteger('Status')->default(0); // 0=pending, 1=sent
            $table->string('SentBy', 100)->nullable();
            $table->dateTime('SentAt')->nullable();
            $table->date('QueueDate');                 // idempotency — one row per BL per day

            $table->unique(['BL', 'QueueDate'], 'arrival_queue_dedup');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('arrival_sms_queue');
    }
};
