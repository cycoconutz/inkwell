<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\User;

/**
 * Session-backed authentication for the single admin account type.
 */
final class Auth
{
    private const KEY = 'auth_user';

    /**
     * The authenticated admin, or null. Kept intentionally small in the session.
     *
     * @return array{id:int,name:string,email:string,role:string}|null
     */
    public static function user(): ?array
    {
        $user = Session::get(self::KEY);
        return is_array($user) ? $user : null;
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function id(): ?int
    {
        return self::user()['id'] ?? null;
    }

    public static function login(array $user): void
    {
        Session::regenerate();
        Session::set(self::KEY, [
            'id'    => (int) $user['id'],
            'name'  => (string) $user['name'],
            'email' => (string) $user['email'],
            'role'  => (string) ($user['role'] ?? 'admin'),
        ]);
    }

    public static function logout(): void
    {
        Session::remove(self::KEY);
        Session::regenerate();
    }

    /**
     * Verify credentials without leaking whether the email or the password was
     * wrong. Returns true and starts a session on success.
     */
    public static function attempt(string $email, string $password): bool
    {
        $user = User::findByEmail($email);

        if ($user === null || !password_verify($password, (string) $user['password_hash'])) {
            return false;
        }

        self::login($user);
        return true;
    }
}
