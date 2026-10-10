<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use RuntimeException;
use Throwable;

/**
 * Lazy PDO wrapper. Connects on first use so pages that never touch the
 * database can render even if MySQL is briefly unreachable. Every query goes
 * through prepared statements.
 */
final class Database
{
    /** @var array<string, mixed> */
    private static array $config = [];

    private static ?PDO $pdo = null;

    /**
     * @param array<string, mixed> $config
     */
    public static function configure(array $config): void
    {
        self::$config = $config;
        self::$pdo = null;
    }

    /** Inject an existing connection (used by tests and the migrate/seed scripts). */
    public static function setPdo(?PDO $pdo): void
    {
        self::$pdo = $pdo;
    }

    public static function pdo(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        if (self::$config === []) {
            throw new RuntimeException('Database has not been configured.');
        }

        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            self::$config['host'],
            (int) self::$config['port'],
            self::$config['database'],
            self::$config['charset'] ?? 'utf8mb4',
        );

        self::$pdo = new PDO(
            $dsn,
            (string) self::$config['username'],
            (string) self::$config['password'],
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_STRINGIFY_FETCHES  => false,
            ],
        );

        return self::$pdo;
    }

    /**
     * @param array<string|int, mixed> $params
     * @return array<int, array<string, mixed>>
     */
    public static function select(string $sql, array $params = []): array
    {
        $stmt = self::pdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * @param array<string|int, mixed> $params
     * @return array<string, mixed>|null
     */
    public static function selectOne(string $sql, array $params = []): ?array
    {
        $stmt = self::pdo()->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    /**
     * Run a statement and return the affected row count.
     *
     * @param array<string|int, mixed> $params
     */
    public static function run(string $sql, array $params = []): int
    {
        $stmt = self::pdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    /**
     * Insert and return the new id as a string.
     *
     * @param array<string|int, mixed> $params
     */
    public static function insert(string $sql, array $params = []): string
    {
        self::run($sql, $params);
        return self::pdo()->lastInsertId();
    }

    public static function transaction(callable $callback): mixed
    {
        $pdo = self::pdo();
        $pdo->beginTransaction();

        try {
            $result = $callback($pdo);
            $pdo->commit();
            return $result;
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }
}
