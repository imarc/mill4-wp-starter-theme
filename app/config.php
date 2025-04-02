<?php

return [
    'sessions' => [
        'enabled' => env('SESSIONS_ENABLED', false),
        'cookie' => env('SESSIONS_COOKIE', 'session'),
        'path' => env('SESSIONS_PATH', '/'),
        'domain' => env('SESSIONS_DOMAIN', ''),
        'secure' => env('SESSIONS_SECURE', false),
        'httponly' => env('SESSIONS_HTTPONLY', true),
        'samesite' => env('SESSIONS_SAMESITE', 'Lax'),
        'lifetime' => env('SESSIONS_LIFETIME', 0),
    ],
];
