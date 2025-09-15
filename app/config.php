<?php

return [
    'vite' => [
        'host' => env('VITE_HOST', 'http://localhost:5173'),
        'manifest_path' => env('VITE_MANIFEST_PATH', 'dist/.vite/manifest.json'),
        'dist_path' => env('VITE_DIST_PATH', 'dist'),
    ],

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

    'cache' => [
        'ttl' => env('CACHE_TTL', 60 * 60 * 24),
    ],
];
