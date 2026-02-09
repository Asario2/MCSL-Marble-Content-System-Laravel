<?php

use Illuminate\Http\Request;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Routing\Exceptions\InvalidSignatureException;

use App\Http\Middleware\CheckRight;
use App\Http\Middleware\RequestInspectionMiddleware;
// use App\Http\Middleware\DetectTenant;
use App\Http\Middleware\CheckSubd;

return Application::configure(basePath: dirname(__DIR__))

    // -------------------------------------------------
    // Routing
    // -------------------------------------------------
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )

    // -------------------------------------------------
    // Middleware
    // -------------------------------------------------
    ->withMiddleware(function (Middleware $middleware) {

        // 🔥 GLOBALE Middleware (alle Requests, auch 404, Assets etc.)
        $middleware->append([
            \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
            \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
            \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
            \Illuminate\Http\Middleware\HandleCors::class,
            \Illuminate\Foundation\Http\Middleware\TrimStrings::class,
            \Illuminate\Http\Middleware\TrustProxies::class,
            App\Http\Middleware\BlockBannedIPs::class,



            // 🔐 IDS / Inspection
            RequestInspectionMiddleware::class,
        ]);

        // 🌍 WEB Middleware Stack
        $middleware->web(append: [
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,

            // DetectTenant::class,
            CheckSubd::class,

            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        // 🏷 Route Middleware Aliases
        $middleware->alias([
            'right'        => CheckRight::class,
            // 'tenant'       => DetectTenant::class,
            'inspect'      => RequestInspectionMiddleware::class,
            'is_admin'     => \App\Http\Middleware\UserIsAdmin::class,
            'is_employee'  => \App\Http\Middleware\UserIsEmployee::class,
            'is_customer'  => \App\Http\Middleware\UserIsCustomer::class,
        ]);

        // 🚫 CSRF Ausnahmen
        $middleware->validateCsrfTokens(except: [
            '/login-silent',
            '/comments/store/*/*',
        ]);
    })

    // -------------------------------------------------
    // Exceptions
    // -------------------------------------------------
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (InvalidSignatureException $e, Request $request) {
            return redirect()->intended(route('home.invalid_signature'));
        });
    })

    ->create();
