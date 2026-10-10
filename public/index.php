<?php

declare(strict_types=1);

/*
 * Front controller. Every request enters here (Apache rewrite or the built-in
 * server's router.php). It prefers Composer's autoloader when present and falls
 * back to the bundled PSR-4 autoloader, so a bare checkout runs unchanged.
 */

define('BASE_PATH', dirname(__DIR__));

$composer = BASE_PATH . '/vendor/autoload.php';

if (is_file($composer)) {
    require $composer;
} else {
    require BASE_PATH . '/src/Support/autoload.php';
}

(new App\Core\App(BASE_PATH))->run();
