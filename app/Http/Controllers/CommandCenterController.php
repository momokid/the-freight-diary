<?php

namespace App\Http\Controllers;

use App\Models\UserAuth;
use App\Services\EntityResolverService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommandCenterController extends Controller
{
    public function __construct(
        private EntityResolverService $resolver
    ) {}

    /**
     * Resolve a Command Center query into grouped, permission-filtered results.
     * Deliberately no permission middleware on the route — a user may be cleared
     * for consignments but not receipts, so filtering happens per group.
     */
    public function resolve(Request $request)
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
        ]);

        $q = strtoupper(trim($validated['q'] ?? ''));

        if (mb_strlen($q) < 2) {
            return response()->json(['groups' => []]);
        }

        $userAuth = UserAuth::where('Username', Auth::user()->ID)->first();

        if (! $userAuth) {
            return response()->json(['groups' => []]);
        }

        try {
            return response()->json([
                'groups' => $this->resolver->resolve($q, $userAuth),
            ]);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'groups'  => [],
                'message' => 'Search failed. Please try again.',
                'debug'   => app()->environment('local') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
