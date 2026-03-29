<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response =  $next($request);

        // Prevents your app from being embedded in iframes — stops clickjacking attacks
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Prevents browser from guessing content types — stops MIME sniffing attacks
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Enables browser's built-in XSS filter
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Controls referrer information sent with requests
        // strict-origin-when-cross-origin — sends full URL for same-origin, only origin for cross-origin
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Disables browser features we don't use — reduces attack surface
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');

        // Removes X-Powered-By header — hides that we're running PHP
        $response->headers->remove('X-Powered-By');

        return $response;
    }
}
