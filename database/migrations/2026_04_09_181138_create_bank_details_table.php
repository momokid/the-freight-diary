<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_details', function (Blueprint $table) {
            $table->id();
            $table->string('BankName', 100);
            $table->string('AccountName', 150);
            $table->string('AccountNo', 50);
            $table->string('Branch', 100)->nullable();
            $table->string('MomoQR', 255)->nullable();   // path to QR image
            $table->string('MerchantID', 50)->nullable();
            $table->string('MerchantName', 100)->nullable();
            $table->tinyInteger('is_active')->default(1);
            $table->string('Username', 15)->default('system');
            $table->timestamps();
        });

        // Seed with PSIL bank details
        DB::table('bank_details')->insert([
            'BankName'     => 'ECOBANK',
            'AccountName'  => 'Prime Survivors International Limited',
            'AccountNo'    => '1441004070750',
            'Branch'       => 'Tema Main',
            'MomoQR'       => 'images/momo-qr.png',
            'MerchantID'   => '817720',
            'MerchantName' => 'PRIME SURVIVORS INT. LTD',
            'is_active'    => 1,
            'Username'     => 'system',
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_details');
    }
};
