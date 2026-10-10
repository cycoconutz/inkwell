<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Post extends Model
{
    protected static string $table = 'posts';

    private const WITH_RELATIONS = "SELECT p.*,
                c.name AS category_name, c.slug AS category_slug,
                u.name AS author_name
         FROM posts p
         LEFT JOIN categories c ON c.id = p.category_id
         LEFT JOIN users u ON u.id = p.user_id";

    /**
     * Published posts, newest first.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function published(int $limit = 6, int $offset = 0): array
    {
        $limit = max(1, $limit);
        $offset = max(0, $offset);

        return Database::select(
            self::WITH_RELATIONS . " WHERE p.status = 'published'
             ORDER BY p.published_at DESC, p.id DESC
             LIMIT {$limit} OFFSET {$offset}",
        );
    }

    public static function publishedCount(): int
    {
        $row = Database::selectOne("SELECT COUNT(*) AS c FROM posts WHERE status = 'published'");
        return (int) ($row['c'] ?? 0);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function latest(int $limit = 3): array
    {
        return self::published($limit, 0);
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function findPublishedBySlug(string $slug): ?array
    {
        return Database::selectOne(
            self::WITH_RELATIONS . " WHERE p.slug = ? AND p.status = 'published' LIMIT 1",
            [$slug],
        );
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function findWithRelations(int $id): ?array
    {
        return Database::selectOne(self::WITH_RELATIONS . ' WHERE p.id = ? LIMIT 1', [$id]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function publishedByCategory(int $categoryId, int $limit = 6, int $offset = 0): array
    {
        $limit = max(1, $limit);
        $offset = max(0, $offset);

        return Database::select(
            self::WITH_RELATIONS . " WHERE p.status = 'published' AND p.category_id = ?
             ORDER BY p.published_at DESC, p.id DESC
             LIMIT {$limit} OFFSET {$offset}",
            [$categoryId],
        );
    }

    public static function publishedCountByCategory(int $categoryId): int
    {
        $row = Database::selectOne(
            "SELECT COUNT(*) AS c FROM posts WHERE status = 'published' AND category_id = ?",
            [$categoryId],
        );
        return (int) ($row['c'] ?? 0);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function search(string $term, int $limit = 6, int $offset = 0): array
    {
        $limit = max(1, $limit);
        $offset = max(0, $offset);
        $like = '%' . $term . '%';

        return Database::select(
            self::WITH_RELATIONS . " WHERE p.status = 'published'
               AND (p.title LIKE ? OR p.excerpt LIKE ? OR p.body LIKE ?)
             ORDER BY p.published_at DESC, p.id DESC
             LIMIT {$limit} OFFSET {$offset}",
            [$like, $like, $like],
        );
    }

    public static function searchCount(string $term): int
    {
        $like = '%' . $term . '%';
        $row = Database::selectOne(
            "SELECT COUNT(*) AS c FROM posts
             WHERE status = 'published' AND (title LIKE ? OR excerpt LIKE ? OR body LIKE ?)",
            [$like, $like, $like],
        );
        return (int) ($row['c'] ?? 0);
    }

    /**
     * Every post (drafts included) for the admin table.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function allForAdmin(): array
    {
        return Database::select(
            self::WITH_RELATIONS . ' ORDER BY p.updated_at DESC, p.id DESC',
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function create(array $data): int
    {
        $id = Database::insert(
            'INSERT INTO posts (user_id, category_id, title, slug, excerpt, body, status, published_at, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())',
            [
                $data['user_id'],
                $data['category_id'],
                $data['title'],
                $data['slug'],
                $data['excerpt'],
                $data['body'],
                $data['status'],
                $data['published_at'],
            ],
        );

        return (int) $id;
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function update(int $id, array $data): void
    {
        Database::run(
            'UPDATE posts SET category_id = ?, title = ?, slug = ?, excerpt = ?, body = ?,
                    status = ?, published_at = ?, updated_at = NOW()
             WHERE id = ?',
            [
                $data['category_id'],
                $data['title'],
                $data['slug'],
                $data['excerpt'],
                $data['body'],
                $data['status'],
                $data['published_at'],
                $id,
            ],
        );
    }

    public static function delete(int $id): int
    {
        return Database::run('DELETE FROM posts WHERE id = ?', [$id]);
    }

    /**
     * @return array{draft:int, published:int, total:int}
     */
    public static function stats(): array
    {
        $row = Database::selectOne(
            "SELECT
                SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END) AS draft,
                SUM(CASE WHEN status = 'published' THEN 1 ELSE 0 END) AS published,
                COUNT(*) AS total
             FROM posts",
        );

        return [
            'draft'     => (int) ($row['draft'] ?? 0),
            'published' => (int) ($row['published'] ?? 0),
            'total'     => (int) ($row['total'] ?? 0),
        ];
    }

    /** Whether a slug is already taken, optionally ignoring one post id. */
    public static function slugExists(string $slug, ?int $ignoreId = null): bool
    {
        if ($ignoreId === null) {
            $row = Database::selectOne('SELECT COUNT(*) AS c FROM posts WHERE slug = ?', [$slug]);
        } else {
            $row = Database::selectOne('SELECT COUNT(*) AS c FROM posts WHERE slug = ? AND id <> ?', [$slug, $ignoreId]);
        }

        return (int) ($row['c'] ?? 0) > 0;
    }
}
