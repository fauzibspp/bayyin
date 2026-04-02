<?php

namespace App\Core;

class Version
{
    public const NAME = 'Bayyin';
    public const VERSION = '2.9.0';

    public static function getVersion(): string
    {
        return 'v' . self::VERSION;
    }

    public static function getFull(): string
    {
        return self::NAME . ' v' . self::VERSION;
    }

    public static function getName(): string
    {
        return self::NAME;
    }
}