<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $cards = [
        'lookup.status'          => ["Check a consignment's status", 10],
        'lookup.manifest'        => ['See a manifest breakdown', 20],
        'list.overdue'           => ['Show overdue consignments', 30],
        'list.not_disbursed'     => ["Show what hasn't been disbursed", 40],
        'list.not_invoiced'      => ["Show what hasn't been invoiced", 50],
        'list.unconfirmed_type'  => ['Show consignments with no type set', 60],
        'list.outstanding'       => ['Show clients who owe money', 70],
    ];

    public function up(): void
    {
        Schema::table('agent_playbooks', function (Blueprint $table) {
            $table->string('CardTitle', 120)->nullable()->after('Title');
            $table->unsignedSmallInteger('SortOrder')->default(0)->after('CardTitle');
        });

        foreach ($this->cards as $key => [$title, $order]) {
            DB::table('agent_playbooks')
                ->where('PlaybookKey', $key)
                ->update(['CardTitle' => $title, 'SortOrder' => $order]);
        }
    }

    public function down(): void
    {
        Schema::table('agent_playbooks', function (Blueprint $table) {
            $table->dropColumn(['CardTitle', 'SortOrder']);
        });
    }
};
