<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class GlobalRedirect
{
    public function handle(Request $request, Closure $next)
    {
        // Wenn Benutzer eingeloggt → alles normal weiter
        if (Auth::check()) {
            return $next($request);
        }

        // Ausnahmen: Login Route selbst oder API-Anfragen
        if ($request->routeIs('login') || $request->is('login') || $request->wantsJson()) {
            return $next($request);
        }

        // Inertia Request → Login Seite rendern (kein Redirect)
        if ($request->header('X-Inertia')) {
            return Inertia::render('Auth/Login')->toResponse($request);
        }

        // Normale HTTP Request → Redirect zu /login
        return redirect()->route('login');
    }
}
