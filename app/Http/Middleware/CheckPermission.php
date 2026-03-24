<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\UserAuth;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = Auth::user();

        // No user logged in — redirect to login
        if (! $user) {
            return redirect()->route('login');
        }

        // Get the user's permissions from user_auth
        $userAuth = UserAuth::where('Username', $user->ID)->first();

        // No user_auth row exists — no permissions at all
        if (! $userAuth) {
            return redirect()->route('dashboard')
                ->with('error', 'You do not have permission to access that page.');
        }

        // Check if the specific permission is granted
        if (! $userAuth->hasPermission($permission)) {
            return redirect()->route('dashboard')
                ->with('error', 'You do not have permission to access that page.');
        }

        // Permission granted — allow the request through
        return $next($request);
    }
}