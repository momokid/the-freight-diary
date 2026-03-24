<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use App\Models\UserAuth;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // ADDED: @canAccess('PermissionName') blade directive
        // Shows content only if the logged in user has that permission
        // Usage: @canAccess('UserPrivilege') ... @endCanAccess
        Blade::if('hasAccess', function (string $permission) {
            $user = Auth::user();

            if (! $user) {
                return false;
            }

            $userAuth = UserAuth::where('Username', $user->ID)->first();

            if (! $userAuth) {
                return false;
            }

            return $userAuth->hasPermission($permission);
        });
    }
}
