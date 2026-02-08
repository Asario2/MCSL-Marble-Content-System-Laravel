<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\HlogService;

class RequestInspectionMiddleware
{
    protected int $maxScore = 25; // Score ab dem gebannt wird
    protected HlogService $hlogService;

    protected array $patterns = [
        // XSS
        '/<script\b/i'   => 15,
        '/javascript:/i' => 10,
        '/on\w+\s*=/i'   => 8,

        // SQL Injection
        '/union\s+select/i' => 12,
        '/select\s+.*\s+from/i' => 8,
        '/or\s+1=1/i'         => 10,

        // Command Injection
        '/;\s*(rm|cat|wget|curl)\s+/i' => 20,
        '/\$\(/'                       => 15,

        // Path Traversal
        '/\.\.\//'      => 10,
        '/\.\.\\\\/'    => 10,
    ];

    public function __construct(HlogService $hlogService)
    {
        $this->hlogService = $hlogService;
    }

    public function handle(Request $request, Closure $next)
    {
        $score = 0;
        $matches = [];

        // Alle GET/POST/JSON Inputs prüfen
        $inputs = array_merge(
            $request->query(),
            $request->post(),
            is_array($request->json()?->all()) ? $request->json()->all() : []
        );

        foreach ($inputs as $key => $value) {
            if (is_array($value)) $value = json_encode($value);
            $value = urldecode((string)$value);

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

        // RAW QueryString prüfen
        $raw = urldecode($request->getQueryString() ?? '');
        if ($raw) {
            foreach ($this->patterns as $pattern => $points) {
                if (preg_match($pattern, $raw)) {
                    $score += $points;
                    $matches[] = [
                        'source'  => 'raw_query',
                        'pattern' => $pattern,
                        'value'   => mb_substr($raw, 0, 200),
                    ];
                }
            }
        }

        // 🔔 Immer loggen, wenn Score > 0
        $banUntil = null;
        if ($score > 0) {
            $banUntil = $this->hlogService->logHit(
                $request->ip(),
                $request,
                $score,
                $matches
            );
        }

        // 🚨 Ban sofort, wenn Score >= maxScore
        if ($score >= $this->maxScore && $banUntil) {
            abort(403, "Request blocked. IP banned until {$banUntil->toDateTimeString()}");
        }

        return $next($request);
    }
    function getScore($ip,$request)
    {
              // Alle GET/POST/JSON Inputs prüfen
              $score = 0;
        $inputs = array_merge(
            $request->query(),
            $request->post(),
            is_array($request->json()?->all()) ? $request->json()->all() : []
        );

        foreach ($inputs as $key => $value) {
            if (is_array($value)) $value = json_encode($value);
            $value = urldecode((string)$value);

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

        // RAW QueryString prüfen
        $raw = urldecode($request->getQueryString() ?? '');
        if ($raw) {
            foreach ($this->patterns as $pattern => $points) {
                if (preg_match($pattern, $raw)) {
                    $score += $points;
                    $matches[] = [
                        'source'  => 'raw_query',
                        'pattern' => $pattern,
                        'value'   => mb_substr($raw, 0, 200),
                    ];
                }
            }
        }
        return $score;
    }
}
