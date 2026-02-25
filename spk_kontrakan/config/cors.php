<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi CORS yang lebih aman dan proper.
    | Mengizinkan akses dari mobile app dan localhost development.
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],

    'allowed_origins' => [
        'http://localhost',
        'http://localhost:8000',
        'http://127.0.0.1',
        'http://127.0.0.1:8000',
        'http://10.0.2.2:8000',       // Android Emulator
        'http://192.168.*',            // Local network
    ],

    'allowed_origins_patterns' => [
        '#^http://192\.168\.\d+\.\d+(:\d+)?$#',  // Local network devices
    ],

    'allowed_headers' => [
        'Content-Type',
        'X-Requested-With',
        'Authorization',
        'Accept',
        'Origin',
    ],

    'exposed_headers' => [
        'X-RateLimit-Limit',
        'X-RateLimit-Remaining',
        'Retry-After',
    ],

    'max_age' => 86400, // Cache preflight for 24 hours

    'supports_credentials' => true,

];
