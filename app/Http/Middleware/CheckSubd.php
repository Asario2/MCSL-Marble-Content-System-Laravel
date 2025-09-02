<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubd
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$allowed)
    {
        // Subdomain ermitteln
        $subb = SD();

        // Alias-Umwandlung
        $map = [
            "asario"        => "ab",
            "monikadargies" => "dag",
            "marblefx"      => "mfx",
            "mjs"           => "mjs",
            "ra-c-henning"  => "chh",
            "localhost"     => "ab",
            "ab"            => "ab",
            "mfx"            => "mfx",
            "chh"            => "chh",
            "dag"            => "dag",
            "mjs"            => "mjs",
        ];
        $subb = $map[$subb] ?? $subb;
        // \Log::   info('Subdomain: ' . $subb . ' | Allowed: ' . implode(',', $allowed));

        // Falls nur bestimmte Subdomains erlaubt sind
        if (!empty($allowed) && !in_array($subb, $allowed, true)) {
            abort(404, "Zugriff für Subdomain '{$subb}' nicht erlaubt.");
        }

        return $next($request);
    }
}
