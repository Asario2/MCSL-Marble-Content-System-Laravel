<?php

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Str;

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = Illuminate\Http\Request::capture();
$kernel->handle($request);

// ---- Session Script ----
$sessionId = Str::random(40); // zufällige Laravel Session-ID

$data = [
    '_token' => csrf_token(),
    'foo' => 'bar',
];

file_put_contents(storage_path("framework/sessions/{$sessionId}"), serialize($data));

setcookie('laravel_session', $sessionId, time() + 3600, '/', '.marblefx.net', true, true);

echo "Session {$sessionId} erstellt!";
