<?php

use Illuminate\Http\Request;
use App\Http\Middleware\CheckRight;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Routing\Exceptions\InvalidSignatureException;

/*
|--------------------------------------------------------------------------
| Automatische ENV-Datei anhand der Domain
|--------------------------------------------------------------------------
*/

    // $host = $_SERVER['HTTP_HOST'] ?? null;

    // $envMap = [
    //     'www.asario.de'     => '.env.ab',
    //     'asario.de'         => '.env.ab',

    //     'www.marblefx.net'  => '.env.mfx',
    //     'marblefx.net'      => '.env.mfx',

    //     'localhost'         => '.env',
    //     '127.0.0.1'         => '.env',
    // ];

    // // Default
    // $envFile = '.env';

    // // Web: nach Domain
    // if ($host && isset($envMap[$host])) {
    //     $envFile = $envMap[$host];
    // }

    // // CLI: ENV_FILE=.env.xxx php artisan ...
    // if (! empty($_SERVER['ENV_FILE'])) {
    //     $envFile = $_SERVER['ENV_FILE'];
    // }

    // // Laravel / Dotenv liest diese Variable
    // putenv("ENV_FILE={$envFile}");
    // $_SERVER['ENV_FILE'] = $envFile;
    // $_ENV['ENV_FILE']    = $envFile;

/*
|--------------------------------------------------------------------------
| Laravel Application
|--------------------------------------------------------------------------
*/




return Application::configure(basePath: dirname(__DIR__))

    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function ($middleware) {
        $middleware->alias([
            'right' => CheckRight::class,
        ]);
    })
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->validateCsrfTokens(except: [
            '/login-silent',
            '/comments/store/*/*',
        ]);
    })
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->alias([
            'remember'     => \Reinink\RememberQueryStrings::class,
            'is_admin'     => \App\Http\Middleware\UserIsAdmin::class,
            'is_employee'  => \App\Http\Middleware\UserIsEmployee::class,
            'is_customer'  => \App\Http\Middleware\UserIsCustomer::class,
        ]);
    })

    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (InvalidSignatureException $e, Request $request) {
            return redirect()->intended(route('home.invalid_signature'));
        });
    })

    ->create();
