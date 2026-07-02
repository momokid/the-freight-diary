<?php

namespace App\Http\Controllers;

use App\Services\ArkeselService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ArrivalSmsController extends Controller
{
    public function __construct(protected ArkeselService $arkesel) {}

    // Returns today's pending rows for the modal
    public function pending()
    {
        $rows = DB::table('arrival_sms_queue')
            ->where('QueueDate', now()->toDateString())
            ->where('Status', 0)
            ->orderBy('BL')
            ->get();

        return response()->json([
            'success' => true,
            'count'   => $rows->count(),
            'rows'    => $rows,
        ]);
    }

    // Send a single row by ID
    public function send(Request $request)
    {
        $request->validate([
            'id'      => ['required', 'integer'],
            'phone'   => ['required', 'string', 'max:20'],
            'message' => ['required', 'string'],
        ]);

        $row = DB::table('arrival_sms_queue')
            ->where('ID', $request->id)
            ->where('Status', 0)
            ->first();

        if (!$row) {
            return response()->json([
                'success' => false,
                'message' => 'Queue item not found or already sent.',
            ], 404);
        }

        $result = $this->arkesel->sendSms($request->phone, $request->message);

        if ($result['success']) {
            $user = Auth::user();

            DB::table('arrival_sms_queue')
                ->where('ID', $request->id)
                ->update([
                    'Status' => 1,
                    'SentBy' => $user->ID,
                    'SentAt' => now(),
                    'Phone'  => $request->phone,
                    'Message' => $request->message,
                ]);

            DB::table('client_messages')->insert([
                'BL'          => $row->BL,
                'ConsigneeID' => $row->ConsigneeID,
                'channel'     => 'sms',
                'event'       => 'arrival',
                'phone'       => $request->phone,
                'message'     => $request->message,
                'status'      => 'sent',
                'sent_by'     => $user->ID,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        return response()->json([
            'success' => $result['success'],
            'message' => $result['success']
                ? 'SMS sent successfully.'
                : 'Failed to send SMS. Please try again.',
        ]);
    }

    // Send all pending rows using each row's current message/phone from JS
    public function sendAll(Request $request)
    {
        $request->validate([
            'rows' => ['required', 'array'],
            'rows.*.id'      => ['required', 'integer'],
            'rows.*.phone'   => ['required', 'string', 'max:20'],
            'rows.*.message' => ['required', 'string'],
        ]);

        $user    = Auth::user();
        $results = ['sent' => 0, 'failed' => 0];

        foreach ($request->rows as $item) {
            $row = DB::table('arrival_sms_queue')
                ->where('ID', $item['id'])
                ->where('Status', 0)
                ->first();

            if (!$row) continue;

            $result = $this->arkesel->sendSms($item['phone'], $item['message']);

            if ($result['success']) {
                DB::table('arrival_sms_queue')
                    ->where('ID', $item['id'])
                    ->update([
                        'Status'  => 1,
                        'SentBy'  => $user->ID,
                        'SentAt'  => now(),
                        'Phone'   => $item['phone'],
                        'Message' => $item['message'],
                    ]);

                DB::table('client_messages')->insert([
                    'BL'          => $row->BL,
                    'ConsigneeID' => $row->ConsigneeID,
                    'channel'     => 'sms',
                    'event'       => 'arrival',
                    'phone'       => $item['phone'],
                    'message'     => $item['message'],
                    'status'      => 'sent',
                    'sent_by'     => $user->ID,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);

                $results['sent']++;
            } else {
                $results['failed']++;
            }
        }

        return response()->json([
            'success' => true,
            'sent'    => $results['sent'],
            'failed'  => $results['failed'],
            'message' => "{$results['sent']} sent, {$results['failed']} failed.",
        ]);
    }
}
