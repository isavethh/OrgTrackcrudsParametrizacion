<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        apiPrefix: 'api',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        /*
        |--------------------------------------------------------------------------
        | TRUST PROXIES (Nginx Proxy Manager)
        |--------------------------------------------------------------------------
        | Permite que Laravel reconozca HTTPS cuando viene desde un proxy
        */
        $middleware->trustProxies(
            at: '*',
            headers:
            Request::HEADER_X_FORWARDED_FOR |
            Request::HEADER_X_FORWARDED_PROTO |
            Request::HEADER_X_FORWARDED_HOST |
            Request::HEADER_X_FORWARDED_PORT
        );

        /*
        |--------------------------------------------------------------------------
        | MIDDLEWARE ALIASES
        |--------------------------------------------------------------------------
        */
        $middleware->alias([
            'jwt' => \App\Http\Middleware\JwtMiddleware::class,
            'jwt.auth' => \App\Http\Middleware\JwtToAuthMiddleware::class, // Bridge para Helpdesk
            'cors' => \App\Http\Middleware\CorsMiddleware::class,
        ]);

        /*
        |--------------------------------------------------------------------------
        | CORS GLOBAL PARA API
        |--------------------------------------------------------------------------
        */
        $middleware->api(prepend: [
            \App\Http\Middleware\CorsMiddleware::class,
        ]);
    })

    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
