<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_chats', function (Blueprint $table) {
            $table->id();
            $table->integer('ConsignmentID')->nullable()->index();
            $table->string('BL', 50)->nullable()->index();
            $table->string('From', 20);
            $table->text('Message');
            $table->enum('Direction', ['inbound', 'outbound'])->default('inbound');
            $table->timestamp('CreatedAt')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_chats');
    }
};
