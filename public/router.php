<?php

declare(strict_types=1);

/*
 * Router script for PHP's built-in server:
 *   php -S localhost:8080 -t public public/router.php
 * It serves real files (CSS, images) directly and sends everything else to the
 * front controller.
 */

$path = (string) (parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');
$file = __DIR__ . $path;

if ($path !== '/' && is_file($file)) {
    return false; // let the built-in server serve the asset
}

require __DIR__ . '/index.php';
