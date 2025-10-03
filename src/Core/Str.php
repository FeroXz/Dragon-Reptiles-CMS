<?php

declare(strict_types=1);

namespace App\Core;

final class Str
{
    private function __construct()
    {
    }

    public static function slug(string $value): string
    {
        $transliterated = iconv('UTF-8', 'ASCII//TRANSLIT', $value);
        if ($transliterated === false) {
            $transliterated = $value;
        }

        $normalized = strtolower($transliterated);
        $normalized = preg_replace('/[^a-z0-9]+/i', '-', $normalized) ?? '';

        return trim($normalized, '-') ?: 'inhalt';
    }
}
