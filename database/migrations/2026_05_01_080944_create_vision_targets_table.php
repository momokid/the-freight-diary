<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vision_targets', function (Blueprint $table) {
            $table->id();
            $table->string('TargetName', 100);
            $table->decimal('TargetAmount', 15, 2);
            $table->integer('TargetYear');
            $table->integer('StartYear');
            $table->text('Description')->nullable();
            $table->string('Username', 20);
            $table->date('Date');
            $table->tinyInteger('IsActive')->default(1);
            $table->timestamps();
        });

        // Seed Vision 5:29
        DB::table('vision_targets')->insert([
            'TargetName' => 'Vision 5:29',
            'TargetAmount' => 5000000.00,
            'TargetYear' => 2029,
            'StartYear' => 2024,
            'Description' => 'To make a net profit of GH₵5 million by the year 2029',
            'Username' => 'system',
            'Date' => now()->toDateString(),
            'IsActive' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('vision_targets');
    }
};
