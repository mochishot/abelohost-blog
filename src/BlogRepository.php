<?php

declare(strict_types=1);

namespace App;

use PDO;

final class BlogRepository
{
    public function __construct(private PDO $db)
    {
    }

    public function categories(): array
    {
        return $this->db->query('SELECT id, name, description FROM categories ORDER BY id')->fetchAll();
    }

    public function homePosts(): array
    {
        // Rank within each category: one query, at most three rows per category.
        return $this->db->query('
            SELECT * FROM (
                SELECT cp.category_id, p.id, p.image, p.title, p.description, p.published_at, p.views,
                    ROW_NUMBER() OVER (
                        PARTITION BY cp.category_id ORDER BY p.published_at DESC, p.id DESC
                    ) AS position
                FROM posts p
                JOIN category_post cp ON cp.post_id = p.id
                WHERE p.published_at <= UTC_TIMESTAMP()
            ) ranked
            WHERE position <= 3
            ORDER BY category_id, position
        ')->fetchAll();
    }

    public function categoryPostCount(int $categoryId): int
    {
        $statement = $this->db->prepare('
            SELECT COUNT(*) FROM posts p
            JOIN category_post cp ON cp.post_id = p.id
            WHERE cp.category_id = ? AND p.published_at <= UTC_TIMESTAMP()
        ');
        $statement->execute([$categoryId]);

        return (int) $statement->fetchColumn();
    }

    public function categoryPosts(int $categoryId, string $sort, int $limit, int $offset): array
    {
        // SQL identifiers cannot be bound; only these two fixed orderings are allowed.
        $order = $sort === 'views' ? 'p.views DESC, p.published_at DESC, p.id DESC' : 'p.published_at DESC, p.id DESC';
        $statement = $this->db->prepare("
            SELECT p.id, p.image, p.title, p.description, p.published_at, p.views FROM posts p
            JOIN category_post cp ON cp.post_id = p.id
            WHERE cp.category_id = :category AND p.published_at <= UTC_TIMESTAMP()
            ORDER BY $order LIMIT :limit OFFSET :offset
        ");
        $statement->bindValue('category', $categoryId, PDO::PARAM_INT);
        $statement->bindValue('limit', $limit, PDO::PARAM_INT);
        $statement->bindValue('offset', $offset, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function post(int $id): array|false
    {
        $statement = $this->db->prepare('SELECT * FROM posts WHERE id = ? AND published_at <= UTC_TIMESTAMP()');
        $statement->execute([$id]);

        return $statement->fetch();
    }

    public function incrementViews(int $id): void
    {
        $statement = $this->db->prepare('UPDATE posts SET views = views + 1 WHERE id = ?');
        $statement->execute([$id]);
    }

    public function postCategories(int $id): array
    {
        $statement = $this->db->prepare('
            SELECT c.id, c.name FROM categories c
            JOIN category_post cp ON cp.category_id = c.id
            WHERE cp.post_id = ? ORDER BY c.id
        ');
        $statement->execute([$id]);

        return $statement->fetchAll();
    }

    public function relatedPosts(int $id): array
    {
        // More shared categories means a closer match. GROUP BY prevents duplicate cards.
        $statement = $this->db->prepare('
            SELECT p.id, p.image, p.title, p.description, p.published_at, p.views
            FROM posts p
            JOIN category_post candidate ON candidate.post_id = p.id
            JOIN category_post current ON current.category_id = candidate.category_id
            WHERE current.post_id = ? AND p.id <> ? AND p.published_at <= UTC_TIMESTAMP()
            GROUP BY p.id
            ORDER BY COUNT(*) DESC, p.published_at DESC, p.id DESC
            LIMIT 3
        ');
        $statement->execute([$id, $id]);

        return $statement->fetchAll();
    }
}
