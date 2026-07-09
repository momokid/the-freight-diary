<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'permission' => \App\Http\Middleware\CheckPermission::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'webhook/whatsapp',
        ]);

        //force password change middleware runs on every authenticated request
        $middleware->appendToGroup('web', \App\Http\Middleware\ForcePasswordChange::class);

        // security headers on every response
        $middleware->appendToGroup('web', \App\Http\Middleware\SecurityHeaders::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->reportable(function (\Throwable $e) {
            $username = null;
            try {
                $username = \Illuminate\Support\Facades\Auth::check()
                    ? \Illuminate\Support\Facades\Auth::user()->ID
                    : null;
            } catch (\Throwable $authError) {
                // console context or auth not bootstrapped — leave username null
            }

            app(\App\Services\ErrorLogService::class)->logException(
                $e,
                request()?->path(),
                $username
            );
        });
    })->create();
