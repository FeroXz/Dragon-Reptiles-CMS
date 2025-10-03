<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

final class SettingRepository
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
        $stmt = $this->connection->query('SELECT key, value FROM settings');
        $settings = [];
        foreach ($stmt->fetchAll() as $row) {
            $settings[$row['key']] = $row['value'];
        }

        return $settings;
    }

    public function get(string $key, ?string $default = null): ?string
    {
        $stmt = $this->connection->prepare('SELECT value FROM settings WHERE key = :key');
        $stmt->execute(['key' => $key]);
        $value = $stmt->fetchColumn();

        return $value !== false ? (string) $value : $default;
    }

    public function updateMany(array $settings): void
    {
        $stmt = $this->connection->prepare('REPLACE INTO settings (key, value) VALUES (:key, :value)');
        foreach ($settings as $key => $value) {
            $stmt->execute([
                'key' => $key,
                'value' => $value,
            ]);
        }
    }
}
