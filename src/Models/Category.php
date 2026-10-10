<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Category extends Model
{
    protected static string $table = 'categories';

    /**
     * Categories with a count of their published posts, for the admin list and
     * the public archive.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function withPostCounts(): array
    {
        return Database::select(
            "SELECT c.*,
                    (SELECT COUNT(*) FROM posts p WHERE p.category_id = c.id AND p.status = 'published') AS post_count
             FROM categories c
             ORDER BY c.name ASC",
        );
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function findBySlug(string $slug): ?array
    {
        return Database::selectOne('SELECT * FROM categories WHERE slug = ? LIMIT 1', [$slug]);
    }

    public static function create(string $name, string $description = ''): int
    {
        return (int) Database::insert(
            'INSERT INTO categories (name, slug, description, created_at) VALUES (?, ?, ?, NOW())',
            [trim($name), str_slug($name), trim($description)],
        );
    }

    /**
     * Remove a category and detach its posts (they become uncategorised) rather
     * than deleting content.
     */
    public static function delete(int $id): int
    {
        return (int) Database::transaction(static function () use ($id): int {
            Database::run('UPDATE posts SET category_id = NULL WHERE category_id = ?', [$id]);
            return Database::run('DELETE FROM categories WHERE id = ?', [$id]);
        });
    }
}
