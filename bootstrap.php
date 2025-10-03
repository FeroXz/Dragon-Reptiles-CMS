<?php

declare(strict_types=1);

session_start();

const BASE_PATH = __DIR__;

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/src/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

require_once __DIR__ . '/config/app.php';

use App\Core\Database;
use App\Services\VersionManager;

$database = Database::getInstance();
$database->initialize();

VersionManager::initialize($database);
