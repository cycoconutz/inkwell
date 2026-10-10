<?php

declare(strict_types=1);

use App\Core\Config;
use App\Core\Csrf;
use App\Core\Session;
use App\Support\Env;

if (!function_exists('env')) {
    function env(string $key, mixed $default = null): mixed
    {
        return Env::get($key, $default);
    }
}

if (!function_exists('config')) {
    function config(string $key, mixed $default = null): mixed
    {
        return Config::get($key, $default);
    }
}

if (!function_exists('base_path')) {
    function base_path(string $path = ''): string
    {
        $base = defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__, 2);
        return $path === '' ? $base : $base . '/' . ltrim($path, '/');
    }
}

if (!function_exists('e')) {
    /**
     * Escape a value for safe output in HTML. This is the single choke point
     * every view goes through, so nothing reaches the page unescaped.
     */
    function e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('url')) {
    function url(string $path = ''): string
    {
        $base = rtrim((string) config('app.url', ''), '/');
        $path = '/' . ltrim($path, '/');
        return $path === '/' ? $base . '/' : $base . $path;
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        return url('assets/' . ltrim($path, '/'));
    }
}

if (!function_exists('redirect')) {
    function redirect(string $path, int $status = 302): never
    {
        header('Location: ' . $path, true, $status);
        exit;
    }
}

if (!function_exists('old')) {
    /**
     * A previous form value after a failed submission (set via Session::flash).
     */
    function old(string $key, mixed $default = ''): mixed
    {
        $old = Session::get('_old', []);
        return is_array($old) && array_key_exists($key, $old) ? $old[$key] : $default;
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        return Csrf::token();
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string
    {
        return '<input type="hidden" name="_token" value="' . e(Csrf::token()) . '">';
    }
}

if (!function_exists('flash')) {
    function flash(string $key, mixed $value): void
    {
        Session::flash($key, $value);
    }
}

if (!function_exists('page_meta')) {
    /**
     * Per-request page metadata (title, description). A view sets it while
     * rendering; the layout, captured afterwards, reads it.
     */
    function page_meta(string $key, ?string $value = null): string
    {
        /** @var array<string, string> $store */
        static $store = [];

        if ($value !== null) {
            $store[$key] = $value;
        }

        return $store[$key] ?? '';
    }
}

if (!function_exists('nav_active')) {
    /**
     * CSS class for a nav link matching the current path. "/" matches only the
     * home page; other prefixes match the section (and its descendants) unless
     * $exact is set.
     */
    function nav_active(string $prefix, bool $exact = false): string
    {
        $path   = (string) (parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');
        $path   = rtrim($path, '/');
        $path   = $path === '' ? '/' : $path;
        $prefix = rtrim($prefix, '/');
        $prefix = $prefix === '' ? '/' : $prefix;

        if ($prefix === '/') {
            $match = $path === '/';
        } elseif ($exact) {
            $match = $path === $prefix;
        } else {
            $match = $path === $prefix || str_starts_with($path, $prefix . '/');
        }

        return $match ? ' is-active' : '';
    }
}

if (!function_exists('page_title')) {
    function page_title(?string $title = null): string
    {
        if ($title !== null) {
            page_meta('title', $title);
        }

        $set = page_meta('title');

        return $set !== '' ? $set : (string) config('app.name', 'Inkwell');
    }
}

if (!function_exists('str_slug')) {
    function str_slug(string $value): string
    {
        $value = trim($value);
        if (function_exists('iconv')) {
            $converted = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
            if ($converted !== false) {
                $value = $converted;
            }
        }
        $value = strtolower($value);
        $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? '';
        return trim($value, '-');
    }
}

if (!function_exists('format_date')) {
    function format_date(?string $iso, string $format = 'M j, Y'): string
    {
        if ($iso === null || $iso === '') {
            return '';
        }
        try {
            return (new DateTimeImmutable($iso))->format($format);
        } catch (Exception) {
            return '';
        }
    }
}

if (!function_exists('reading_time')) {
    /**
     * Rough reading time in minutes, from a word count at ~200 wpm.
     */
    function reading_time(?string $text): int
    {
        $words = str_word_count(strip_tags((string) $text));
        return max(1, (int) ceil($words / 200));
    }
}
