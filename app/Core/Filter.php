<?php

namespace App\Core;

class Filter
{
    public static function clean(?string $value): string
    {
        return trim((string)$value);
    }

    public static function keyword(string $key = 'q'): string
    {
        return trim((string)($_GET[$key] ?? ''));
    }

    public static function int(string $key, ?int $default = null): ?int
    {
        $value = $_GET[$key] ?? null;

        if ($value === null || $value === '') {
            return $default;
        }

        return is_numeric($value) ? (int)$value : $default;
    }

    public static function value(string $key, mixed $default = null): mixed
    {
        $value = $_GET[$key] ?? $default;

        if (is_string($value)) {
            return trim($value);
        }

        return $value;
    }
}