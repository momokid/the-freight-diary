<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\Shipper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShipperController extends Controller
{
    public function index()
    {
        $shippers         = Shipper::active()->orderBy('ShipperName')->get();
        $inactiveShippers = Shipper::inactive()->orderBy('ShipperName')->get();

        return view('master-data.shippers', compact('shippers', 'inactiveShippers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ShipperName'  => ['required', 'string', 'max:150'],
            'AddressLine1' => ['required', 'string', 'max:500'],
            'AddressLine2' => ['nullable', 'string', 'max:500'],
            'AddressLine3' => ['nullable', 'string', 'max:500'],
            'AddressLine4' => ['nullable', 'string', 'max:500'],
        ]);

        //check for duplicate including inactive shippers
        $exists = Shipper::whereRaw('LOWER(ShipperName) = ?', [strtolower(trim($request->ShipperName))])->exists();
        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'A shipper with this name already exists.',
            ], 409);
        }

        $shipper = Shipper::create([
            'ShipperName'  => trim($request->ShipperName),
            'AddressLine1' => trim($request->AddressLine1),
            'AddressLine2' => trim($request->AddressLine2 ?? ''),
            'AddressLine3' => trim($request->AddressLine3 ?? ''),
            'AddressLine4' => trim($request->AddressLine4 ?? ''),
            'Username'     => Auth::user()->ID,
            'Date'         => now()->toDateString(),
            'Time'         => now()->toDateTimeString(),
            'Status'       => 1,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success'   => true,
                'message'   => 'Shipper added successfully.',
                'ShipperID' => $shipper->ShipperID,
                'ShipperName' => $shipper->ShipperName,
            ]);
        }

        return response()->json([
            'success'      => true,
            'message'      => 'Shipper added successfully.',
            'ShipperID'    => $shipper->ShipperID,
            'ShipperName'  => $shipper->ShipperName,
            'AddressLine1' => $shipper->AddressLine1,
            'AddressLine2' => $shipper->AddressLine2,
            'AddressLine3' => $shipper->AddressLine3,
            'AddressLine4' => $shipper->AddressLine4,
        ]);
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'ShipperName'  => ['required', 'string', 'max:150'],
            'AddressLine1' => ['required', 'string', 'max:500'],
            'AddressLine2' => ['nullable', 'string', 'max:500'],
            'AddressLine3' => ['nullable', 'string', 'max:500'],
            'AddressLine4' => ['nullable', 'string', 'max:500'],
        ]);

        //check for duplicate excluding current shipper
        $exists = Shipper::whereRaw('LOWER(ShipperName) = ?', [strtolower(trim($request->ShipperName))])
            ->where('ShipperID', '!=', $id)
            ->exists();
        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'A shipper with this name already exists.',
            ], 409);
        }

        $shipper = Shipper::findOrFail($id);
        $shipper->ShipperName  = trim($request->ShipperName);
        $shipper->AddressLine1 = trim($request->AddressLine1);
        $shipper->AddressLine2 = trim($request->AddressLine2 ?? '');
        $shipper->AddressLine3 = trim($request->AddressLine3 ?? '');
        $shipper->AddressLine4 = trim($request->AddressLine4 ?? '');
        $shipper->save();

        return response()->json(['success' => true, 'message' => 'Shipper updated successfully.']);
    }

    public function deactivate(int $id)
    {
        $shipper         = Shipper::findOrFail($id);
        $shipper->Status = 0;
        $shipper->save();

        return response()->json(['success' => true, 'message' => 'Shipper deactivated successfully.']);
    }

    public function restore(int $id)
    {
        $shipper         = Shipper::findOrFail($id);
        $shipper->Status = 1;
        $shipper->save();

        return response()->json(['success' => true, 'message' => 'Shipper restored successfully.']);
    }
}
