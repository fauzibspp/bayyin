<?php

namespace App\Middleware;

class Guest
{
    public static function handle(): void
    {
        if (!empty($_SESSION['user_id'])) {
            header('Location: /');
            exit;
        }
    }
}