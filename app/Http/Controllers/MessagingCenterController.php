<?php

namespace App\Http\Controllers;

use App\Services\ClientNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MessagingCenterController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('client_messages as msgs')
            ->leftJoin('consignee_main as c', 'msgs.ConsigneeID', '=', 'c.ConsigneeID')
            ->select(
                'msgs.id',
                'msgs.BL',
                'msgs.ConsigneeID',
                'msgs.channel',
                'msgs.event',
                'msgs.phone',
                'msgs.message',
                'msgs.status',
                'msgs.sent_by',
                'msgs.created_at',
                'c.FullName as ConsigneeName'
            )
            ->orderByDesc('msgs.created_at');

        if ($request->filled('bl')) {
            $query->where('msgs.BL', 'like', '%' . strtoupper(trim($request->bl)) . '%');
        }

        if ($request->filled('event')) {
            $query->where('msgs.event', $request->event);
        }

        if ($request->filled('status')) {
            $query->where('msgs.status', $request->status);
        }

        $messages = $query->paginate(25)->withQueryString();

        return view('messaging.index', compact('messages'));
    }

    public function send(Request $request, ClientNotificationService $notification)
    {
        $request->validate([
            'bl'           => ['required', 'string', 'max:50'],
            'phone'        => ['required', 'string', 'max:20'],
            'consignee_id' => ['nullable', 'integer'],
            'event'        => ['required', 'in:registration,gate_out,invoice_payment,manual'],
            'client_code'  => ['nullable', 'string', 'size:4'],
            'message'      => ['required_if:event,manual', 'nullable', 'string'],
        ]);

        $user = Auth::user();

        $result = $notification->sendSMS(
            bl: strtoupper(trim($request->bl)),
            phone: $request->phone,
            event: $request->event,
            params: [
                'client_code' => $request->client_code,
                'message'     => $request->message,
            ],
            consigneeId: (int) ($request->consignee_id ?? 0),
            sentBy: $user->ID,
        );

        return response()->json($result, $result['success'] ? 200 : 500);
    }

    public function history(Request $request, string $bl)
    {
        $bl = strtoupper(trim($bl));

        $messages = DB::table('client_messages as msgs')
            ->leftJoin('consignee_main as c', 'msgs.ConsigneeID', '=', 'c.ConsigneeID')
            ->where('msgs.BL', $bl)
            ->select(
                'msgs.id',
                'msgs.BL',
                'msgs.channel',
                'msgs.event',
                'msgs.phone',
                'msgs.message',
                'msgs.status',
                'msgs.sent_by',
                'msgs.created_at',
                'c.FullName as ConsigneeName'
            )
            ->orderByDesc('msgs.created_at')
            ->get();

        return response()->json([
            'success'  => true,
            'messages' => $messages,
        ]);
    }
}
