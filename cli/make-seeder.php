<?php

// require_once dirname(__DIR__) . '/app/Core/Autoloader.php';
require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Core\Console;

$name = $argv[1] ?? null;

if (!$name) {
    Console::error('Usage: php cli/make-seeder.php ProductSeeder');
    exit(1);
}

$name = preg_replace('/[^A-Za-z0-9_]/', '', $name);

$dir = dirname(__DIR__) . '/app/Database/Seeders';

if (!is_dir($dir)) {
    mkdir($dir, 0775, true);
}

$path = $dir . '/' . $name . '.php';

if (file_exists($path)) {
    Console::error("Seeder already exists: {$name}");
    exit(1);
}

$template = <<<PHP
<?php

namespace App\Database\Seeders;

use App\Core\Database;

class {$name}
{
    public function run(): void
    {
        \$db = Database::getInstance();

        // Write seeder logic here
    }
}
PHP;

file_put_contents($path, $template);

Console::success("Seeder created: {$name}.php");