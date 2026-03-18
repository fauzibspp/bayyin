<?php

// require_once dirname(__DIR__) . '/app/Core/Autoloader.php';
require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Core\Console;

$name = $argv[1] ?? null;

if (!$name) {
    Console::error('Usage: php cli/make-middleware.php AuditMiddleware');
    exit(1);
}

$name = preg_replace('/[^A-Za-z0-9_]/', '', $name);

$path = dirname(__DIR__) . '/app/Middleware/' . $name . '.php';

if (file_exists($path)) {
    Console::error("Middleware already exists: {$name}");
    exit(1);
}

$template = <<<PHP
<?php

namespace App\Middleware;

class {$name}
{
    public static function handle(): void
    {
        // TODO: implement middleware logic
    }
}
PHP;

file_put_contents($path, $template);

Console::success("Middleware created: {$name}.php");