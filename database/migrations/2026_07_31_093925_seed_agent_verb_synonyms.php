<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // [verb, canonical, intentKey, weight]
        // Verbs MUST be lowercase — the normaliser matches lowercased tokens.
        $rows = [
            // ── Look something up ──
            ['status',   'check', 'lookup.status', 2.00],
            ['track',    'check', 'lookup.status', 2.00],
            ['trace',    'check', 'lookup.status', 2.00],
            ['where',    'check', 'lookup.status', 1.50],
            ['arrived',  'check', 'lookup.status', 1.50],
            ['check',    'check', null,            1.00],
            ['confirm',  'check', null,            1.00],
            ['verify',   'check', null,            1.00],
            ['find',     'check', null,            1.00],
            ['show',     'check', null,            1.00],
            ['view',     'check', null,            1.00],
            ['see',      'check', null,            1.00],
            ['look',     'check', null,            1.00],
            ['pull',     'check', null,            1.00],
            ['fetch',    'check', null,            1.00],
            ['get',      'check', null,            1.00],
            ['chase',    'check', null,            1.00],

            // ── Bring something into being ──
            ['create',   'create', null, 1.00],
            ['make',     'create', null, 1.00],
            ['prepare',  'create', null, 1.00],
            ['prep',     'create', null, 1.00],
            ['do',       'create', null, 1.00],
            ['set',      'create', null, 1.00],
            ['setup',    'create', null, 1.00],
            ['enter',    'create', null, 1.00],
            ['capture',  'create', null, 1.00],
            ['register', 'create', null, 1.00],
            ['raise',    'create', null, 1.00],
            ['generate', 'create', null, 1.00],
            ['issue',    'create', null, 1.00],
            ['add',      'create', null, 1.00],
            ['new',      'create', null, 1.00],
            ['open',     'create', null, 1.00],
            ['post',     'create', null, 1.00],
            ['run',      'create', null, 1.00],
            ['process',  'create', null, 1.00],
            ['breakdown', 'create', null, 1.00],
            ['disburse', 'create', 'disbursement.analysis', 2.00],
            ['invoice',  'create', null, 1.50],
            ['receipt',  'create', null, 1.50],

            // ── Change something ──
            ['edit',     'edit', null, 1.00],
            ['change',   'edit', null, 1.00],
            ['modify',   'edit', null, 1.00],
            ['correct',  'edit', null, 1.00],
            ['amend',    'edit', null, 1.00],
            ['adjust',   'edit', null, 1.00],
            ['fix',      'edit', null, 1.00],

            // ── Undo something ──
            ['reverse',  'reverse', null, 2.00],
            ['undo',     'reverse', null, 2.00],
            ['cancel',   'reverse', null, 1.50],
            ['void',     'reverse', null, 1.50],

            // ── Tell someone ──
            ['send',     'notify', null, 1.00],
            ['notify',   'notify', null, 1.00],
            ['alert',    'notify', null, 1.00],
            ['sms',      'notify', null, 1.50],
            ['message',  'notify', null, 1.00],
        ];

        $now = now()->toDateTimeString();

        foreach ($rows as [$verb, $canonical, $intent, $weight]) {
            DB::statement(
                "INSERT IGNORE INTO `agent_verb_synonyms`
                 (`Verb`, `CanonicalVerb`, `IntentKey`, `Weight`, `IsSystem`, `HitCount`, `CreatedAt`, `Status`)
                 VALUES (?, ?, ?, ?, 1, 0, ?, 1)",
                [$verb, $canonical, $intent, $weight, $now]
            );
        }
    }

    public function down(): void
    {
        DB::table('agent_verb_synonyms')->where('IsSystem', 1)->delete();
    }
};
