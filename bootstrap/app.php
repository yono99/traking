<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Menambahkan middleware untuk web
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Penanganan pengecualian, jika diperlukan
    })
    ->create();

// Mendaftarkan route middleware
$app->routeMiddleware([
    'role' => \App\Http\Middleware\CheckRole::class, // Menggunakan namespace lengkap
    'loket' => \App\Http\Middleware\LoketMiddleware::class,
    'checkUnit' => \App\Http\Middleware\CheckUnit::class,
    'main' => \App\Http\Middleware\CheckUnit::class,
    
]);
