<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RequestInspectionMiddleware
{
    /**
     * Score-Schwellenwert
     */
    protected int $blockThreshold = 25;

    /**
     * Verdächtige Patterns
     */
    protected array $patterns = [
        // XSS
        '/<script\b/i'           => 10,
        '/onerror\s*=/i'         => 8,
        '/onload\s*=/i'          => 8,
        '/javascript:/i'         => 10,

        // SQL Injection
        '/union\s+select/i'      => 12,
        '/select\s+.*\s+from/i'  => 8,
        '/drop\s+table/i'        => 15,
        '/or\s+1=1/i'            => 10,

        // Command Injection
        '/;\s*rm\s+/i'           => 20,
        '/\|\s*sh/i'             => 15,
        '/\$\(/'                 => 10,

        // Path Traversal
        '/\.\.\//'               => 10,
        '/\.\.\\\\/'             => 10,
    ];

    public function handle(Request $request, Closure $next)
    {
        $score = 0;
        $matches = [];

        // Sammle alle Inputquellen
        $inputs = array_merge(
            $request->query(),
            $request->post(),
            is_array($request->json()?->all()) ? $request->json()->all() : []
        );

        foreach ($inputs as $key => $value) {
            if (is_array($value)) {
                $value = json_encode($value);
            }

            foreach ($this->patterns as $pattern => $points) {
                if (preg_match($pattern, (string)$value)) {
                    $score += $points;
                    $matches[] = [
                        'field'   => $key,
                        'pattern' => $pattern,
                        'value'   => mb_substr((string)$value, 0, 150),
                    ];
                }
            }
        }

        // Loggen ab Score > 0
        if ($score > 0) {
            Log::warning('RequestInspection triggered', [
                'ip'      => $request->ip(),
                'url'     => $request->fullUrl(),
                'method'  => $request->method(),
                'score'   => $score,
                'matches' => $matches,
                'agent'   => $request->userAgent(),
            ]);
        }

        // BLOCKIEREN (optional)
        if ($score >= $this->blockThreshold) {
            abort(403, 'Request blocked due to suspicious activity.');
        }

        return $next($request);
    }
}
