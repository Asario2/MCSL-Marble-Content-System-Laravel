<?php

return [
    'config' => base_path('./phpids.ini'),
    'filter' => [
        'GET' => true,
        'POST' => true,
        'COOKIE' => true,
        'FILES' => false,
    ],
    'exception' => false, // true = Exceptions bei Angriffen
];
