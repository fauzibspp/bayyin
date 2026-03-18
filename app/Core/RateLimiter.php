<?php

namespace App\Core;

class RateLimiter
{
    public static function hit(string $key, int $limit = 5, int $seconds = 60): void
    {
        $dir = dirname(__DIR__, 2) . '/storage/cache';

        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $file = $dir . '/rate_' . md5($key) . '.json';
        $now = time();

        $attempts = [];

        if (file_exists($file)) {
            $raw = file_get_contents($file);
            $attempts = json_decode($raw ?: '[]', true) ?: [];
        }

        $attempts = array_values(array_filter($attempts, fn ($timestamp) => $timestamp > ($now - $seconds)));

        if (count($attempts) >= $limit) {
            http_response_code(429);
            exit('Too many attempts. Please try again later.');
        }

        $attempts[] = $now;
        file_put_contents($file, json_encode($attempts));
    }
}