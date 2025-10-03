<?php

declare(strict_types=1);

require __DIR__ . '/../bootstrap.php';

use App\Services\AuthService;

$auth = AuthService::make();

if (!isset($_SESSION['user_id'])) {
    header('Location: /admin/login.php');
    exit;
}

$currentUser = $auth->user();
