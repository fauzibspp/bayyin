<?php

// require_once dirname(__DIR__) . '/app/Core/Autoloader.php';
require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Core\Console;

$name = $argv[1] ?? null;

if (!$name) {
    Console::error('Usage: php cli/make-request.php StoreProductRequest');
    exit(1);
}

$name = preg_replace('/[^A-Za-z0-9_]/', '', $name);

if (!str_ends_with($name, 'Request')) {
    $name .= 'Request';
}

$dir = dirname(__DIR__) . '/app/Requests';

if (!is_dir($dir)) {
    mkdir($dir, 0775, true);
}

$path = $dir . '/' . $name . '.php';

if (file_exists($path)) {
    Console::error("Request already exists: {$name}");
    exit(1);
}

$template = <<<PHP
<?php

namespace App\Requests;

class {$name}
{
    public static function rules(): array
    {
        return [
            // 'name' => 'required|min:3|max:100',
        ];
    }
}
PHP;

file_put_contents($path, $template);

Console::success("Request created: {$name}.php");