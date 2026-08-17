<?php

namespace App\Http\Controllers;

use App\Models\MatchAlias;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MatchAliasController extends Controller
{
    private const SOURCES = ['consignee', 'carrier', 'shipper', 'pol', 'pod'];

    /** A user confirming or correcting an OCR match teaches the next one. */
    public function store(Request $request)
    {
        $request->validate([
            'SourceKey' => ['required', 'string', 'in:' . implode(',', self::SOURCES)],
            'RawText'   => ['required', 'string', 'max:255'],
            'MatchedID' => ['required', 'integer', 'min:1'],
        ]);

        $user = Auth::user();

        MatchAlias::remember(
            $request->SourceKey,
            $request->RawText,
            (int) $request->MatchedID,
            $user->ID,
            $user->BranchID
        );

        return response()->json(['success' => true]);
    }
}
