<?php

// require_once dirname(__DIR__) . '/app/Core/Autoloader.php';
require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Core\Console;
use App\Core\Version;

$webRoutes = require dirname(__DIR__) . '/routes/web.php';
$apiRoutes = file_exists(dirname(__DIR__) . '/routes/api.php')
    ? require dirname(__DIR__) . '/routes/api.php'
    : [];

Console::line(Version::getFull() . ' Route List');
Console::line(str_repeat('-', 60));

foreach (['WEB' => $webRoutes, 'API' => $apiRoutes] as $groupName => $group) {
    Console::line("[{$groupName}]");

    foreach ($group as $method => $routes) {
        foreach ($routes as $uri => $handler) {
            $target = is_array($handler) ? implode('@', $handler) : (string) $handler;
            Console::line(str_pad($method, 8) . str_pad($uri, 30) . $target);
        }
    }

    Console::line('');
}

exit;