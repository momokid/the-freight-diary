<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('arrival_sms_queue', function (Blueprint $table) {
            $table->integer('ConsignmentID')->after('id');
            $table->string('BL', 50)->after('ConsignmentID');
            $table->integer('ConsigneeID')->after('BL');
            $table->string('ConsigneeName', 500)->after('ConsigneeID');
            $table->string('Phone', 30)->after('ConsigneeName');
            $table->date('ETA')->after('Phone');
            $table->integer('ContainerCount')->default(0)->after('ETA');
            $table->text('Message')->after('ContainerCount');
            $table->tinyInteger('Status')->default(0)->after('Message');
            $table->string('SentBy', 100)->nullable()->after('Status');
            $table->dateTime('SentAt')->nullable()->after('SentBy');
            $table->date('QueueDate')->after('SentAt');

            $table->unique(['BL', 'QueueDate'], 'arrival_queue_dedup');
        });
    }

    public function down(): void
    {
        Schema::table('arrival_sms_queue', function (Blueprint $table) {
            $table->dropUnique('arrival_queue_dedup');
            $table->dropColumn(['ConsignmentID', 'BL', 'ConsigneeID', 'ConsigneeName', 'Phone', 'ETA', 'ContainerCount', 'Message', 'Status', 'SentBy', 'SentAt', 'QueueDate']);
        });
    }
};
