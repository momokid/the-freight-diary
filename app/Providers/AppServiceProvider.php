<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use App\Models\UserAuth;
use App\Models\User;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        //rate limiter for login — 5 attempts per minute per IP + User ID combination
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)
                ->by($request->input('ID') . '|' . $request->ip())
                ->response(function () {
                    return back()->withErrors([
                        'ID' => 'Too many login attempts. Please wait 60 seconds before trying again.',
                    ]);
                });
        });

        // rate limiter for forgot password — 3 attempts per minute per IP
        RateLimiter::for('forgot-password', function (Request $request) {
            return Limit::perMinute(3)
                ->by($request->ip())
                ->response(function () {
                    return back()->with(
                        'reset_success',
                        'Your request has been submitted. Please contact your administrator.'
                    );
                });
        });

        //cache results so DB is only hit once per request, not once per view
        View::composer('*', function ($view) {
            if (Auth::check()) {
                static $pendingResetCount = null;
                static $userAuth = null;

                if ($pendingResetCount === null) {
                    $pendingResetCount = User::where('reset_requested', 1)->count();
                }

                if ($userAuth === null) {
                    $userAuth = UserAuth::where('Username', Auth::user()->ID)->first();
                }

                $view->with('pendingResetCount', $pendingResetCount);
                $view->with('userAuth', $userAuth);
            }
        });
    }
}
