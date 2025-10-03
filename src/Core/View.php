<?php

declare(strict_types=1);

namespace App\Core;

final class View
{
    private function __construct()
    {
    }

    public static function render(string $template, array $data = []): void
    {
        extract($data, EXTR_OVERWRITE);
        require BASE_PATH . '/storage/templates/' . $template . '.php';
    }
}
