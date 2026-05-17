<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(function ($request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return null;
            }
            return null; // No web routes needing login redirect in this app
        });
        $middleware->api(prepend: [
            \Illuminate\Http\Middleware\HandleCors::class,
        ]);
        $middleware->alias([
            'role'       => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
        });
        $exceptions->render(function (\Spatie\Permission\Exceptions\UnauthorizedException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['message' => 'No tienes permiso para realizar esta acción.'], 403);
            }
        });
        $exceptions->render(function (\App\Exceptions\AlreadyClosedException $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        });
        $exceptions->render(function (\App\Exceptions\InsufficientStockException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        });
        $exceptions->render(function (\App\Exceptions\PaymentExceedsBalanceException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        });
        $exceptions->render(function (\App\Exceptions\ClientHasActiveOrdersException $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        });
    })->create();
