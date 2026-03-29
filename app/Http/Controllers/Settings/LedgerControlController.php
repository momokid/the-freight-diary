<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\LedgerControl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LedgerControlController extends Controller
{
    // Show the ledger control page
    public function index()
    {
        $activeControls   = LedgerControl::active()->orderBy('ControlID')->get();
        $inactiveControls = LedgerControl::inactive()->orderBy('ControlID')->get();

        return view('settings.ledger-control', compact('activeControls', 'inactiveControls'));
    }

    // Store a new ledger control
    public function store(Request $request)
    {
        $request->validate([
            'ControlName' => ['required', 'string', 'max:100', 'unique:ledger_control,ControlName'],
        ]);

        LedgerControl::create([
            'ControlName' => ucwords(strtolower(trim($request->ControlName))),
            'Username'    => Auth::user()->ID,
            'Date'        => now()->toDateString(),
            'Time'        => now()->toDateTimeString(),
            'Status'      => 1,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ledger control added successfully.',
        ]);
    }

    // Update a ledger control name inline
    public function update(Request $request, int $id)
    {
        $request->validate([
            'ControlName' => [
                'required',
                'string',
                'max:100',
                // unique but ignore the current record
                "unique:ledger_control,ControlName,{$id},ControlID",
            ],
        ]);

        $control = LedgerControl::findOrFail($id);
        $control->ControlName = ucwords(strtolower(trim($request->ControlName)));
        $control->save();

        return response()->json([
            'success'     => true,
            'message'     => 'Ledger control updated successfully.',
            'ControlName' => $control->ControlName,
        ]);
    }

    // Deactivate a ledger control — soft delete
    public function deactivate(int $id)
    {
        $control = LedgerControl::findOrFail($id);
        $control->Status = 0;
        $control->save();

        return response()->json([
            'success' => true,
            'message' => 'Ledger control deactivated successfully.',
        ]);
    }

    // Restore a deactivated ledger control
    public function restore(int $id)
    {
        $control = LedgerControl::findOrFail($id);
        $control->Status = 1;
        $control->save();

        return response()->json([
            'success' => true,
            'message' => 'Ledger control restored successfully.',
        ]);
    }
}
