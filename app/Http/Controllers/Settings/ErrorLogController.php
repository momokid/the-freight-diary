<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ErrorLogController extends Controller
{
    // Initial page load — just the shell, data comes via AJAX
    public function index()
    {
        return view('settings.error-log');
    }

    // AJAX data endpoint — used by tab switching and pagination
    public function data(Request $request)
    {
        $status = $request->query('status', 'new');
        $page   = (int) $request->query('page', 1);

        $query = DB::table('error_log')->orderByDesc('LastSeenAt');

        if ($status !== 'all') {
            $query->where('Status', $status);
        }

        $paginated = $query->paginate(20, ['*'], 'page', $page);

        $summary = [
            'new'          => DB::table('error_log')->where('Status', 'new')->count(),
            'acknowledged' => DB::table('error_log')->where('Status', 'acknowledged')->count(),
            'resolved'     => DB::table('error_log')->where('Status', 'resolved')->count(),
        ];

        return response()->json([
            'entries'    => $paginated->items(),
            'summary'    => $summary,
            'pagination' => [
                'currentPage' => $paginated->currentPage(),
                'lastPage'    => $paginated->lastPage(),
                'total'       => $paginated->total(),
            ],
        ]);
    }

    public function show(int $id)
    {
        $entry = DB::table('error_log')->where('ID', $id)->first();
        abort_if(! $entry, 404);

        return view('settings.error-log-show', compact('entry'));
    }

    public function updateStatus(Request $request, int $id)
    {
        $request->validate([
            'status' => ['required', 'in:new,acknowledged,resolved'],
        ]);

        DB::table('error_log')->where('ID', $id)->update(['Status' => $request->status]);

        return response()->json(['success' => true]);
    }
}
