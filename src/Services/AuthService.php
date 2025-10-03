<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\UserRepository;

final class AuthService
{
    public function __construct(private readonly UserRepository $users)
    {
    }

    public static function make(): self
    {
        return new self(UserRepository::make());
    }

    public function attempt(string $email, string $password): bool
    {
        $user = $this->users->findByEmail($email);
        if (!$user) {
            return false;
        }

        if (!password_verify($password, $user['password'])) {
            return false;
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];

        return true;
    }

    public function user(): ?array
    {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }

        return $this->users->find((int) $_SESSION['user_id']);
    }

    public function checkRole(string $role): bool
    {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === $role;
    }

    public function logout(): void
    {
        session_destroy();
    }
}
