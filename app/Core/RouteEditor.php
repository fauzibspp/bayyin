<?php

namespace App\Core;

class RouteEditor
{
    public static function addWebRoute(string $route, string $controller, string $method): void
    {
        self::addRoute(
            dirname(__DIR__, 2) . '/routes/web.php',
            'GET',
            $route,
            $controller,
            $method
        );
    }

    public static function addApiRoute(string $httpMethod, string $route, string $controller, string $method): void
    {
        self::addRoute(
            dirname(__DIR__, 2) . '/routes/api.php',
            strtoupper($httpMethod),
            $route,
            $controller,
            $method
        );
    }

    private static function addRoute(
        string $file,
        string $httpMethod,
        string $route,
        string $controller,
        string $method
    ): void {
        if (!file_exists($file)) {
            return;
        }

        $routes = require $file;

        if (!is_array($routes)) {
            return;
        }

        if (!isset($routes[$httpMethod]) || !is_array($routes[$httpMethod])) {
            $routes[$httpMethod] = [];
        }

        if (isset($routes[$httpMethod][$route])) {
            return;
        }

        $routes[$httpMethod][$route] = [$controller, $method];

        file_put_contents($file, self::exportRoutes($routes));
    }

    private static function exportRoutes(array $routes): string
    {
        $output = "<?php\n\nreturn [\n";

        foreach ($routes as $httpMethod => $items) {
            $output .= "    " . var_export($httpMethod, true) . " => [\n";

            foreach ($items as $uri => $handler) {
                $controller = $handler[0] ?? '';
                $method = $handler[1] ?? '';

                $output .= "        "
                    . var_export($uri, true)
                    . " => ["
                    . var_export($controller, true)
                    . ", "
                    . var_export($method, true)
                    . "],\n";
            }

            $output .= "    ],\n";
        }

        $output .= "];\n";

        return $output;
    }
}