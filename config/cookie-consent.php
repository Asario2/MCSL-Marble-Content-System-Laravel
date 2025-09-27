<?php
return [
    'cookie' => [
        'name' => 'laravel_cookie_consent',
        'duration' => 525600,
        'path' => '/',
        'domain' => null,
        'secure' => false,
        'http_only' => false,
        'same_site' => 'lax', // ← Ändern!
        'domain' => '.marblefx.net', // Punkt vor der Domain = alle Subdomains

    ],


];
