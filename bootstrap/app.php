<?php

use App\Support\DatabaseConnectionDetector;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->statefulApi();

        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Throwable $exception, Request $request) {
            if (! DatabaseConnectionDetector::isConnectionIssue($exception)) {
                return null;
            }

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'No fue posible conectar con la base de datos.',
                    'code' => 'DATABASE_UNAVAILABLE',
                    'retryable' => true,
                ], 503);
            }

            $connection = (string) config('database.default', 'pgsql');
            $connectionConfig = (array) config("database.connections.{$connection}", []);

            return response()->view('errors.database-unavailable', [
                'databaseInfo' => [
                    'connection' => $connection,
                    'host' => (string) ($connectionConfig['host'] ?? 'n/a'),
                    'port' => (string) ($connectionConfig['port'] ?? 'n/a'),
                    'database' => (string) ($connectionConfig['database'] ?? 'n/a'),
                ],
                'timestamp' => now()->format('Y-m-d H:i:s'),
            ], 503);
        });
    })->create();
