<?php

namespace App\Http\Controllers;

use App\Models\Consignee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ConsigneeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $consignees = Consignee::active()
            ->when($search, function ($query) use ($search) {
                $query->where('FullName', 'like', "%{$search}%")
                    ->orWhere('TelNo', 'like', "%{$search}%");
            })
            ->orderBy('FullName')
            ->paginate(20)
            ->withQueryString();

        $inactiveConsignees = Consignee::inactive()
            ->orderBy('FullName')
            ->get();

        return view('consignees.index', compact('consignees', 'inactiveConsignees', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'FullName' => ['required', 'string', 'max:500'],
            'TelNo'    => ['required', 'string', 'max:30'],
            'Address1' => ['required', 'string', 'max:500'],
            'Address2' => ['nullable', 'string', 'max:500'],
            'Address3' => ['nullable', 'string', 'max:500'],
        ]);

        $consignee = Consignee::create([
            'FullName' => trim($request->FullName),
            'TelNo'    => trim($request->TelNo),
            'Address1' => trim($request->Address1),
            'Address2' => trim($request->Address2 ?? ''),
            'Address3' => trim($request->Address3 ?? ''),
            'Date'     => now()->toDateString(),
            'Time'     => now()->toDateTimeString(),
            'Username' => Auth::user()->ID,
            'Status'   => 1,
        ]);

        // If AJAX request (from quick add modal in consignment form)
        if ($request->expectsJson()) {
            return response()->json([
                'success'     => true,
                'message'     => 'Consignee added successfully.',
                'ConsigneeID' => $consignee->ConsigneeID,
                'FullName'    => $consignee->FullName,
            ]);
        }

        return redirect()->route('consignees.index')
            ->with('success', 'Consignee added successfully.');
    }

    public function show(int $id)
    {
        $consignee = DB::table('consignee_main')
            ->where('ConsigneeID', $id)
            ->select('ConsigneeID', 'FullName', 'TelNo')
            ->first();

        if (!$consignee) {
            return response()->json(['success' => false, 'message' => 'Consignee not found.'], 404);
        }

        return response()->json(['success' => true, 'consignee' => $consignee]);
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'FullName' => ['required', 'string', 'max:500'],
            'TelNo'    => ['required', 'string', 'max:30'],
            'Address1' => ['required', 'string', 'max:500'],
            'Address2' => ['nullable', 'string', 'max:500'],
            'Address3' => ['nullable', 'string', 'max:500'],
        ]);

        $consignee = Consignee::findOrFail($id);
        $consignee->FullName = trim($request->FullName);
        $consignee->TelNo    = trim($request->TelNo);
        $consignee->Address1 = trim($request->Address1);
        $consignee->Address2 = trim($request->Address2 ?? '');
        $consignee->Address3 = trim($request->Address3 ?? '');
        $consignee->save();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Consignee updated successfully.',
            ]);
        }

        return redirect()->route('consignees.index')
            ->with('success', 'Consignee updated successfully.');
    }

    public function deactivate(int $id)
    {
        $consignee         = Consignee::findOrFail($id);
        $consignee->Status = 0;
        $consignee->save();

        return response()->json([
            'success' => true,
            'message' => 'Consignee deactivated successfully.',
        ]);
    }

    public function restore(int $id)
    {
        $consignee         = Consignee::findOrFail($id);
        $consignee->Status = 1;
        $consignee->save();

        return response()->json([
            'success' => true,
            'message' => 'Consignee restored successfully.',
        ]);
    }

    // AJAX search for consignee dropdown in consignment form
    public function search(Request $request)
    {
        $consignees = Consignee::active()
            ->where('FullName', 'like', '%' . $request->q . '%')
            ->orderBy('FullName')
            ->limit(10)
            ->get(['ConsigneeID', 'FullName', 'TelNo']);

        return response()->json($consignees);
    }

    // returns only table rows for AJAX search
    public function table(Request $request)
    {
        $search = $request->input('search');

        $consignees = Consignee::active()
            ->when($search, function ($query) use ($search) {
                $query->where('FullName', 'like', "%{$search}%")
                    ->orWhere('TelNo', 'like', "%{$search}%");
            })
            ->orderBy('FullName')
            ->paginate(20)
            ->withQueryString();

        return view('consignees.table', compact('consignees'));
    }
}
