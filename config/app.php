<?php

declare(strict_types=1);

use App\Core\Config;

Config::set([
    'app_name' => 'Dragon Reptiles CMS',
    'environment' => 'development',
    'database' => [
        'driver' => 'sqlite',
        'path' => BASE_PATH . '/storage/database.sqlite',
    ],
    'security' => [
        'password_algo' => defined('PASSWORD_ARGON2ID') ? PASSWORD_ARGON2ID : PASSWORD_DEFAULT,
    ],
]);
