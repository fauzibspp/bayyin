<?php

namespace App\Core;

class Router
{
    public static function resolve(): array
    {
        $webRoutes = require dirname(__DIR__, 2) . '/routes/web.php';
        $apiRoutes = file_exists(dirname(__DIR__, 2) . '/routes/api.php')
            ? require dirname(__DIR__, 2) . '/routes/api.php'
            : [];

        $routes = [];

        foreach ([$webRoutes, $apiRoutes] as $group) {
            foreach ($group as $method => $items) {
                if (!isset($routes[$method])) {
                    $routes[$method] = [];
                }

                $routes[$method] = array_merge($routes[$method], $items);
            }
        }

        $method = Request::method();
        $uri = Request::uri();

        if (isset($routes[$method][$uri])) {
            return $routes[$method][$uri];
        }

        return [null, null];
    }
}