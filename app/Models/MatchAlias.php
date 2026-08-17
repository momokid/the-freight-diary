<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MatchAlias extends Model
{
    protected $table      = 'match_aliases';
    protected $primaryKey = 'ID';
    public $timestamps    = false;

    protected $guarded = [];

    /** Normalised so casing and spacing do not create duplicate aliases. */
    public static function normalise(string $text): string
    {
        return trim(preg_replace('/\s+/', ' ', strtoupper($text)));
    }

    /** What this document text has previously been confirmed to mean. */
    public static function lookup(string $sourceKey, string $rawText): ?int
    {
        $id = static::where('SourceKey', $sourceKey)
            ->where('RawText', static::normalise($rawText))
            ->value('MatchedID');

        return $id ? (int) $id : null;
    }

    /** Record a user confirmation. Re-confirming an existing pair counts it. */
    public static function remember(string $sourceKey, string $rawText, int $matchedId, string $username, string $branchId): void
    {
        $rawText = static::normalise($rawText);

        if ($rawText === '' || $matchedId <= 0) {
            return;
        }

        $existing = static::where('SourceKey', $sourceKey)->where('RawText', $rawText)->first();

        if ($existing && (int) $existing->MatchedID === $matchedId) {
            $existing->increment('UseCount');
            return;
        }

        // A different answer replaces the old one — the newest correction wins.
        static::updateOrCreate(
            ['SourceKey' => $sourceKey, 'RawText' => $rawText],
            [
                'MatchedID' => $matchedId,
                'UseCount'  => 1,
                'Username'  => $username,
                'BranchID'  => $branchId,
                'Date'      => now()->toDateString(),
                'Time'      => now()->toDateTimeString(),
            ]
        );
    }
}
