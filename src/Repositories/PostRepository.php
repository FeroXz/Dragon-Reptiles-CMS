<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

final class PostRepository
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
        $stmt = $this->connection->query('SELECT * FROM posts ORDER BY COALESCE(published_at, created_at) DESC');
        return $stmt->fetchAll();
    }

    public function published(): array
    {
        $stmt = $this->connection->query('SELECT * FROM posts WHERE published_at IS NOT NULL ORDER BY published_at DESC');
        return $stmt->fetchAll();
    }

    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->connection->prepare('SELECT * FROM posts WHERE slug = :slug');
        $stmt->execute(['slug' => $slug]);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    public function find(int $id): ?array
    {
        $stmt = $this->connection->prepare('SELECT * FROM posts WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    public function save(array $data): void
    {
        $now = (new \DateTimeImmutable())->format(DATE_ATOM);
        $payload = [
            'title' => $data['title'],
            'slug' => $data['slug'],
            'excerpt' => $data['excerpt'] ?? null,
            'content' => $data['content'],
            'published_at' => $data['published_at'] ?? null,
        ];

        if (isset($data['id'])) {
            $payload['id'] = $data['id'];
            $payload['updated_at'] = $now;
            $this->connection->prepare('UPDATE posts SET title = :title, slug = :slug, excerpt = :excerpt, content = :content, published_at = :published_at, updated_at = :updated_at WHERE id = :id')
                ->execute($payload);
        } else {
            $payload['created_at'] = $now;
            $payload['updated_at'] = $now;
            $this->connection->prepare('INSERT INTO posts (title, slug, excerpt, content, published_at, created_at, updated_at) VALUES (:title, :slug, :excerpt, :content, :published_at, :created_at, :updated_at)')
                ->execute($payload);
        }
    }

    public function delete(int $id): void
    {
        $this->connection->prepare('DELETE FROM posts WHERE id = :id')->execute(['id' => $id]);
    }
}
