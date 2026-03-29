<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_login_logs_archive', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('status');
            $table->timestamp('created_at')->nullable(); // original log time
            $table->timestamp('archived_at')->useCurrent(); // when it was archived
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_login_logs_archive');
    }
};
