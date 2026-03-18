<?php

namespace App\Core;

class Flash
{
    public static function set(string $key, string $message): void
    {
        $_SESSION['_flash'][$key] = $message;
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        $value = $_SESSION['_flash'][$key] ?? $default;
        unset($_SESSION['_flash'][$key]);
        return $value;
    }

    public static function has(string $key): bool
    {
        return isset($_SESSION['_flash'][$key]);
    }
}