<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /** IntentExamples is newline-delimited. Earlier migrations wrote JSON. */
    public function up(): void
    {
        $rows = DB::table('agent_playbooks')
            ->where('IntentExamples', 'like', '[%')
            ->get(['ID', 'IntentExamples']);

        foreach ($rows as $row) {
            $examples = json_decode($row->IntentExamples, true);

            if (! is_array($examples)) {
                continue;
            }

            DB::table('agent_playbooks')
                ->where('ID', $row->ID)
                ->update(['IntentExamples' => implode("\n", $examples)]);
        }
    }

    public function down(): void
    {
        // The newline form is correct; reverting would break the accessor.
    }
};
