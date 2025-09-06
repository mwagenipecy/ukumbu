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
        $middleware->alias([
            'client.vendor.access' => \App\Http\Middleware\ClientVendorAccess::class,
            'admin.access' => \App\Http\Middleware\AdminAccess::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Custom error page responses
        $exceptions->render(function (Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Page not found'], 404);
            }
            return response()->view('errors.404', [], 404);
        });

        $exceptions->render(function (Symfony\Component\HttpKernel\Exception\HttpException $e, $request) {
            $statusCode = $e->getStatusCode();
            
            if ($request->expectsJson()) {
                return response()->json(['error' => $e->getMessage()], $statusCode);
            }

            switch ($statusCode) {
                case 403:
                    return response()->view('errors.403', [], 403);
                case 404:
                    return response()->view('errors.404', [], 404);
                case 419:
                    return response()->view('errors.419', [], 419);
                case 500:
                    return response()->view('errors.500', [], 500);
                default:
                    return response()->view('errors.error', ['exception' => $e], $statusCode);
            }
        });

        // Handle general exceptions
        $exceptions->render(function (Throwable $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Internal server error'], 500);
            }
            return response()->view('errors.error', ['exception' => $e], 500);
        });
    })->create();
