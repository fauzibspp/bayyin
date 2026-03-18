<?php

namespace App\Core;

class Logger
{
    private static function write(string $level, string $message, ?string $user = null): void
    {
        $logDir = dirname(__DIR__, 2) . '/storage/logs';

        if (!is_dir($logDir)) {
            mkdir($logDir, 0775, true);
        }

        $line = sprintf(
            "[%s] [%s] [%s] %s%s",
            date('Y-m-d H:i:s'),
            strtoupper($level),
            $user ?? ($_SESSION['email'] ?? $_SESSION['user'] ?? 'Guest'),
            $message,
            PHP_EOL
        );

        file_put_contents($logDir . '/activity.log', $line, FILE_APPEND);
    }

    public static function info(string $message, ?string $user = null): void
    {
        self::write('info', $message, $user);
    }

    public static function warning(string $message, ?string $user = null): void
    {
        self::write('warning', $message, $user);
    }

    public static function error(string $message, ?string $user = null): void
    {
        self::write('error', $message, $user);
    }
}