<?php

declare(strict_types=1);

namespace App\Core;

/**
 * A tiny path router. Routes are registered with {param} placeholders and
 * dispatched to either a [Controller::class, 'method'] pair or a closure.
 * Handlers for controller routes are invoked as $method($request, ...$params).
 */
final class Router
{
    /** @var array<int, array{method:string, regex:string, params:array<int,string>, handler:callable|array{0:class-string,1:string}}> */
    private array $routes = [];

    /**
     * @param callable|array{0:class-string, 1:string} $handler
     */
    public function get(string $path, callable|array $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    /**
     * @param callable|array{0:class-string, 1:string} $handler
     */
    public function post(string $path, callable|array $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    /**
     * @param callable|array{0:class-string, 1:string} $handler
     */
    public function put(string $path, callable|array $handler): void
    {
        $this->add('PUT', $path, $handler);
    }

    /**
     * @param callable|array{0:class-string, 1:string} $handler
     */
    public function delete(string $path, callable|array $handler): void
    {
        $this->add('DELETE', $path, $handler);
    }

    /**
     * @param callable|array{0:class-string, 1:string} $handler
     */
    public function add(string $method, string $path, callable|array $handler): void
    {
        [$regex, $params] = $this->compile($path);

        $this->routes[] = [
            'method'  => strtoupper($method),
            'regex'   => $regex,
            'params'  => $params,
            'handler' => $handler,
        ];
    }

    /**
     * Dispatch the request. Returns true when a route matched, false otherwise
     * (the caller decides how to render a 404). Throws nothing on a miss.
     */
    public function dispatch(Request $request): bool
    {
        foreach ($this->routes as $route) {
            if ($route['method'] !== $request->method()) {
                continue;
            }

            if (!preg_match($route['regex'], $request->path(), $matches)) {
                continue;
            }

            array_shift($matches);

            $arguments = [];
            foreach ($route['params'] as $index => $name) {
                $arguments[$name] = $matches[$index] ?? null;
            }

            $handler = $route['handler'];

            if (is_array($handler)) {
                [$class, $method] = $handler;
                $controller = new $class();
                $controller->{$method}($request, ...array_values($arguments));
            } else {
                $handler($request, ...array_values($arguments));
            }

            return true;
        }

        return false;
    }

    /**
     * @return array{0:string, 1:array<int,string>}
     */
    private function compile(string $path): array
    {
        $params = [];

        $regex = preg_replace_callback(
            '/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/',
            static function (array $m) use (&$params): string {
                $params[] = $m[1];
                return '([^/]+)';
            },
            $path,
        );

        return ['#^' . $regex . '$#', $params];
    }
}
