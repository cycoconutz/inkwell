<?php

declare(strict_types=1);

/*
 * Dependency-free PSR-4 autoloader for the App\ namespace.
 *
 * The application ships with zero runtime Composer dependencies, so it also
 * runs from a bare checkout (and inside the Apache image) without `vendor/`.
 * If Composer's generated autoloader happens to be present, index.php prefers
 * it and this file is still harmless.
 */

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $path = __DIR__ . '/../' . str_replace('\\', '/', $relative) . '.php';

    if (is_file($path)) {
        require $path;
    }
});

require_once __DIR__ . '/helpers.php';
