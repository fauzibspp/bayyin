<?php

namespace App\Database\Seeders;

use App\Core\Database;

class UserSeeder
{
    public function run(): void
    {
        $db = Database::getInstance();

        $tableExists = $db->fetch("SHOW TABLES LIKE 'users'");

        if (!$tableExists) {
            throw new \RuntimeException('Users table does not exist. Run migrations first.');
        }

        $existing = $db->fetch(
            "SELECT id FROM users WHERE email = ? LIMIT 1",
            ['admin@example.com']
        );

        if ($existing) {
            return;
        }

        $db->execute(
            "INSERT INTO users (name, email, password, roles, created_at)
             VALUES (?, ?, ?, ?, NOW())",
            [
                'Administrator',
                'admin@example.com',
                password_hash('password123', PASSWORD_DEFAULT),
                'admin'
            ]
        );
    }
}