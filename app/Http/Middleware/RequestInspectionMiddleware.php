<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Hlog;

class RequestInspectionMiddleware
{
    protected int $blockThreshold = 25;

    protected array $patterns = [
        // XSS
        '/<script\b/i'           => 15,
        '/javascript:/i'         => 10,
        '/on\w+\s*=/i'           => 8,

        // SQLi
        '/union\s+select/i'      => 12,
        '/select\s+.*\s+from/i'  => 8,
        '/or\s+1=1/i'            => 10,

        // Command Injection
        '/;\s*(rm|cat|wget|curl)\s+/i' => 20,
        '/\$\(/'                       => 15,

        // Path Traversal
        '/\.\.\//'               => 10,
        '/\.\.\\\\/'             => 10,
    ];

    public function handle(Request $request, Closure $next)
    {
        $score   = 0;
        $matches = [];

        /**
         * 🔥 WICHTIG:
         * RAW Querystring + decoded prüfen
         */
        $rawQuery = urldecode($request->getQueryString() ?? '');

        $inputs = array_merge(
            $request->query(),
            $request->post(),
            is_array($request->json()?->all()) ? $request->json()->all() : []
        );

        // RAW QUERYSTRING prüfen
        if ($rawQuery) {
            foreach ($this->patterns as $pattern => $points) {
                if (preg_match($pattern, $rawQuery)) {
                    $score += $points;
                    $matches[] = [
                        'source'  => 'raw_query',
                        'pattern' => $pattern,
                        'value'   => mb_substr($rawQuery, 0, 200),
                    ];
                }
            }
        }

        // Normale Inputs prüfen
        foreach ($inputs as $key => $value) {
            if (is_array($value)) {
                $value = json_encode($value);
            }

            $value = urldecode((string) $value);

            foreach ($this->patterns as $pattern => $points) {
                if (preg_match($pattern, $value)) {
                    $score += $points;
                    $matches[] = [
                        'source'  => $key,
                        'pattern' => $pattern,
                        'value'   => mb_substr($value, 0, 200),
                    ];
                }
            }
        }

        // 🔍 LOGGEN
        if ($score > 0) {
            HLog::save([
                'ip'      => $request->ip(),
                'url'     => $request->fullUrl(),
                'method'  => $request->method(),
                'score'   => $score,
                'matches' => $matches,
                'agent'   => $request->userAgent(),
            ]);
        }

        // ⛔ BLOCKIEREN
        if ($score >= $this->blockThreshold) {
            abort(403, 'Request blocked due to suspicious activity.');
        }

        return $next($request);
    }
}
