<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class OcrCacheController extends Controller
{
    public function index()
    {
        $entries = DB::table('ocr_cache')
            ->orderByDesc('CreatedAt')
            ->paginate(25);

        $totalEntries = DB::table('ocr_cache')->count();
        $totalHits    = DB::table('ocr_cache')->sum('HitCount');

        $summary = [
            'totalEntries' => $totalEntries,
            'totalHits'    => $totalHits,
            'hitsSaved'    => $totalHits - $totalEntries,
            'byProvider'   => DB::table('ocr_cache')
                ->select('Provider', DB::raw('count(*) as count'))
                ->groupBy('Provider')
                ->pluck('count', 'Provider'),
        ];

        return view('settings.ocr-cache', compact('entries', 'summary'));
    }

    public function clearAll()
    {
        DB::table('ocr_cache')->delete();

        return response()->json([
            'success' => true,
            'message' => 'OCR cache cleared.',
        ]);
    }
}
