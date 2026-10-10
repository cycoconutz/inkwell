<?php

declare(strict_types=1);

/*
 * Configuration. Every value is read from the environment (with a safe
 * default), so the same image runs locally, in CI, and on Render.
 */

return [
    'app' => [
        'name'  => env('APP_NAME', 'Inkwell'),
        'env'   => env('APP_ENV', 'production'),
        'debug' => filter_var(env('APP_DEBUG', 'false'), FILTER_VALIDATE_BOOL),
        'url'   => rtrim((string) env('APP_URL', 'http://localhost:8080'), '/'),
        'key'   => (string) env('APP_KEY', ''),
    ],

    'db' => [
        'host'     => env('DB_HOST', '127.0.0.1'),
        'port'     => (int) env('DB_PORT', 3306),
        'database' => env('DB_DATABASE', 'inkwell'),
        'username' => env('DB_USERNAME', 'root'),
        'password' => env('DB_PASSWORD', ''),
        'charset'  => 'utf8mb4',
    ],

    'session' => [
        'name' => env('SESSION_NAME', 'inkwell_session'),
    ],

    'admin' => [
        'email'    => env('ADMIN_EMAIL', 'admin@inkwell.test'),
        'password' => env('ADMIN_PASSWORD', 'password'),
    ],

    'pagination' => [
        'per_page' => 6,
    ],
];
