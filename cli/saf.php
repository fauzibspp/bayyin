<?php

// require_once dirname(__DIR__) . '/app/Core/Autoloader.php';
require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Core\Bootstrap;
use App\Core\Console;
use App\Core\Migration;
use App\Core\Version;

Bootstrap::init();

$command = $argv[1] ?? null;
$arg1 = $argv[2] ?? null;
$arg2 = $argv[3] ?? null;

if ($command === '' || in_array($command, ['help', '--help', '-h'], true)) {
    Console::line(Version::getVersion() . ' CLI');
    Console::line(str_repeat('-', 40));
    Console::line("Usage:");
    Console::line("  php cli/saf.php serve [host] [port]");
    Console::line("  php cli/saf.php migrate");
    Console::line("  php cli/saf.php rollback");
    Console::line("  php cli/saf.php migrate:status");
    Console::line("  php cli/saf.php seed");
    Console::line("  php cli/saf.php make:migration create_users_table");
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

        Console::info("Starting " . Version::getVersion() . " on http://{$host}:{$port}");
        passthru(sprintf(
            'php -S %s:%s -t %s',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($publicPath)
        ));
        break;

    case 'migrate':
        (new Migration())->migrate();
        break;

    case 'rollback':
        (new Migration())->rollback();
        break;

    case 'migrate:status':
        (new Migration())->status();
        break;

    case 'seed':
        require dirname(__DIR__) . '/cli/seed.php';
        break;

    case 'make:migration':
        if (!$arg1) {
            Console::error('Migration name is required.');
            exit(1);
        }
        
        $GLOBALS['argv'][1] = $arg1;
        require dirname(__DIR__) . '/cli/make-migration.php';
        break;

    default:
        Console::error("Unknown command: {$command}");
        exit(1);
}