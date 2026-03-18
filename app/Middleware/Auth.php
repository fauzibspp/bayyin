<?php

namespace App\Middleware;
use App\Core\ApiResponse;

class Auth
{
    // public static function handle(): void
    // {
    //     if (empty($_SESSION['user_id'])) {
    //         header('Location: /login');
    //         exit;
    //     }
    // }
    public static function handle(): void
    {
        if (empty($_SESSION['user_id'])) {
            ApiResponse::error('Unauthenticated.', 401);
        }
    }

    public static function role(string ...$roles): void
    {
        self::handle();

        if (empty($_SESSION['role']) || !in_array($_SESSION['role'], $roles, true)) {
            ApiResponse::error('Forbidden.', 403);
        }
    }
}