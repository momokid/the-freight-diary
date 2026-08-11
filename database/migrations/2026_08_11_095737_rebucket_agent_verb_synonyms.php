<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const BUCKETS = [
        'check' => [
            'arrived',
            'chase',
            'check',
            'confirm',
            'display',
            'fetch',
            'find',
            'get',
            'list',
            'look',
            'pull',
            'see',
            'show',
            'status',
            'trace',
            'track',
            'verify',
            'view',
            'where',
        ],
        'total'     => ['average', 'count', 'sum', 'total'],
        'breakdown' => ['breakdown', 'itemise', 'itemize', 'lines', 'manifest', 'split'],
        'register'  => ['add', 'capture', 'create', 'enter', 'register'],
        'invoice'   => ['bill', 'invoice', 'raise'],
        'disburse'  => ['disburse', 'disbursed', 'disbursement'],
        'receipt'   => ['receipt', 'receive'],
        'declare'   => ['declaration', 'declare'],
        'edit'      => ['adjust', 'amend', 'change', 'correct', 'edit', 'fix', 'modify', 'update'],
        'delete'    => ['delete', 'remove'],
        'gate'      => ['gate', 'gated', 'gateout'],
        'return'    => ['return', 'returned'],
        'notify'    => ['alert', 'message', 'notify', 'send', 'sms'],
        'reverse'   => ['cancel', 'reverse', 'undo', 'void'],
    ];

    public function up(): void
    {
        $now  = Carbon::now();
        $rows = [];

        foreach (self::BUCKETS as $canonical => $verbs) {
            foreach ($verbs as $verb) {
                $rows[] = [
                    'Verb'          => $verb,
                    'CanonicalVerb' => $canonical,
                    'IsSystem'      => 1,
                    'CreatedAt'     => $now,
                    'Status'        => 1,
                ];
            }
        }

        DB::transaction(function () use ($rows) {
            DB::table('agent_verb_synonyms')->delete();
            DB::table('agent_verb_synonyms')->insert($rows);

            // Patterns change with the mapping, so every cached fingerprint
            // is now unreachable. Rebuilds on first use of each phrasing.
            DB::table('agent_intent_cache')->delete();
        });
    }

    public function down(): void
    {
        // The previous mapping bucketed by CRUD category, which made
        // breakdown, invoice, register and disburse share one fingerprint.
        // Restoring it would reintroduce the collision.
    }
};
