<?php

// require_once dirname(__DIR__) . '/app/Core/Autoloader.php';

require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Core\Bootstrap;
use App\Core\Config;
use App\Core\Console;
use App\Core\Migration;
use App\Core\Version;

Bootstrap::init();

$command = strtolower($argv[1] ?? '');
$arg1 = $argv[2] ?? null;
$arg2 = $argv[3] ?? null;
$arg3 = $argv[4] ?? null;

if ($command === '' || in_array($command, ['help', '--help', '-h'], true)) {
    Console::line(Version::getFull() . ' CLI');
    Console::line(str_repeat('-', 50));
    Console::line('Usage:');
    Console::line('  php cli/bayyin.php serve [host] [port]');
    Console::line('  php cli/bayyin.php migrate');
    Console::line('  php cli/bayyin.php rollback');
    Console::line('  php cli/bayyin.php migrate:status');
    Console::line('  php cli/bayyin.php seed');
    Console::line('  php cli/bayyin.php make:migration create_users_table');
    Console::line('  php cli/bayyin.php make:controller ProductController');
    Console::line('  php cli/bayyin.php make:model ProductModel products');
    Console::line('  php cli/bayyin.php make:middleware AuditMiddleware');
    Console::line('  php cli/bayyin.php make:module Product products');
    Console::line('  php cli/bayyin.php make:seeder ProductSeeder');
    Console::line('  php cli/bayyin.php make:request StoreProductRequest');
    Console::line('  php cli/bayyin.php make:crud Product products "name:string,price:decimal,stock:int"');
    Console::line('  php cli/bayyin.php make:api-crud Product products "name:string,price:decimal,stock:int"');
    Console::line('  php cli/bayyin.php make:full-module Product products "name:string,price:decimal,stock:int,is_active:boolean,description:text"');
    Console::line('  php cli/bayyin.php route:list');
    Console::line('  php cli/bayyin.php config:check');
    exit;
}

switch ($command) {
    case 'serve':
        $host = $arg1 ?? 'localhost';
        $port = $arg2 ?? '8080';
        $publicPath = realpath(dirname(__DIR__) . '/public');

        if ($publicPath === false) {
            Console::error('Public folder not found.');
            exit(1);
        }

        Console::info('Starting ' . Version::full() . " on http://{$host}:{$port}");
        passthru(sprintf(
            'php -S %s:%s -t %s',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($publicPath)
        ));
        exit;

    case 'migrate':
        (new Migration())->migrate();
        exit;

    case 'rollback':
        (new Migration())->rollback();
        exit;

    case 'migrate:status':
        (new Migration())->status();
        exit;

    case 'seed':
        require dirname(__DIR__) . '/cli/seed.php';
        exit;

    case 'make:migration':
        if (!$arg1) {
            Console::error('Migration name is required.');
            exit(1);
        }

        $argv = [$argv[0], $arg1];
        require dirname(__DIR__) . '/cli/make-migration.php';
        exit;

    case 'make:controller':
        if (!$arg1) {
            Console::error('Controller name is required.');
            exit(1);
        }

        $argv = [$argv[0], $arg1];
        require dirname(__DIR__) . '/cli/make-controller.php';
        exit;

    case 'make:model':
        if (!$arg1) {
            Console::error('Usage: php cli/bayyin.php make:model ProductModel products');
            exit(1);
        }

        $argv = [$argv[0], $arg1, $arg2];
        require dirname(__DIR__) . '/cli/make-model.php';
        exit;

    case 'make:middleware':
        if (!$arg1) {
            Console::error('Middleware name is required.');
            exit(1);
        }

        $argv = [$argv[0], $arg1];
        require dirname(__DIR__) . '/cli/make-middleware.php';
        exit;

    case 'make:module':
        if (!$arg1 || !$arg2) {
            Console::error('Usage: php cli/bayyin.php make:module Product products');
            exit(1);
        }

        $argv = [$argv[0], $arg1, $arg2];
        require dirname(__DIR__) . '/cli/make-module.php';
        exit;
    
    case 'make:full-module':
        if (!$arg1 || !$arg2) {
            Console::error('Usage: php cli/bayyin.php make:full-module Product products "name:string,price:decimal"');
            exit(1);
        }
        $arg3 = $argv[4] ?? null;
        $argv = [$argv[0], $arg1, $arg2, $arg3];
        require dirname(__DIR__) . '/cli/make-full-module.php';
        exit;

    case 'make:seeder':
        if (!$arg1) {
            Console::error('Seeder name is required.');
            exit(1);
        }

        $argv = [$argv[0], $arg1];
        require dirname(__DIR__) . '/cli/make-seeder.php';
        exit;

    case 'make:request':
        if (!$arg1) {
            Console::error('Usage: php cli/bayyin.php make:request StoreProductRequest');
            exit(1);
        }

        $argv = [$argv[0], $arg1];
        require dirname(__DIR__) . '/cli/make-request.php';
        exit;

    case 'make:crud':
        if (!$arg1 || !$arg2) {
            Console::error('Usage: php cli/bayyin.php make:crud Product products "name:string,price:decimal"');
            exit(1);
        }
        $arg3 = $argv[4] ?? null;
        $argv = [$argv[0], $arg1, $arg2, $arg3];
        require dirname(__DIR__) . '/cli/make-crud.php';
        exit;
        
    case 'make:api-crud':
        if (!$arg1 || !$arg2) {
            Console::error('Usage: php cli/bayyin.php make:api-crud Product products "name:string,price:decimal,stock:int"');
            exit(1);
        }
        $arg3 = $argv[4] ?? null;
        $argv = [$argv[0], $arg1, $arg2, $arg3];
        require dirname(__DIR__) . '/cli/make-api-crud.php';
        exit;

    case 'route:list':
        require dirname(__DIR__) . '/cli/route-list.php';
        exit;

    case 'config:check':
        Console::line('Bayyin Configuration Check');
        Console::line(str_repeat('-', 40));

        $keys = [
            'APP_NAME',
            'APP_ENV',
            'APP_DEBUG',
            'APP_URL',
            'DB_HOST',
            'DB_PORT',
            'DB_NAME',
            'DB_USER',
            'SESSION_NAME',
        ];

        foreach ($keys as $key) {
            $value = Config::get($key, '[NOT SET]');
            Console::line(str_pad($key, 20) . ': ' . $value);
        }
        exit;

    default:
        Console::error("Unknown command: {$command}");
        exit(1);
}