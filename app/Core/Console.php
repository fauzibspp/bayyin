<?php

namespace App\Core;

class Console
{
    public static function line(string $message = ''): void
    {
        echo $message . PHP_EOL;
    }

    public static function success(string $message): void
    {
        self::line("[OK] " . $message);
    }

    public static function error(string $message): void
    {
        self::line("[ERROR] " . $message);
    }

    public static function warning(string $message): void
    {
        self::line("[WARNING] " . $message);
    }

    public static function info(string $message): void
    {
        self::line("[INFO] " . $message);
    }
}