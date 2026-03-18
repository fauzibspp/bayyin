<?php

namespace App\Core;

class CSRF
{
    public static function generate(): string
    {
        if (empty($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['_csrf_token'];
    }

    public static function verify(string $token): bool
    {
        $sessionToken = $_SESSION['_csrf_token'] ?? '';
        return is_string($token) && hash_equals($sessionToken, $token);
    }
}