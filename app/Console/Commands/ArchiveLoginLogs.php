<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ArchiveLoginLogs extends Command
{
    // The command name — run with: php artisan logs:archive
    protected $signature = 'logs:archive';

    // Description shown in php artisan list
    protected $description = 'Archive login logs older than 90 days and permanently delete archives older than 5 years';

    public function handle()
    {
        $now = now();

        // ── Step 1: Move logs older than 90 days to archive ──
        $logsToArchive = DB::table('user_login_logs')
            ->where('created_at', '<', $now->copy()->subDays(90))
            ->get();

        if ($logsToArchive->isEmpty()) {
            $this->info('No logs to archive.');
        } else {
            // Insert into archive table
            $archiveRecords = $logsToArchive->map(function ($log) {
                return [
                    'username'    => $log->username,
                    'ip_address'  => $log->ip_address,
                    'user_agent'  => $log->user_agent,
                    'status'      => $log->status,
                    'created_at'  => $log->created_at,  // preserve original timestamp
                    'archived_at' => now(),
                ];
            })->toArray();

            DB::table('user_login_logs_archive')->insert($archiveRecords);

            // Delete from main table after successful archive
            DB::table('user_login_logs')
                ->where('created_at', '<', $now->copy()->subDays(90))
                ->delete();

            $this->info("Archived {$logsToArchive->count()} log(s) successfully.");
        }

        // ── Step 2: Permanently delete archives older than 5 years ──
        $deleted = DB::table('user_login_logs_archive')
            ->where('archived_at', '<', $now->copy()->subYears(5))
            ->delete();

        if ($deleted > 0) {
            $this->info("Permanently deleted {$deleted} archive record(s) older than 5 years.");
        } else {
            $this->info('No archive records old enough for permanent deletion.');
        }

        $this->info('Log archiving complete.');

        return Command::SUCCESS;
    }
}
