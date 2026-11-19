<?php

use App\Http\Middleware\EnsureAdminAuthenticated;
use App\Http\Middleware\EnsureUserHasPermission;
use App\Http\Middleware\EnsureUserHasRole;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->prefix('admin')
                ->name('admin.')
                ->group(base_path('routes/admin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\TrackVisitorMiddleware::class,
        ]);

        $middleware->alias([
            'admin.auth' => EnsureAdminAuthenticated::class,
            'role' => EnsureUserHasRole::class,
            'permission' => EnsureUserHasPermission::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        // Resilient system error log hook: ensures all unhandled exceptions are visible in
        // Nginx / PHP-FPM error logs regardless of storage/logs directory permissions.
        $exceptions->report(function (\Throwable $e) {
            error_log(sprintf('[MRH ERROR] %s: %s in %s:%d', get_class($e), $e->getMessage(), $e->getFile(), $e->getLine()));
        });
    })->create();
