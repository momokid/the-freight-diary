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
            ['key' => 'manifest.read',       'inputs' => new stdClass(), 'approval' => null],
            ['key' => 'reply.manifest',      'inputs' => new stdClass(), 'approval' => null],
        ]);

        $examples = implode("\n", [
            'show the manifest breakdown for {BL}',
            'breakdown of {BL}',
            'who are the consignees on {BL}',
            'list the house bills for {BL}',
            'what is inside {BL}',
            'how many house bls on {BL}',
        ]);

        DB::statement("
            INSERT IGNORE INTO `agent_playbooks`
              (`PlaybookKey`, `TaskType`, `Title`, `Description`, `IntentExamples`,
               `StepsJson`, `Autonomy`, `IsSystem`, `Version`, `Username`, `BranchID`,
               `CreatedAt`, `Status`)
            VALUES
              ('lookup.manifest', 'lookup.manifest', 'Manifest breakdown lookup',
               'Lists the house BLs on a consignment with consignee, packages and weight. Read-only.',
               ?, ?, 'fill_stop', 1, 1, 'SYSTEM', '', NOW(), 1)
        ", [$examples, $steps]);
    }

    public function down(): void
    {
        DB::table('agent_playbooks')->where('PlaybookKey', 'lookup.manifest')->delete();
    }
};
