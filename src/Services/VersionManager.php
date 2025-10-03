<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use PDO;

final class VersionManager
{
    private function __construct()
    {
    }

    public static function initialize(Database $database): void
    {
        $connection = $database->getConnection();
        $connection->exec('CREATE TABLE IF NOT EXISTS version_cache (
            id INTEGER PRIMARY KEY CHECK (id = 1),
            major INTEGER NOT NULL,
            minor INTEGER NOT NULL,
            patch INTEGER NOT NULL,
            updated_at TEXT NOT NULL
        )');

        $count = (int) $connection->query('SELECT COUNT(*) FROM version_cache')->fetchColumn();
        if ($count === 0) {
            $latest = self::latestVersion($connection);
            $stmt = $connection->prepare('INSERT INTO version_cache (id, major, minor, patch, updated_at) VALUES (1, :major, :minor, :patch, :updated_at)');
            $stmt->execute([
                'major' => $latest['major'],
                'minor' => $latest['minor'],
                'patch' => $latest['patch'],
                'updated_at' => (new \DateTimeImmutable())->format(DATE_ATOM),
            ]);
        }
    }

    public static function latest(): string
    {
        $connection = Database::getInstance()->getConnection();
        $stmt = $connection->query('SELECT major, minor, patch FROM version_cache WHERE id = 1');
        $cache = $stmt->fetch();

        if (!$cache) {
            $latest = self::latestVersion($connection);
            return sprintf('%d.%d.%d', $latest['major'], $latest['minor'], $latest['patch']);
        }

        return sprintf('%d.%d.%d', $cache['major'], $cache['minor'], $cache['patch']);
    }

    public static function record(string $note, string $type = 'patch'): void
    {
        $connection = Database::getInstance()->getConnection();
        $latest = self::latestVersion($connection);

        [$major, $minor, $patch] = [$latest['major'], $latest['minor'], $latest['patch']];

        if ($type === 'major') {
            $major += 1;
            $minor = 0;
            $patch = 0;
        } elseif ($type === 'minor') {
            $minor += 1;
            $patch = 0;
        } else {
            $patch += 1;
        }

        $now = (new \DateTimeImmutable())->format(DATE_ATOM);
        $stmt = $connection->prepare('INSERT INTO version_history (major, minor, patch, note, created_at) VALUES (:major, :minor, :patch, :note, :created_at)');
        $stmt->execute([
            'major' => $major,
            'minor' => $minor,
            'patch' => $patch,
            'note' => $note,
            'created_at' => $now,
        ]);

        $connection->prepare('REPLACE INTO version_cache (id, major, minor, patch, updated_at) VALUES (1, :major, :minor, :patch, :updated_at)')
            ->execute([
                'major' => $major,
                'minor' => $minor,
                'patch' => $patch,
                'updated_at' => $now,
            ]);
    }

    private static function latestVersion(PDO $connection): array
    {
        $stmt = $connection->query('SELECT major, minor, patch FROM version_history ORDER BY id DESC LIMIT 1');
        $result = $stmt->fetch();

        if (!$result) {
            return ['major' => 0, 'minor' => 1, 'patch' => 0];
        }

        return $result;
    }
}
