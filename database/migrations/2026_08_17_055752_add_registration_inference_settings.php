<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $settings = [
        ['registration_inference_model',      'claude-haiku-4-5', 'Registration Inference Model'],
        ['release_type_min_history',          '3',                'Minimum Past Consignments Before Predicting Release Type'],
    ];

    public function up(): void
    {
        foreach ($this->settings as [$key, $value, $label]) {
            DB::table('system_settings')->updateOrInsert(
                ['key' => $key],
                ['value' => $value, 'label' => $label, 'group' => 'agent', 'updated_at' => now(), 'created_at' => now()]
            );
        }
    }

    public function down(): void
    {
        DB::table('system_settings')
            ->whereIn('key', array_column($this->settings, 0))
            ->delete();
    }
};
