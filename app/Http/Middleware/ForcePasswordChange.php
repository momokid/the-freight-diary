<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChange
{
    public function handle(Request $request, Closure $next): Response
    {
        // Only applies to logged in users
        if (Auth::check()) {

            $user = Auth::user();

            // If user must change password and is not already on the change password route
            // redirect them to the change password screen
            if (
                $user->must_change_password &&
                ! $request->routeIs('password.change') &&
                ! $request->routeIs('password.update') &&
                ! $request->routeIs('logout')
            ) {
                return redirect()->route('password.change');
            }
        }

        return $next($request);
    }
}
