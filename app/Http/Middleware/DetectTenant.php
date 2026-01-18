<?php
// app/Http/Middleware/DetectTenant.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant;

class DetectTenant {
    public function handle($request, Closure $next)
    {
        $host = $request->getHost(); // z. B. mfx.localhost.de
        $subdomain = SD();

        $tenant = Tenant::where('subdomain', $subdomain)->first();

        if ($tenant) {
//             Log::info('Mandant erkannt: ' . $subdomain . ' → ' . $tenant->database);

            // DB umschalten
            config(['database.connections.mysql.database' => $tenant->database]);
            DB::purge('mysql');
            DB::reconnect('mysql');

            // Optional: Tenant global verfügbar machen
            app()->instance('tenant', $tenant);
        } else {
            Log::warning('Kein Mandant gefunden für Subdomain: ' . $subdomain);
            abort(404, 'Mandant nicht gefunden');
        }

        return $next($request);
    }
}
