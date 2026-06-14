<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ArkeselService
{
    /**
     * Send an SMS via Arkesel v1 GET API.
     *
     * @return array{success: bool, ref: string|null, error: string|null}
     */
    public function sendSms(string $phone, string $message): array
    {
        $normalised = $this->normalisePhone($phone);

        if ($normalised === null) {
            Log::warning('[ArkeselService] Invalid phone skipped', ['raw' => $phone]);
            return ['success' => false, 'ref' => null, 'error' => 'Invalid phone'];
        }

        if (config('services.arkesel.sandbox')) {
            Log::info('[ArkeselService] SANDBOX — SMS not sent', [
                'to'      => $normalised,
                'message' => $message,
            ]);
            return ['success' => true, 'ref' => 'SANDBOX', 'error' => null];
        }

        try {
            $response = Http::get(config('services.arkesel.sms_url'), [
                'action'  => 'send-sms',
                'api_key' => config('services.arkesel.api_key'),
                'to'      => $normalised,
                'from'    => config('services.arkesel.sender_id'),
                'sms'     => $message,
            ]);

            if ($response->successful()) {
                Log::info('[ArkeselService] SMS sent', ['to' => $normalised]);
                return [
                    'success' => true,
                    'ref'     => substr($response->body(), 0, 100),
                    'error'   => null,
                ];
            }

            Log::error('[ArkeselService] SMS failed', [
                'to'          => $normalised,
                'http_status' => $response->status(),
            ]);
            return ['success' => false, 'ref' => null, 'error' => 'HTTP ' . $response->status()];
        } catch (\Exception $e) {
            // Never expose $e->getMessage() in production
            Log::error('[ArkeselService] SMS exception', ['to' => $normalised]);
            return ['success' => false, 'ref' => null, 'error' => 'Request failed'];
        }
    }

    /**
     * Normalise a Ghana phone number to 233XXXXXXXXX format.
     * Returns null if the number cannot be resolved to a valid Ghana mobile.
     */
    private function normalisePhone(string $phone): ?string
    {
        // Strip everything that is not a digit
        $digits = preg_replace('/\D/', '', $phone);

        // Local format 0XXXXXXXXX (10 digits) → 233XXXXXXXXX
        if (strlen($digits) === 10 && str_starts_with($digits, '0')) {
            $digits = '233' . substr($digits, 1);
        }

        // Valid Ghana mobile: 233 + 9 digits = 12 digits total
        if (strlen($digits) === 12 && str_starts_with($digits, '233')) {
            return $digits;
        }

        return null; // landline, empty, garbage — skip silently
    }
}
