<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppBotService
{
    private string $apiUrl;
    private string $phoneNumberId;
    private string $token;

    public function __construct()
    {
        $this->apiUrl = config('services.meta.api_url');
        $this->phoneNumberId = config('services.meta.phone_number_id');
        $this->token = config('services.meta.whatsapp_token');
    }

    // Main entry point — called from WhatsAppController::receive()
    public function handle(string $from, string $text, string $msgId): void
    {
        $text = strtoupper(trim($text));

        // Parse BL and CODE from message
        // Expected format: "BL:MEDUWY011522 CODE:4782"
        // Also handle free-form like "MEDUWY011522 4782"
        $parsed = $this->parseMessage($text);

        if (!$parsed) {
            $this->sendMessage($from, $this->helpMessage());
            return;
        }

        ['bl' => $bl, 'code' => $code] = $parsed;

        // Authenticate — check BL exists and code matches
        $consignment = DB::table('container_main as cm')
            ->leftJoin('consignee_main as co', 'cm.ConsigneeID', '=', 'co.ConsigneeID')
            ->leftJoin('ship_carrier as sc', 'cm.CarrierID', '=', 'sc.CarrierID')
            ->where('cm.BL', $bl)
            ->where('cm.ClientCode', $code)
            ->where('cm.Status', '!=', 9)
            ->select([
                'cm.ConsignmentID',
                'cm.BL',
                'cm.ETA',
                'cm.Status',
                'cm.Destination',
                'co.FullName as ConsigneeName',
                'sc.CarrierName',
            ])
            ->first();

        if (!$consignment) {
            $this->sendMessage(
                $from,
                "Invalid BL number or access code. Please check and try again.\n\n"
                    . "Format: BL:XXXXXXXX CODE:XXXX"
            );
            return;
        }

        // Log the chat interaction so officers can see it in the app
        $this->logChat($from, $text, $consignment->ConsignmentID, $bl);

        // Build and send status response
        $this->sendMessage($from, $this->buildStatusMessage($consignment));
    }

    // Parse the incoming message to extract BL and CODE
    private function parseMessage(string $text): ?array
    {
        // Format 1: BL:MEDUWY011522 CODE:4782
        if (preg_match('/BL[:\s]+([A-Z0-9]+).*CODE[:\s]+(\d{4})/i', $text, $m)) {
            return ['bl' => strtoupper($m[1]), 'code' => $m[2]];
        }

        // Format 2: MEDUWY011522 4782 (BL followed by 4 digits)
        if (preg_match('/^([A-Z0-9]{6,30})\s+(\d{4})$/', $text, $m)) {
            return ['bl' => strtoupper($m[1]), 'code' => $m[2]];
        }

        return null;
    }

    // Build the status message from consignment data
    private function buildStatusMessage(object $consignment): string
    {
        $statusMap = [
            0 => '✅ Cleared',
            1 => '🔵 Not Arrived',
            2 => '🟡 In Harbour / Pending',
            3 => '🟠 Gated Out',
        ];

        $status  = $statusMap[$consignment->Status] ?? 'Unknown';
        $eta     = $consignment->ETA
            ? date('d M Y', strtotime($consignment->ETA))
            : '—';
        $carrier = $consignment->CarrierName ?? '—';
        $dest    = $consignment->Destination ?? '—';

        // Outstanding balance from student_fee
        $balance = DB::table('student_fee')
            ->where('StudentID', DB::table('container_main')
                ->where('BL', $consignment->BL)
                ->value('ConsigneeID'))
            ->selectRaw('ROUND(SUM(Dr) - SUM(Cr), 2) AS balance')
            ->value('balance') ?? 0;

        $balanceText = $balance > 0
            ? number_format($balance, 2) . ' GH₵ outstanding'
            : 'No outstanding balance';

        return "*PSIL Consignment Status*\n\n"
            . "BL#: *{$consignment->BL}*\n"
            . "Consignee: {$consignment->ConsigneeName}\n"
            . "Carrier: {$carrier}\n"
            . "Destination: {$dest}\n"
            . "ETA: *{$eta}*\n"
            . "Status: {$status}\n\n"
            . " *Invoice Balance*\n"
            . "{$balanceText}\n\n"
            . "Reply with your BL# and code anytime for updates.";
    }

    // Default help message when format is not recognised
    private function helpMessage(): string
    {
        return "Welcome to *PSIL Freight Tracker*.\n\n"
            . "To check your consignment status, send:\n"
            . "*BL:XXXXXXXX CODE:XXXX*\n\n"
            . "Example:\n"
            . "BL:MEDUWY011522 CODE:4782\n\n"
            . "Your BL number and access code were sent to you via SMS when your consignment was registered.";
    }

    // Log chat to whatsapp_chats table so officers can see interactions
    private function logChat(string $from, string $text, int $consignmentId, string $bl): void
    {
        try {
            DB::table('whatsapp_chats')->insert([
                'ConsignmentID' => $consignmentId,
                'BL'            => $bl,
                'From'          => $from,
                'Message'       => $text,
                'Direction'     => 'inbound',
                'CreatedAt'     => now(),
            ]);
        } catch (\Exception $e) {
            // Don't break the bot if logging fails
            Log::error('[WhatsAppBot] Chat log failed: ' . $e->getMessage());
        }
    }

    // Send a WhatsApp message via Meta Cloud API
    public function sendMessage(string $to, string $message): void
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type'  => 'application/json',
            ])->post("{$this->apiUrl}/{$this->phoneNumberId}/messages", [
                'messaging_product' => 'whatsapp',
                'to'                => $to,
                'type'              => 'text',
                'text'              => ['body' => $message],
            ]);

            if (!$response->successful()) {
                Log::error('[WhatsAppBot] Send failed', [
                    'to'     => $to,
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('[WhatsAppBot] Send exception: ' . $e->getMessage());
        }
    }

    // Send officer notification — called from other parts of the system
    public function notifyOfficer(string $officerPhone, string $message): void
    {
        $this->sendMessage($officerPhone, $message);
    }
}
