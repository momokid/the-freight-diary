<?php

namespace App\Http\Controllers;

use App\Services\ReceiptService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WaybillController extends Controller
{
    public function index()
    {
        return view('invoices.waybill');
    }

    public function search(Request $request)
    {
        $request->validate(['q' => ['required', 'string', 'min:2']]);

        $q = trim($request->q);

        $results = DB::table('waybill_main')
            ->where(function ($query) use ($q) {
                $query->where('Consignee', 'like', "%{$q}%")
                      ->orWhere('VehicleNo', 'like', "%{$q}%");
            })
            ->orderByDesc('Date')
            ->limit(10)
            ->get();

        return response()->json($results);
    }

    public function store(Request $request)
    {
        $request->validate([
            'Consignee'     => ['required', 'string', 'max:125'],
            'VehicleNo'     => ['required', 'string', 'max:15'],
            'DriverName'    => ['required', 'string', 'max:125'],
            'Port'          => ['required', 'string', 'max:25'],
            'DriverLicense' => ['required', 'string', 'max:20'],
            'Package'       => ['required', 'string'],
            'Description'   => ['required', 'string'],
            'Quantity'      => ['required', 'integer', 'min:1'],
            'WaybillDate'   => ['required', 'date'],
        ]);

        $user = Auth::user();

        $id = DB::table('waybill_main')->insertGetId([
            'Consignee'     => trim($request->Consignee),
            'VehicleNo'     => strtoupper(trim($request->VehicleNo)),
            'DriverName'    => trim($request->DriverName),
            'Port'          => trim($request->Port),
            'DriverLicense' => trim($request->DriverLicense),
            'Package'       => trim($request->Package),
            'Description'   => trim($request->Description),
            'Quantity'      => $request->Quantity,
            'WaybillDate'   => $request->WaybillDate,
            'Username'      => $user->ID,
            'Date'          => now()->toDateString(),
            'Time'          => now()->toDateTimeString(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Waybill saved successfully.',
            'id'      => $id,
        ]);
    }

    public function report(int $id)
    {
        $waybill = DB::table('waybill_main')->where('id', $id)->first();

        if (!$waybill) {
            abort(404, 'Waybill not found.');
        }

        return view('invoices.waybill-report', compact('waybill'));
    }
}