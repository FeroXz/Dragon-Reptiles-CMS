<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Config;
use App\Core\Database;
use PDO;

final class UserRepository
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
        $stmt = $this->connection->query('SELECT id, name, email, role, created_at, updated_at FROM users ORDER BY created_at DESC');
        return $stmt->fetchAll();
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->connection->prepare('SELECT * FROM users WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    public function find(int $id): ?array
    {
        $stmt = $this->connection->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    public function save(array $data): void
    {
        $now = (new \DateTimeImmutable())->format(DATE_ATOM);
        $payload = [
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
            'updated_at' => $now,
        ];

        if (!empty($data['password'])) {
            $payload['password'] = password_hash($data['password'], Config::get('security.password_algo'));
        }

        if (isset($data['id'])) {
            $payload['id'] = $data['id'];
            $sql = 'UPDATE users SET name = :name, email = :email, role = :role, updated_at = :updated_at';
            if (isset($payload['password'])) {
                $sql .= ', password = :password';
            }
            $sql .= ' WHERE id = :id';
            $this->connection->prepare($sql)->execute($payload);
        } else {
            $payload['password'] = $payload['password'] ?? password_hash($data['password'] ?? 'ChangeMe123!', Config::get('security.password_algo'));
            $payload['created_at'] = $now;
            $this->connection->prepare('INSERT INTO users (name, email, password, role, created_at, updated_at) VALUES (:name, :email, :password, :role, :created_at, :updated_at)')->execute($payload);
        }
    }

    public function delete(int $id): void
    {
        $this->connection->prepare('DELETE FROM users WHERE id = :id')->execute(['id' => $id]);
    }
}
