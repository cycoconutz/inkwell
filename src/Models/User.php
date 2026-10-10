<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class User extends Model
{
    protected static string $table = 'users';

    /**
     * @return array<string, mixed>|null
     */
    public static function findByEmail(string $email): ?array
    {
        return Database::selectOne(
            'SELECT * FROM users WHERE email = ? LIMIT 1',
            [strtolower(trim($email))],
        );
    }

    public static function create(string $name, string $email, string $password, string $role = 'admin'): int
    {
        $id = Database::insert(
            'INSERT INTO users (name, email, password_hash, role, created_at) VALUES (?, ?, ?, ?, NOW())',
            [
                trim($name),
                strtolower(trim($email)),
                password_hash($password, PASSWORD_DEFAULT),
                $role,
            ],
        );

        return (int) $id;
    }

    public static function updatePassword(int $id, string $password): void
    {
        Database::run(
            'UPDATE users SET password_hash = ? WHERE id = ?',
            [password_hash($password, PASSWORD_DEFAULT), $id],
        );
    }
}
