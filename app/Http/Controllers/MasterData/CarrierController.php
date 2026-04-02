<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\Carrier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CarrierController extends Controller
{
    public function index()
    {
        $carriers         = Carrier::active()->orderBy('CarrierName')->get();
        $inactiveCarriers = Carrier::inactive()->orderBy('CarrierName')->get();

        return view('master-data.carriers', compact('carriers', 'inactiveCarriers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'CarrierName' => ['required', 'string', 'max:500'],
        ]);

        // check for duplicate including inactive carriers
        $exists = Carrier::withoutGlobalScopes()
            ->whereRaw('LOWER(CarrierName) = ?', [strtolower(trim($request->CarrierName))])
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'A carrier with this name already exists.',
            ], 409);
        }

        $carrier = Carrier::create([
            'CarrierName' => trim($request->CarrierName),
            'Username'    => Auth::user()->ID,
            'Time'        => now()->toDateTimeString(),
            'Status'      => 1,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success'     => true,
                'message'     => 'Carrier added successfully.',
                'CarrierID'   => $carrier->CarrierID,
                'CarrierName' => $carrier->CarrierName,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Carrier added successfully.']);
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'CarrierName' => ['required', 'string', 'max:500'],
        ]);

        // check for duplicate including inactive carriers
        $exists = Carrier::withoutGlobalScopes()
            ->whereRaw('LOWER(CarrierName) = ?', [strtolower(trim($request->CarrierName))])
            ->where('CarrierID', '!=', $id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'A carrier with this name already exists.',
            ], 409);
        }

        $carrier = Carrier::findOrFail($id);
        $carrier->CarrierName = trim($request->CarrierName);
        $carrier->save();

        return response()->json([
            'success'     => true,
            'message'     => 'Carrier updated successfully.',
            'CarrierName' => $carrier->CarrierName,
        ]);
    }

    public function deactivate(int $id)
    {
        $carrier         = Carrier::findOrFail($id);
        $carrier->Status = 0;
        $carrier->save();

        return response()->json(['success' => true, 'message' => 'Carrier deactivated successfully.']);
    }

    public function restore(int $id)
    {
        $carrier         = Carrier::findOrFail($id);
        $carrier->Status = 1;
        $carrier->save();

        return response()->json(['success' => true, 'message' => 'Carrier restored successfully.']);
    }
}
