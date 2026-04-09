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
        // Confiar en proxies (Cloudflare) para que HTTPS funcione correctamente
        $middleware->trustProxies(at: '*');

        // CORS para que el widget React pueda llamar a la API desde otros dominios
        $middleware->api(prepend: [
            \Illuminate\Http\Middleware\HandleCors::class,
        ]);
        $middleware->web(prepend: [
            \Illuminate\Http\Middleware\HandleCors::class,
        ]);
        // Partner license check — runs on all web requests, skips API/widget
        $middleware->web(append: [
            \App\Http\Middleware\PartnerLicenseCheck::class,
            \App\Http\Middleware\DebugPanelEntry::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
