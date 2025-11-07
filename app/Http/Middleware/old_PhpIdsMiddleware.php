<?php

namespace App\Http\Middleware;

use Closure;
use IDS\Init;
use IDS\Monitor;
use Illuminate\Http\Request;

class PhpIdsMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        \Log::info('PHPIDS Middleware reached');

        $configPath = config_path('phpids.ini');

        if (!file_exists($configPath)) {
            \Log::error('PHPIDS config not found: '.$configPath);
            return $next($request);
        }

        $init = new Init($configPath);

        // GET + POST + COOKIE prüfen
        $data = array_merge(
            $request->query->all(),
            $request->request->all(),
            $request->cookies->all()
        );
        \Log::info($data);

        $monitor = new Monitor($data, $init);
        $result = $monitor->run();

        if (!$result->isEmpty()) {
            // Log und Debug
            \Log::warning('PHPIDS Alert', [
                'request' => $data,
                'impact' => $result->getImpact(),
                'events' => $result->getEvents()
            ]);

            // Sofort blocken
            abort(403, 'PHPIDS detected a potential intrusion');
        }

        return $next($request);
    }
}
