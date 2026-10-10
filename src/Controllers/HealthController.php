<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;

/**
 * Liveness/readiness probe for the platform (Render health checks).
 */
final class HealthController extends Controller
{
    public function index(Request $request): void
    {
        $database = true;

        try {
            Database::selectOne('SELECT 1 AS ok');
        } catch (\Throwable) {
            $database = false;
        }

        $this->json([
            'status'   => $database ? 'ok' : 'degraded',
            'database' => $database,
            'time'     => date('c'),
        ], $database ? 200 : 503);
    }
}
