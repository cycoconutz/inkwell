<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

/**
 * Shared read helpers. The table name is a class constant (never user input),
 * so interpolating it into SQL is safe; all values are bound.
 */
abstract class Model
{
    protected static string $table = '';

    /**
     * @return array<string, mixed>|null
     */
    public static function find(int|string $id): ?array
    {
        return Database::selectOne('SELECT * FROM ' . static::$table . ' WHERE id = ? LIMIT 1', [$id]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function all(string $orderBy = 'id ASC'): array
    {
        return Database::select('SELECT * FROM ' . static::$table . ' ORDER BY ' . $orderBy);
    }

    public static function count(): int
    {
        $row = Database::selectOne('SELECT COUNT(*) AS c FROM ' . static::$table);
        return (int) ($row['c'] ?? 0);
    }
}
