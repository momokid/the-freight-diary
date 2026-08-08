<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * An LCL has no single consignee — they sit per house BL in the breakdown.
 * The status reply needs them, so manifest.read joins the chain before the
 * reply is composed. On an FCL it returns an empty list and costs one
 * indexed query.
 */
return new class extends Migration
{
    private const KEY = 'lookup.status';

    public function up(): void
    {
        $this->setSteps([
            ['key' => 'consignment.resolve', 'inputs' => new stdClass(), 'approval' => null],
            ['key' => 'consignment.read',    'inputs' => new stdClass(), 'approval' => null],
            ['key' => 'manifest.read',       'inputs' => new stdClass(), 'approval' => null],
            ['key' => 'reply.compose',       'inputs' => new stdClass(), 'approval' => null],
        ]);
    }

    public function down(): void
    {
        $this->setSteps([
            ['key' => 'consignment.resolve', 'inputs' => new stdClass(), 'approval' => null],
            ['key' => 'consignment.read',    'inputs' => new stdClass(), 'approval' => null],
            ['key' => 'reply.compose',       'inputs' => new stdClass(), 'approval' => null],
        ]);
    }

    private function setSteps(array $steps): void
    {
        DB::table('agent_playbooks')
            ->where('PlaybookKey', self::KEY)
            ->update([
                'StepsJson' => json_encode($steps),
                'Version'   => DB::raw('Version + 1'),
            ]);
    }
};
