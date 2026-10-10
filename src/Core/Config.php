<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Dot-notation access to the loaded configuration array.
 *
 * Config::get('app.name') / Config::get('db.host', 'localhost').
 */
final class Config
{
    /** @var array<string, mixed> */
    private static array $items = [];

    /**
     * @param array<string, mixed> $items
     */
    public static function load(array $items): void
    {
        self::$items = $items;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $value = self::$items;

        foreach (explode('.', $key) as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }

    /**
     * @return array<string, mixed>
     */
    public static function all(): array
    {
        return self::$items;
    }
}
