<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Canonical verb list for the Command Center's client-side mode detection.
 *
 * Kept out of AppServiceProvider deliberately — that provider is load-bearing
 * for every view, and a fault there fails silently across the whole app.
 */
class AgentVerbService
{
    private const CACHE_KEY = 'cc_verbs';
    private const TTL       = 3600;

    /** Lowercased verbs for the JS config object. */
    public static function forJs(): array
    {
        try {
            return Cache::remember(
                self::CACHE_KEY,
                self::TTL,
                fn() =>
                DB::table('agent_verb_synonyms')
                    ->where('Status', 1)
                    ->pluck('Verb')
                    ->map(fn($v) => mb_strtolower($v))
                    ->values()
                    ->all()
            );
        } catch (\Throwable $e) {
            report($e);
            return [];   // degrade quietly: badge stops flipping, nothing breaks
        }
    }

    /** Call after adding or editing a synonym. */
    public static function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
