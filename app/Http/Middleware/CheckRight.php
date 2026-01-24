<?php

namespace App\Http\Middleware;

use Closure;

class CheckRight
{
    public function handle($request, Closure $next, string $right)
    {
        if (!CheckZRights($right)) {
            header("Location: /no-rights");
        exit;
        }

        return $next($request);
    }
}
