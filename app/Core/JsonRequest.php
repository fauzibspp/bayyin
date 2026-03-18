<?php

namespace App\Core;

class JsonRequest
{
    public static function all(): array
    {
        $raw = file_get_contents('php://input');

        if (!$raw) {
            return [];
        }

        $decoded = json_decode($raw, true);

        return is_array($decoded) ? $decoded : [];
    }

    public static function input(string $key, mixed $default = null): mixed
    {
        $data = self::all();
        return $data[$key] ?? $default;
    }
}