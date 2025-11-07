<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class IntrusionDetectionMiddleware
{
    protected array $patterns = [
        // Basic XSS patterns
        '/<\s*script.*?>/i',
        '/on\w+\s*=\s*["\']?.*?["\']?/i',
        '/javascript\s*:/i',
        '/document\.cookie/i',
        '/window\.location/i',
        '/<\s*iframe/i',

        // SQL Injection patterns
        '/\bUNION\b.*\bSELECT\b/i',
        '/\b(SELECT|INSERT|UPDATE|DELETE|DROP)\b/i',
        '/(\bOR\b|\bAND\b)\s+\d+\s*=\s*\d+/i',
        '/(--|#|\/\*)/',
        '/sleep\s*\(\s*\d+\s*\)/i',

        // Command injection
        '/(;|&&|\|\|)\s*(ls|cat|whoami|curl|wget)/i',

        // Path traversal
        '/\.\.\//',
    ];

    protected bool $blockOnMatch = true;

    public function handle(Request $request, Closure $next)
    {
        // Rohdaten sammeln (keine json_encode, damit < und > erhalten bleiben)
        $rawParts = [];

        // rohe Query-String (z. B. test=%3Cscript%3E... wird hier noch URL-encoded sein)
        if ($qs = $request->getQueryString()) {
            // zusätzlich url-decode, damit %3C -> <
            $rawParts[] = urldecode($qs);
        }

        // raw body (für JSON payloads etc.)
        $rawBody = $request->getContent();
        if (!empty($rawBody)) {
            // wenn JSON, lasse es als-is; auch hier nicht json_encode
            $rawParts[] = $rawBody;
        }

        // Alle einzelnen input-Werte roh zusammenführen (keine json_encode)
        foreach ($request->all() as $k => $v) {
            if (is_array($v) || is_object($v)) {
                $rawParts[] = print_r($v, true);
            } else {
                $rawParts[] = (string) $v;
            }
        }

        // Dateien prüfen (z. B. Dateinamen)
        foreach ($request->allFiles() as $key => $file) {
            $rawParts[] = is_array($file) ? implode(' ', array_map(fn($f) => $f->getClientOriginalName(), $file)) : $file->getClientOriginalName();
        }

        // Zusammensetzen zu einem großen Plaintext
        $flatData = implode("\n", $rawParts);

        // Patterns prüfen
        foreach ($this->patterns as $pattern) {
            if (preg_match($pattern, $flatData)) {
                $this->logAttempt($request, $pattern, $flatData);

                if ($this->blockOnMatch) {
                    abort(403, 'Suspicious request blocked by IDS middleware.');
                }

                break;
            }
        }

        return $next($request);
    }

    protected function logAttempt(Request $request, string $pattern, string $data): void
    {
        Log::warning('🚨 IDS Alert: Possible intrusion detected.', [
            'ip' => $request->ip(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'pattern' => $pattern,
            // Achtung: große Payloads können Logs volllaufen lassen — bei Bedarf kürzen
            'payload_snippet' => substr($data, 0, 2000),
        ]);
    }
}
