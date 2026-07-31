<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $steps = json_encode([
            ['key' => 'consignment.resolve', 'inputs' => new stdClass(), 'approval' => null],
            ['key' => 'consignment.read',    'inputs' => new stdClass(), 'approval' => null],
            ['key' => 'reply.compose',       'inputs' => new stdClass(), 'approval' => null],
        ]);

        $examples = implode("\n", [
            'what is the status of BL {BL}',
            'status of {BL}',
            'where is {BL}',
            'check {BL}',
            'has {BL} arrived',
            'any update on {BL}',
        ]);

        DB::statement("
            INSERT IGNORE INTO `agent_playbooks`
              (`PlaybookKey`, `TaskType`, `Title`, `Description`, `IntentExamples`,
               `StepsJson`, `Autonomy`, `IsSystem`, `Version`, `Username`, `BranchID`,
               `CreatedAt`, `Status`)
            VALUES
              ('lookup.status', 'lookup.status', 'Consignment status lookup',
               'Reports where a consignment sits in the workflow and what is owed next. Read-only.',
               ?, ?, 'fill_stop', 1, 1, 'SYSTEM', '', NOW(), 1)
        ", [$examples, $steps]);
    }

    public function down(): void
    {
        DB::table('agent_playbooks')->where('PlaybookKey', 'lookup.status')->delete();
    }
};
