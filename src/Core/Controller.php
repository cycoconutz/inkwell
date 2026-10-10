<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Base controller: the small set of outcomes every handler needs.
 */
abstract class Controller
{
    /**
     * @param array<string, mixed> $data
     */
    protected function view(string $template, array $data = [], string $layout = 'layouts/app'): void
    {
        View::render($template, $data, $layout);
    }

    protected function redirect(string $path, int $status = 302): never
    {
        header('Location: ' . $path, true, $status);
        exit;
    }

    protected function back(string $fallback = '/'): never
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? $fallback;
        header('Location: ' . $referer);
        exit;
    }

    /**
     * Reject a request whose CSRF token is missing or wrong.
     */
    protected function verifyCsrf(Request $request): void
    {
        if (!Csrf::check((string) $request->input('_token', ''))) {
            $this->abort(403);
        }
    }

    protected function json(mixed $data, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Abort with a status code, rendering the matching view when one exists.
     */
    protected function abort(int $status): never
    {
        http_response_code($status);
        $view = base_path("views/errors/{$status}.php");

        if (is_file($view)) {
            $this->view("errors/{$status}", [], 'layouts/app');
        } else {
            echo "{$status}";
        }

        exit;
    }
}
