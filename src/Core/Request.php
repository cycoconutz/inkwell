<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Immutable-ish view of the incoming request. Built from the PHP superglobals by
 * Request::fromGlobals(), or constructed directly in tests.
 */
final class Request
{
    private function __construct(
        private string $method,
        private string $path,
        /** @var array<string, mixed> */
        private array $query,
        /** @var array<string, mixed> */
        private array $body,
        /** @var array<string, mixed> */
        private array $files,
        /** @var array<string, mixed> */
        private array $server,
    ) {
    }

    public static function fromGlobals(): self
    {
        $server = $_SERVER;
        $method = strtoupper((string) ($server['REQUEST_METHOD'] ?? 'GET'));
        $query  = $_GET;
        $body   = $_POST;

        $contentType = (string) ($server['CONTENT_TYPE'] ?? '');
        if (str_contains($contentType, 'application/json')) {
            $raw = file_get_contents('php://input') ?: '';
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                $body = array_merge($body, $decoded);
            }
        }

        // HTML forms cannot send PUT/PATCH/DELETE; a hidden _method field stands in.
        $override = $body['_method'] ?? null;
        if ($method === 'POST' && is_string($override) && $override !== '') {
            $method = strtoupper($override);
        }

        $uri  = (string) ($server['REQUEST_URI'] ?? '/');
        $path = (string) (parse_url($uri, PHP_URL_PATH) ?: '/');
        $path = rawurldecode($path);
        $path = self::normalizePath($path);

        return new self($method, $path, $query, $body, $_FILES, $server);
    }

    public function method(): string
    {
        return $this->method;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function isPost(): bool
    {
        return $this->method === 'POST';
    }

    public function isSecure(): bool
    {
        if (!empty($this->server['HTTPS']) && $this->server['HTTPS'] !== 'off') {
            return true;
        }
        return ($this->server['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';
    }

    public function input(string $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, $this->body)) {
            return $this->body[$key];
        }
        return $this->query[$key] ?? $default;
    }

    public function string(string $key, string $default = ''): string
    {
        $value = $this->input($key, $default);
        if (is_array($value)) {
            return $default;
        }
        return trim((string) $value);
    }

    public function int(string $key, int $default = 0): int
    {
        $value = $this->input($key, $default);
        return is_numeric($value) ? (int) $value : $default;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->body) || array_key_exists($key, $this->query);
    }

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return array_merge($this->query, $this->body);
    }

    /**
     * @param array<int, string> $keys
     * @return array<string, mixed>
     */
    public function only(array $keys): array
    {
        $all = $this->all();
        return array_intersect_key($all, array_flip($keys));
    }

    /**
     * @return array<string, mixed>|null
     */
    public function file(string $key): ?array
    {
        $file = $this->files[$key] ?? null;
        return is_array($file) ? $file : null;
    }

    public function ip(): string
    {
        return (string) ($this->server['REMOTE_ADDR'] ?? '');
    }

    public function userAgent(): string
    {
        return (string) ($this->server['HTTP_USER_AGENT'] ?? '');
    }

    public function header(string $name): ?string
    {
        $key = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
        return isset($this->server[$key]) ? (string) $this->server[$key] : null;
    }

    private static function normalizePath(string $path): string
    {
        if ($path === '' || $path === '/') {
            return '/';
        }

        $path = '/' . ltrim($path, '/');

        return $path === '/' ? '/' : rtrim($path, '/');
    }
}
