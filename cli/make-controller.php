<?php

// require_once dirname(__DIR__) . '/app/Core/Autoloader.php';
require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Core\Console;

$name = $argv[1] ?? null;

if (!$name) {
    Console::error('Usage: php cli/make-controller.php ProductController');
    exit(1);
}

$name = preg_replace('/[^A-Za-z0-9_]/', '', $name);

if (!str_ends_with($name, 'Controller')) {
    $name .= 'Controller';
}

$path = dirname(__DIR__) . '/app/Controllers/' . $name . '.php';

if (file_exists($path)) {
    Console::error("Controller already exists: {$name}");
    exit(1);
}

$template = <<<PHP
<?php

namespace App\Controllers;

class {$name} extends BaseController
{
    public function index(): void
    {
        // TODO: implement index
        echo '{$name} index';
    }
}
PHP;

file_put_contents($path, $template);

Console::success("Controller created: {$name}.php");