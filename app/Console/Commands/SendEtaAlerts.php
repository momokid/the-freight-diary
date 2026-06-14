<?php

namespace App\Console\Commands;

use App\Mail\EtaDigestMail;
use App\Services\ArkeselService;
use App\Services\EtaAlertService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendEtaAlerts extends Command
{
    protected $signature   = 'alerts:eta';
    protected $description = 'Send ETA SMS alerts to consignees and daily digest email to internal staff';

    public function handle(): int
    {
        $service = new EtaAlertService(new ArkeselService());

        $digest = $service->run();

        $recipients = config('alerts.digest_emails', []);

        if (empty($recipients)) {
            Log::warning('[SendEtaAlerts] No digest recipients configured — skipping email.');
            return Command::SUCCESS;
        }

        // Step 5 — one digest email to all internal recipients
        Mail::to($recipients)->send(new EtaDigestMail($digest));

        // Step 6 — log summary so you can confirm the cron ran
        Log::info('[SendEtaAlerts] Run complete', [
            'arriving_today' => count($digest['arriving_today'] ?? []),
            'upcoming' => count($digest['upcoming'] ?? []),
            'eta_changed' => count($digest['eta_changed'] ?? []),
            'digest_sent_to' => count($recipients),
        ]);

        return Command::SUCCESS;
    }
}
