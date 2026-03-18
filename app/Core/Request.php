<?php

namespace App\Core;

class Request
{
    public static function method(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    public static function isGet(): bool
    {
        return self::method() === 'GET';
    }

    public static function isPost(): bool
    {
        return self::method() === 'POST';
    }

    public static function isAjax(): bool
    {
        return strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';
    }

    public static function input(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    public static function all(): array
    {
        return array_merge($_GET, $_POST);
    }

    public static function uri(): string
    {
        return parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    }

    public static function ip(): string
    {
        return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }

    public static function only(array $keys): array
    {
        $data = [];

        foreach ($keys as $key) {
            $data[$key] = $_POST[$key] ?? $_GET[$key] ?? null;
        }

        return $data;
    }


}