<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::user()->is_admin && !Auth::user()->is_customer) {
            Auth::guard('web')->logout();
            return redirect(route('home.user_is_no_admin'));
        }
        //
        return $next($request);
    }
}
