<?php

namespace App\Core;

class Bootstrap
{
    public static function init(): void
    {
        Env::load(dirname(__DIR__, 2) . '/.env');

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_name(Config::get('SESSION_NAME', 'secureapp_session'));

            session_start([
                'cookie_httponly' => true,
                'cookie_secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
                'cookie_samesite' => 'Lax',
                'use_strict_mode' => true,
            ]);
        }
        if (php_sapi_name() !== 'cli') {
            header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
            header('Pragma: no-cache');
            header('Expires: 0');
            header('X-Frame-Options: SAMEORIGIN');
            header('X-Content-Type-Options: nosniff');
            header('Referrer-Policy: strict-origin-when-cross-origin');
        }
    }
}