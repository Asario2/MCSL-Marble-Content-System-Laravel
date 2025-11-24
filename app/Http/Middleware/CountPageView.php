<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\PageView;
use Illuminate\Support\Facades\Auth;

class CountPageView
{
    public function handle(Request $request, Closure $next)
    {
        // Nur wenn kein Auth-User angemeldet ist oder Route öffentlich
        $route = $request->route();
        if ($route && (!$route->middleware() || !in_array('auth', $route->middleware()))) {

            $ip = $request->ip();
            // Anonymisiere IP: letzte Oktett auf 0 setzen
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                $ipParts = explode('.', $ip);
                $ipParts[3] = '0';
                $ip = implode('.', $ipParts);
            } elseif (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
                $ipParts = explode(':', $ip);
                $ipParts[count($ipParts) - 1] = '0000';
                $ip = implode(':', $ipParts);
            }

            PageView::create([
                'url' => $request->fullUrl(),
                'referrer' => $request->headers->get('referer'),
                'ip' => $ip,
                'visited_at' => now(),
            ]);
        }

        return $next($request);
    }
}
