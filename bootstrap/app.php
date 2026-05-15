<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))

    // ── Providers ───────────────────────────────────────────

    ->withProviders([
        App\Providers\EventServiceProvider::class,
    ])

    // ── Routes ──────────────────────────────────────────────

    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',

        then: function () {

            Route::middleware('web')
                ->group(base_path('routes/admin.php'));
        },
    )

    // ── Middleware ──────────────────────────────────────────

    ->withMiddleware(function (Middleware $middleware): void {

        $middleware->alias([
            'admin' => \App\Http\Middleware\IsAdmin::class,
        ]);
    })

    // ── Exceptions ──────────────────────────────────────────

    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })

    ->withRouting(
    web: __DIR__ . '/../routes/web.php',
    api: __DIR__ . '/../routes/api.php',
    commands: __DIR__ . '/../routes/console.php',
    then: function () {
        \Illuminate\Support\Facades\Route::middleware('web')
            ->group(base_path('routes/admin.php'));
    },
)

    ->withMiddleware(function (\Illuminate\Foundation\Configuration\Middleware $middleware) {
    $middleware->alias([
        'admin' => \App\Http\Middleware\IsAdmin::class,
    ]);
})

    ->create();