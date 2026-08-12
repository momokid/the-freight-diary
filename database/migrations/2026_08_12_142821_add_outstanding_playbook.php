<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const KEY = 'list.outstanding';

    public function up(): void
    {
        DB::table('agent_playbooks')->where('PlaybookKey', self::KEY)->delete();

        DB::table('agent_playbooks')->insert([
            'PlaybookKey'    => self::KEY,
            'TaskType'       => self::KEY,
            'Title'          => 'Outstanding client balances',
            'Description'    => 'Clients with money owing across their consignments.',
            'IntentExamples' => json_encode([
                'who owes us money',
                'show outstanding balances',
                'which clients have outstanding balances',
            ]),
            'StepsJson'      => json_encode([
                ['key' => 'client.list.outstanding', 'inputs' => [], 'approval' => null],
                ['key' => 'reply.list',              'inputs' => [], 'approval' => null],
            ]),
            'ParamsJson'     => null,
            'GatesJson'      => null,
            'Autonomy'       => 'fill_stop',
            'IsSystem'       => 1,
            'Version'        => 1,
            'Username'       => 'system',
            'BranchID'       => '',
            'CreatedAt'      => Carbon::now(),
            'Status'         => 1,
        ]);
    }

    public function down(): void
    {
        DB::table('agent_playbooks')->where('PlaybookKey', self::KEY)->delete();
    }
};
