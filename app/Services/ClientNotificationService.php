<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class ClientNotificationService
{
    public function __construct(protected ArkeselService $arkesel) {}

    public function generateClientCode(): string
    {
        return str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
    }

    public function buildMessage(string $event, string $bl, array $params = []): string
    {
        return match ($event) {
            'registration' =>
            "Dear Client, your consignment BL# {$bl} has been registered with PSIL. "
                . "Your access code is {$params['client_code']}."
                . ($params['wa_link'] ?? null
                    ? "\n\nChat with us on WhatsApp:\n{$params['wa_link']}"
                    : ''),

            'gate_out' =>
            "Dear Client, your consignment BL# {$bl} has been released for gate-out by PSIL. "
                . "Please arrange collection at your earliest convenience.",

            'invoice_payment' =>
            "Dear Client, your payment for consignment BL# {$bl} "
                . "has been recorded by PSIL. Thank you for your payment.",

            'eta_change' =>
            $params['message'] ?? "Dear Client, the ETA for your consignment BL# {$bl} has been updated. Please contact PSIL for further details.",

            'manual' => $params['message'] ?? '',

            default => '',
        };
    }

    public function sendSMS(
        string $bl,
        string $phone,
        string $event,
        array $params = [],
        int $consigneeId = 0,
        string $sentBy = ''
    ): array {
        if ($event === 'registration') {
            $whatsappNumber = DB::table('system_settings')
                ->where('key', 'whatsapp_number')
                ->value('value');

            $waText = urlencode("BL:{$bl} CODE:{$params['client_code']}");
            $params['wa_link'] = $whatsappNumber
                ? "https://wa.me/{$whatsappNumber}?text={$waText}"
                : null;
        }

        $message = $this->buildMessage($event, $bl, $params);

        if (empty($message)) {
            return [
                'success' => false,
                'message' => 'Invalid message event type.',
            ];
        }

        $result = $this->arkesel->sendSms($phone, $message);

        $this->logMessage([
            'BL'          => $bl,
            'ConsigneeID' => $consigneeId,
            'channel'     => 'sms',
            'event'       => $event,
            'phone'       => $phone,
            'message'     => $message,
            'status'      => $result['success'] ? 'sent' : 'failed',
            'sent_by'     => $sentBy,
        ]);

        return [
            'success' => $result['success'],
            'message' => $result['success']
                ? 'Message sent successfully.'
                : 'SMS could not be sent. Please try again or notify the client manually.',
        ];
    }

    private function logMessage(array $data): void
    {
        DB::table('client_messages')->insert([
            'BL'          => $data['BL'],
            'ConsigneeID' => $data['ConsigneeID'] ?? 0,
            'channel'     => $data['channel'],
            'event'       => $data['event'],
            'phone'       => $data['phone'],
            'message'     => $data['message'],
            'status'      => $data['status'],
            'sent_by'     => $data['sent_by'] ?? '',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }
}
