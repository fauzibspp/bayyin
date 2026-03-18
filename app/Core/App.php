<?php

namespace App\Core;

class App
{
    public function run(): void
    {
        [$controllerName, $action] = Router::resolve();

        if (!$controllerName || !$action) {
            Response::abort(404);
        }

        $classCandidates = [
            "App\\Controllers\\{$controllerName}",
            "App\\Controllers\\Api\\{$controllerName}",
        ];

        $class = null;

        foreach ($classCandidates as $candidate) {
            if (class_exists($candidate)) {
                $class = $candidate;
                break;
            }
        }

        if (!$class) {
            Response::abort(404);
        }

        $controller = new $class();

        if (!method_exists($controller, $action)) {
            Response::abort(404);
        }

        try {
            $controller->{$action}();
        } catch (\Throwable $e) {
            $logDir = dirname(__DIR__, 2) . '/storage/logs';

            if (!is_dir($logDir)) {
                mkdir($logDir, 0775, true);
            }

            file_put_contents(
                $logDir . '/error.log',
                '[' . date('Y-m-d H:i:s') . '] ' . $e->getMessage() . PHP_EOL . $e->getTraceAsString() . PHP_EOL . PHP_EOL,
                FILE_APPEND
            );

            Response::abort(500);
        }
    }
}