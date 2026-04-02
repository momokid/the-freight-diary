<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\Pol;
use App\Models\Pod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PortController extends Controller
{
    public function index()
    {
        $pols = Pol::orderBy('POL_Name')->get();
        $pods = Pod::orderBy('POD_Name')->get();

        return view('master-data.ports', compact('pols', 'pods'));
    }

    // ── POL ──
    public function storePol(Request $request)
    {
        $request->validate([
            'POL_Name' => ['required', 'string', 'max:60'],
        ]);

        $pol = Pol::create([
            'POL_Name' => trim($request->POL_Name),
            'Username' => Auth::user()->ID,
            'Time'     => now()->toDateTimeString(),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success'  => true,
                'message'  => 'Port of Loading added successfully.',
                'POL_ID'   => $pol->POL_ID,
                'POL_Name' => $pol->POL_Name,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Port of Loading added successfully.']);
    }

    public function updatePol(Request $request, int $id)
    {
        $request->validate([
            'POL_Name' => ['required', 'string', 'max:60'],
        ]);

        $pol           = Pol::findOrFail($id);
        $pol->POL_Name = trim($request->POL_Name);
        $pol->save();

        return response()->json([
            'success'  => true,
            'message'  => 'Port of Loading updated successfully.',
            'POL_Name' => $pol->POL_Name,
        ]);
    }

    public function destroyPol(int $id)
    {
        Pol::findOrFail($id)->delete();

        return response()->json(['success' => true, 'message' => 'Port of Loading removed successfully.']);
    }

    // ── POD ──
    public function storePod(Request $request)
    {
        $request->validate([
            'POD_Name' => ['required', 'string', 'max:60'],
        ]);

        $pod = Pod::create([
            'POD_Name' => trim($request->POD_Name),
            'Username' => Auth::user()->ID,
            'Time'     => now()->toDateTimeString(),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success'  => true,
                'message'  => 'Port of Discharge added successfully.',
                'POD_ID'   => $pod->POD_ID,
                'POD_Name' => $pod->POD_Name,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Port of Discharge added successfully.']);
    }

    public function updatePod(Request $request, int $id)
    {
        $request->validate([
            'POD_Name' => ['required', 'string', 'max:60'],
        ]);

        $pod           = Pod::findOrFail($id);
        $pod->POD_Name = trim($request->POD_Name);
        $pod->save();

        return response()->json([
            'success'  => true,
            'message'  => 'Port of Discharge updated successfully.',
            'POD_Name' => $pod->POD_Name,
        ]);
    }

    public function destroyPod(int $id)
    {
        Pod::findOrFail($id)->delete();

        return response()->json(['success' => true, 'message' => 'Port of Discharge removed successfully.']);
    }
}
