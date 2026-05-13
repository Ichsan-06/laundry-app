<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: [
            'callback/wijayapay',
        ]);

        $middleware->alias([
            'active.user' => \App\Http\Middleware\EnsureUserIsActive::class,
            'rbac' => \App\Http\Middleware\AccessControlMiddleware::class,
            'subscription' => \App\Http\Middleware\CheckSubscription::class,
            'plan.permission' => \App\Http\Middleware\CheckPlanPermission::class,
            'outlet.access' => \App\Http\Middleware\CheckOutletAccess::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
