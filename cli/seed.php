<?php

// require_once dirname(__DIR__) . '/app/Core/Autoloader.php';
require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Core\Bootstrap;
use App\Core\Console;
use App\Database\Seeders\UserSeeder;

Bootstrap::init();

try {
    $seeder = new UserSeeder();
    $seeder->run();
    Console::success('Database seeded successfully.');
} catch (\Throwable $e) {
    Console::error('Seeding failed.');
    Console::error($e->getMessage());
    exit(1);
}