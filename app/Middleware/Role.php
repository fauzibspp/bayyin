<?php

namespace App\Middleware;

class Role
{
    public static function handle(string ...$roles): void
    {
        if (empty($_SESSION['role']) || !in_array($_SESSION['role'], $roles, true)) {
            http_response_code(403);
            require dirname(__DIR__) . '/Views/errors/403.php';
            exit;
        }
    }
}