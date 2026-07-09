<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\ErrorAlertMail;

class ErrorLogService
{
    public function __construct(protected ArkeselService $arkesel) {}

    public function logException(\Throwable $e, ?string $route = null, ?string $username = null): void
    {
        try {
            $signature = hash('sha256', get_class($e) . $e->getFile() . $e->getLine());

            $existing = DB::table('error_log')->where('Signature', $signature)->first();

            if ($existing && $existing->Status !== 'resolved') {
                // Known, still-open issue — just bump the counter
                DB::table('error_log')->where('ID', $existing->ID)->update([
                    'OccurrenceCount' => $existing->OccurrenceCount + 1,
                    'LastSeenAt'      => now(),
                ]);
                return;
            }

            $isReopen = $existing && $existing->Status === 'resolved';

            $payload = [
                'Signature'      => $signature,
                'ExceptionClass' => get_class($e),
                'Message'        => $e->getMessage(),
                'File'           => $e->getFile(),
                'Line'           => $e->getLine(),
                'Trace'          => $e->getTraceAsString(),
                'Route'          => $route,
                'Username'       => $username,
                'Status'         => 'new',
                'OccurrenceCount' => $isReopen ? $existing->OccurrenceCount + 1 : 1,
                'LastSeenAt'     => now(),
            ];

            if ($isReopen) {
                DB::table('error_log')->where('ID', $existing->ID)->update($payload);
            } else {
                $payload['FirstSeenAt'] = now();
                DB::table('error_log')->insert($payload);
            }

            // New signature (first-ever OR reopened) — push alert
            $this->sendAlert($payload);
        } catch (\Throwable $inner) {
            // Never let error logging itself crash the app
            Log::error('[ErrorLogService] Failed to log exception', ['message' => $inner->getMessage()]);
        }
    }

    protected function sendAlert(array $error): void
    {
        $email = DB::table('system_settings')->where('key', 'error_alert_email')->value('value');
        $phone = DB::table('system_settings')->where('key', 'error_alert_phone')->value('value');

        if ($email) {
            try {
                Mail::to($email)->send(new ErrorAlertMail($error));
            } catch (\Throwable $mailError) {
                Log::error('[ErrorLogService] Failed to send alert email');
            }
        }

        if ($phone) {
            $message = "New error: {$error['ExceptionClass']} at {$error['Route']}. Check Error Log Tickets.";
            $this->arkesel->sendSms($phone, $message);
        }
    }
}
