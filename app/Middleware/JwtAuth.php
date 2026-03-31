<?php

namespace App\Middleware;

use App\Core\ApiResponse;
use App\Core\Jwt;

class JwtAuth
{
    public static function handle(): void
    {
        $token = Jwt::fromAuthorizationHeader();

        if (!$token) {
            ApiResponse::error('Unauthorized.', 401, [
                'token' => ['Bearer token is required.']
            ]);
        }

        $payload = Jwt::decode($token);

        if (!$payload) {
            ApiResponse::error('Invalid or expired token.', 401, [
                'token' => ['Token is invalid or expired.']
            ]);
        }

        $_SERVER['jwt_user'] = $payload;
    }

    public static function role(string ...$roles): void
    {
        self::handle();

        $user = $_SERVER['jwt_user'] ?? [];
        $role = $user['role'] ?? null;

        if (!$role || !in_array($role, $roles, true)) {
            ApiResponse::error('Forbidden.', 403, [
                'role' => ['Insufficient permission.']
            ]);
        }
    }

    public static function user(): array
    {
        return $_SERVER['jwt_user'] ?? [];
    }
}