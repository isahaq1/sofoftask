<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['*'],
    'allowed_origins_patterns' => [],
    'allowed_headers' => [
        'X-CSRF-TOKEN',
        'Content-Type',
        'X-Requested-With',
        'Authorization',
        'Accept',
    ],
    'exposed_headers' => ['X-CSRF-TOKEN'],
    'max_age' => 0,
    'supports_credentials' => true,
];
