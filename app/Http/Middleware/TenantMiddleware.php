<?php
// app/Http/Middleware/TenantMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class TenantMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $host = $request->getHost();
        $subdomain = SD();

//         Log::info("Subdomain gefunden: " . $subdomain);

        $tenant = Tenant::where('subdomain', $subdomain)->first();

        if (!$tenant) {
            abort(405, 'Tenant nicht gefunden.');
        }

//         Log::info("Tenant gefunden: " . $tenant->database);

        // DB config dynamisch anpassen
        config(['database.connections.mysql.database' => $tenant->database]);

        // Verbindung neu initialisieren, sonst bleibt alte DB
        DB::purge('mysql');
        DB::reconnect('mysql');

        // Optional: Tenant in Service Container für spätere Nutzung
        app()->instance('tenant', $tenant);

        return $next($request);
    }
}

