<?php

namespace App\Core;

class Response
{
    public static function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');

        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    public static function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }

    public static function abort(int $code = 404): void
    {
        http_response_code($code);

        $file = dirname(__DIR__) . "/Views/errors/{$code}.php";

        if (file_exists($file)) {
            require $file;
        } else {
            echo "{$code} Error";
        }

        exit;
    }
}