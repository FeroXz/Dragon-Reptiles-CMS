<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;

final class Database
{
    private static ?self $instance = null;
    private ?PDO $connection = null;

    private function __construct()
    {
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function initialize(): void
    {
        if ($this->connection !== null) {
            return;
        }

        $databaseConfig = Config::get('database');

        if (!$databaseConfig) {
            throw new \RuntimeException('Database configuration missing.');
        }

        if ($databaseConfig['driver'] === 'sqlite') {
            $path = $databaseConfig['path'];
            $directory = dirname($path);

            if (!is_dir($directory)) {
                mkdir($directory, 0o755, true);
            }

            $this->connection = new PDO('sqlite:' . $path, options: [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } else {
            throw new \RuntimeException('Unsupported database driver: ' . $databaseConfig['driver']);
        }

        $this->runMigrations();
    }

    public function getConnection(): PDO
    {
        if ($this->connection === null) {
            throw new \RuntimeException('Database has not been initialized.');
        }

        return $this->connection;
    }

    private function runMigrations(): void
    {
        $pdo = $this->getConnection();

        $pdo->exec('CREATE TABLE IF NOT EXISTS migrations (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL UNIQUE,
            executed_at TEXT NOT NULL
        )');

        $migrations = [
            '20240101_000000_initial_schema' => function (PDO $connection): void {
                $connection->exec('CREATE TABLE settings (
                    key TEXT PRIMARY KEY,
                    value TEXT NOT NULL
                )');

                $connection->exec('CREATE TABLE users (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    name TEXT NOT NULL,
                    email TEXT NOT NULL UNIQUE,
                    password TEXT NOT NULL,
                    role TEXT NOT NULL,
                    created_at TEXT NOT NULL,
                    updated_at TEXT NOT NULL
                )');

                $connection->exec('CREATE TABLE pages (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    title TEXT NOT NULL,
                    slug TEXT NOT NULL UNIQUE,
                    content TEXT NOT NULL,
                    is_home INTEGER NOT NULL DEFAULT 0,
                    created_at TEXT NOT NULL,
                    updated_at TEXT NOT NULL
                )');

                $connection->exec('CREATE TABLE posts (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    title TEXT NOT NULL,
                    slug TEXT NOT NULL UNIQUE,
                    excerpt TEXT,
                    content TEXT NOT NULL,
                    published_at TEXT,
                    created_at TEXT NOT NULL,
                    updated_at TEXT NOT NULL
                )');

                $connection->exec('CREATE TABLE version_history (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    major INTEGER NOT NULL,
                    minor INTEGER NOT NULL,
                    patch INTEGER NOT NULL,
                    note TEXT,
                    created_at TEXT NOT NULL
                )');

                $now = (new \DateTimeImmutable())->format(DATE_ATOM);

                $connection->prepare('INSERT INTO users (name, email, password, role, created_at, updated_at) VALUES (:name, :email, :password, :role, :created_at, :updated_at)')
                    ->execute([
                        'name' => 'Administrator',
                        'email' => 'admin@example.com',
                        'password' => password_hash('ChangeMe123!', Config::get('security.password_algo')),
                        'role' => 'admin',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);

                $settings = [
                    ['site_title', 'Dragon Reptiles CMS'],
                    ['logo_text', 'Dragon Reptiles Knowledge Hub'],
                    ['menu_structure', json_encode([
                        ['label' => 'Startseite', 'url' => '/'],
                        ['label' => 'News', 'url' => '/news.php'],
                    ], JSON_THROW_ON_ERROR)],
                    ['sidebar_content', '<p>Willkommen im Reptilien Wissenszentrum.</p>'],
                    ['header_content', '<h1>Dragon Reptiles</h1>'],
                    ['footer_content', '<p>&copy; ' . date('Y') . ' Dragon Reptiles</p>'],
                    ['homepage_intro', '<p>Erkunde moderne Einblicke in die Welt der Reptilien.</p>'],
                ];

                $insertSetting = $connection->prepare('INSERT INTO settings (key, value) VALUES (:key, :value)');
                foreach ($settings as [$key, $value]) {
                    $insertSetting->execute(['key' => $key, 'value' => $value]);
                }

                $pageStmt = $connection->prepare('INSERT INTO pages (title, slug, content, is_home, created_at, updated_at) VALUES (:title, :slug, :content, :is_home, :created_at, :updated_at)');
                $pageStmt->execute([
                    'title' => 'Startseite',
                    'slug' => 'startseite',
                    'content' => '<h2>Willkommen</h2><p>Dieses CMS unterstützt moderne Reptilienforschung und -verwaltung.</p>',
                    'is_home' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                $versionStmt = $connection->prepare('INSERT INTO version_history (major, minor, patch, note, created_at) VALUES (:major, :minor, :patch, :note, :created_at)');
                $versionStmt->execute([
                    'major' => 0,
                    'minor' => 1,
                    'patch' => 0,
                    'note' => 'Initiale CMS Bereitstellung',
                    'created_at' => $now,
                ]);
            },
        ];

        foreach ($migrations as $name => $migration) {
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM migrations WHERE name = :name');
            $stmt->execute(['name' => $name]);
            $hasRun = (int) $stmt->fetchColumn() === 1;

            if (!$hasRun) {
                $pdo->beginTransaction();
                try {
                    $migration($pdo);
                    $insert = $pdo->prepare('INSERT INTO migrations (name, executed_at) VALUES (:name, :executed_at)');
                    $insert->execute([
                        'name' => $name,
                        'executed_at' => (new \DateTimeImmutable())->format(DATE_ATOM),
                    ]);
                    $pdo->commit();
                } catch (PDOException $exception) {
                    $pdo->rollBack();
                    throw new \RuntimeException('Migration failed: ' . $exception->getMessage(), 0, $exception);
                }
            }
        }
    }
}
