<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const PLAYBOOKS = [
        [
            'key'      => 'list.overdue',
            'title'    => 'Overdue consignments',
            'desc'     => 'Consignments past the expected time for their current stage.',
            'examples' => ['what is overdue', 'show me overdue consignments', 'what is late'],
            'step'     => 'consignment.list.operations',
            'filter'   => 'overdue',
        ],
        [
            'key'      => 'list.unconfirmed_type',
            'title'    => 'Consignments with unconfirmed type',
            'desc'     => 'Arrived consignments never confirmed as LCL or FCL.',
            'examples' => ['which consignments have unconfirmed type', 'show unconfirmed type'],
            'step'     => 'consignment.list.operations',
            'filter'   => 'unconfirmed_type',
        ],
        [
            'key'      => 'list.not_disbursed',
            'title'    => 'Consignments with no disbursement',
            'desc'     => 'Arrived consignments with no disbursement raised against them.',
            'examples' => ['what has not been disbursed', 'show consignments with no disbursement'],
            'step'     => 'consignment.list.disbursement',
            'filter'   => 'not_disbursed',
        ],
        [
            'key'      => 'list.not_invoiced',
            'title'    => 'Consignments not yet invoiced',
            'desc'     => 'Disbursed consignments with nothing billed to the client yet.',
            'examples' => ['what has not been invoiced', 'show consignments not yet invoiced'],
            'step'     => 'consignment.list.client',
            'filter'   => 'not_invoiced',
        ],
    ];

    public function up(): void
    {
        $now  = Carbon::now();
        $rows = [];

        foreach (self::PLAYBOOKS as $p) {
            $rows[] = [
                'PlaybookKey'    => $p['key'],
                'TaskType'       => $p['key'],
                'Title'          => $p['title'],
                'Description'    => $p['desc'],
                'IntentExamples' => json_encode($p['examples']),
                'StepsJson'      => json_encode([
                    [
                        'key'      => $p['step'],
                        'inputs'   => ['Filter' => $p['filter']],
                        'approval' => null,
                    ],
                    [
                        'key'      => 'reply.list',
                        'inputs'   => [],
                        'approval' => null,
                    ],
                ]),
                'ParamsJson'     => null,
                'GatesJson'      => null,
                'Autonomy'       => 'fill_stop',
                'IsSystem'       => 1,
                'Version'        => 1,
                'Username'       => 'system',
                'BranchID'       => '',
                'CreatedAt'      => $now,
                'Status'         => 1,
            ];
        }

        DB::table('agent_playbooks')
            ->whereIn('PlaybookKey', array_column(self::PLAYBOOKS, 'key'))
            ->delete();

        DB::table('agent_playbooks')->insert($rows);
    }

    public function down(): void
    {
        DB::table('agent_playbooks')
            ->whereIn('PlaybookKey', array_column(self::PLAYBOOKS, 'key'))
            ->delete();
    }
};
