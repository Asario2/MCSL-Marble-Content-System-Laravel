<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PhpIdsMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Test-Log: prüft, ob Middleware aufgerufen wird
//         \Log::info('PHPIDS Middleware reached');

        return $next($request);
    }
}
