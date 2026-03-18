<?php

namespace App\Core;

class ApiResponse
{
    public static function success(
        mixed $data = null,
        string $message = 'Success',
        int $status = 200,
        array $meta = []
    ): void {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');

        echo json_encode([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'meta' => $meta,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        exit;
    }

    public static function error(
        string $message = 'Error',
        int $status = 400,
        mixed $errors = null
    ): void {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');

        echo json_encode([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        exit;
    }
}