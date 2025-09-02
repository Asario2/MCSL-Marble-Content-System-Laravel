<?php
echo "=== Laravel White Screen Debug ===\n\n";

// 1. Prüfe PHP-Version
echo "PHP Version: " . phpversion() . "\n";

// 2. Prüfe wichtige PHP-Extensions
$required_ext = ['bcmath', 'mbstring', 'openssl', 'pdo_mysql', 'tokenizer', 'ctype', 'json'];
echo "\nPHP Extensions:\n";
foreach ($required_ext as $ext) {
    echo " - $ext: " . (extension_loaded($ext) ? "OK" : "MISSING") . "\n";
}

// 3. Prüfe Laravel Storage/Cache Berechtigungen
$paths = [
    'storage' => __DIR__ . '/storage',
    'bootstrap/cache' => __DIR__ . '/bootstrap/cache'
];

echo "\nFolder Permissions:\n";
foreach ($paths as $name => $path) {
    if (!is_writable($path)) {
        echo " - $name: NOT WRITABLE\n";
    } else {
        echo " - $name: writable\n";
    }
}

// 4. Prüfe ob wichtige Laravel-Klassen geladen werden können
$classes = [
    \Illuminate\Foundation\Application::class,
    \Barryvdh\Debugbar\ServiceProvider::class, // optional, falls installiert
    \Tightenco\Ziggy\ZiggyServiceProvider::class, // optional, falls installiert
];

echo "\nClass Checks:\n";
foreach ($classes as $class) {
    echo " - $class: " . (class_exists($class) ? "OK" : "NOT FOUND") . "\n";
}

// 5. Zeige die letzten 10 Laravel-Logeinträge
$log_file = glob(__DIR__ . '/storage/logs/laravel-*.log');
$latest_log = $log_file ? array_pop($log_file) : null;

if ($latest_log && file_exists($latest_log)) {
    echo "\nLast 10 log entries ($latest_log):\n";
    $lines = file($latest_log, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $lines = array_slice($lines, -10);
    foreach ($lines as $line) {
        echo " - $line\n";
    }
} else {
    echo "\nNo Laravel logs found.\n";
}

echo "\n=== Debug Complete ===\n";
