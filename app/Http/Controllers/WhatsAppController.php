<?php

namespace App\Http\Controllers;

use App\Services\WhatsAppBotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppController extends Controller
{
    public function verify(Request $request)
    {
        $mode      = $request->query('hub_mode');
        $token     = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        if ($mode === 'subscribe' && $token === config('services.meta.verify_token')) {
            Log::info('[WhatsApp] Webhook verified successfully.');
            return response($challenge, 200);
        }

        Log::warning('[WhatsApp] Webhook verification failed.', [
            'mode'  => $mode,
            'token' => $token,
        ]);

        return response('Forbidden', 403);
    }

    // Meta sends a POST request every time a message arrives
    public function receive(Request $request)
    {
        $payload = $request->all();

        Log::info('[WhatsApp] Incoming payload', $payload);

        $entry   = $payload['entry'][0] ?? null;
        $changes = $entry['changes'][0] ?? null;
        $value   = $changes['value'] ?? null;

        // Only process actual messages — ignore delivery receipts, read receipts etc.
        if (!isset($value['messages'])) {
            return response('OK', 200);
        }

        $message  = $value['messages'][0];
        $from     = $message['from'];          // sender's WhatsApp number e.g. 233244857634
        $type     = $message['type'];          // text, image, audio etc.
        $text     = $message['text']['body'] ?? null;
        $msgId    = $message['id'];

        // Only handle text messages for now
        if ($type !== 'text' || !$text) {
            app(WhatsAppBotService::class)->sendMessage(
                $from,
                "Sorry, I can only process text messages. Please send your BL number and access code as text."
            );
            return response('OK', 200);
        }

        // Hand off to bot service
        app(WhatsAppBotService::class)->handle($from, trim($text), $msgId);

        return response('OK', 200);
    }
}
