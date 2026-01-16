<?php

return [
    'config' => base_path('vendor/phpids/phpids/lib/IDS/default_config.ini'),
    'filter' => [
        'GET' => true,
        'POST' => true,
        'COOKIE' => true,
        'FILES' => false,
    ],
    'exception' => false, // true = Exceptions bei Angriffen
];
