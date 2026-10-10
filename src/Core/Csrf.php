<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Per-session CSRF token. Every state-changing form carries it; the router's
 * controllers verify it before touching data.
 */
final class Csrf
{
    private const KEY = '_csrf';

    public static function token(): string
    {
        if (empty($_SESSION[self::KEY]) || !is_string($_SESSION[self::KEY])) {
            $_SESSION[self::KEY] = bin2hex(random_bytes(32));
        }

        return $_SESSION[self::KEY];
    }

    public static function check(?string $token): bool
    {
        $expected = $_SESSION[self::KEY] ?? null;

        if (!is_string($expected) || $expected === '' || !is_string($token) || $token === '') {
            return false;
        }

        return hash_equals($expected, $token);
    }
}
