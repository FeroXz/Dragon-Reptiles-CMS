<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

final class PageRepository
{
    public function __construct(private readonly PDO $connection)
    {
    }

    public static function make(): self
    {
        return new self(Database::getInstance()->getConnection());
    }

    public function all(): array
    {
        $stmt = $this->connection->query('SELECT * FROM pages ORDER BY title');
        return $stmt->fetchAll();
    }

    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->connection->prepare('SELECT * FROM pages WHERE slug = :slug');
        $stmt->execute(['slug' => $slug]);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    public function findHomePage(): ?array
    {
        $stmt = $this->connection->query('SELECT * FROM pages WHERE is_home = 1 LIMIT 1');
        $result = $stmt->fetch();

        return $result ?: null;
    }

    public function find(int $id): ?array
    {
        $stmt = $this->connection->prepare('SELECT * FROM pages WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    public function save(array $data): void
    {
        $now = (new \DateTimeImmutable())->format(DATE_ATOM);
        if (isset($data['id'])) {
            $this->connection->prepare('UPDATE pages SET title = :title, slug = :slug, content = :content, is_home = :is_home, updated_at = :updated_at WHERE id = :id')
                ->execute([
                    'id' => $data['id'],
                    'title' => $data['title'],
                    'slug' => $data['slug'],
                    'content' => $data['content'],
                    'is_home' => $data['is_home'] ?? 0,
                    'updated_at' => $now,
                ]);
        } else {
            $this->connection->prepare('INSERT INTO pages (title, slug, content, is_home, created_at, updated_at) VALUES (:title, :slug, :content, :is_home, :created_at, :updated_at)')
                ->execute([
                    'title' => $data['title'],
                    'slug' => $data['slug'],
                    'content' => $data['content'],
                    'is_home' => $data['is_home'] ?? 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
        }

        if (!empty($data['is_home'])) {
            $this->connection->prepare('UPDATE pages SET is_home = CASE WHEN id = :id THEN 1 ELSE 0 END')->execute(['id' => $data['id'] ?? $this->connection->lastInsertId()]);
        }
    }

    public function delete(int $id): void
    {
        $this->connection->prepare('DELETE FROM pages WHERE id = :id')->execute(['id' => $id]);
    }
}
