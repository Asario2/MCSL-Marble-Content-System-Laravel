<?php

namespace App\Http\Middleware;

use Closure;

class CheckSubdomain
{
    public function handle($request, Closure $next, $allowedSubdomains)
    {
        $subdomain = SD();
        $allowedList = explode('|', $allowedSubdomains);

        if (!in_array($subdomain, $allowedList)) {
            abort(404);
        }

        return $next($request);
    }
}
