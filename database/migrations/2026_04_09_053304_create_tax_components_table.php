<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tax_components', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);                    // 'GetFund', 'NHIL', 'VAT'
            $table->string('label', 100);                  // Display name e.g. 'Ghana Education Trust Fund'
            $table->decimal('rate', 5, 2);                 // e.g. 2.50, 15.00
            $table->enum('applies_on', ['base', 'subtotal']); // 'base' = on original amount, 'subtotal' = on running total
            $table->integer('sort_order');                 // order of calculation
            $table->tinyInteger('is_active')->default(1);  // 1 = active, 0 = inactive
            $table->date('effective_date');
            $table->string('Username', 15)->default('system');
            $table->timestamps();
        });

        // Seed with current Ghana tax components (old cascading system)
        DB::table('tax_components')->insert([
            [
                'name'           => 'GetFund',
                'label'          => 'Ghana Education Trust Fund Levy',
                'rate'           => 2.50,
                'applies_on'     => 'base',
                'sort_order'     => 1,
                'is_active'      => 1,
                'effective_date' => '2026-01-01',
                'Username'       => 'system',
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'name'           => 'NHIL',
                'label'          => 'National Health Insurance Levy',
                'rate'           => 2.50,
                'applies_on'     => 'base',
                'sort_order'     => 2,
                'is_active'      => 1,
                'effective_date' => '2026-01-01',
                'Username'       => 'system',
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'name'           => 'VAT',
                'label'          => 'Value Added Tax',
                'rate'           => 15.00,
                'applies_on'     => 'subtotal',
                'sort_order'     => 3,
                'is_active'      => 1,
                'effective_date' => '2026-01-01',
                'Username'       => 'system',
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_components');
    }
};