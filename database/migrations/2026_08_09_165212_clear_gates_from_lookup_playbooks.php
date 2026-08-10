<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * A disbursed gate had been set by hand on lookup.status, so asking where a
 * consignment sat was refused until the disbursement was raised.
 *
 * Gates stop work being done out of order. A lookup does no work, so a
 * read-only playbook has nothing to gate.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('agent_playbooks')
            ->whereIn('PlaybookKey', ['lookup.status', 'lookup.manifest'])
            ->update([
                'GatesJson' => null,
                'Version'   => DB::raw('Version + 1'),
            ]);
    }

    public function down(): void
    {
        // Nothing to restore — the gate was never intended.
    }
};
