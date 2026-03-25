<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use App\Models\UserAuth;
use App\Models\User;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
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
