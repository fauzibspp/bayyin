<?php
namespace App\Core;

class Config
{
    public static function get(string $key, mixed $default = null): mixed
    {
        return Env::get($key, $default);
    }
}