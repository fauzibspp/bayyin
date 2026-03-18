<?php

// require_once dirname(__DIR__) . '/app/Core/Autoloader.php';
require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Core\Console;

$name = $argv[1] ?? null;
$table = $argv[2] ?? null;

if (!$name) {
    Console::error('Usage: php cli/make-model.php ProductModel products');
    exit(1);
}

$name = preg_replace('/[^A-Za-z0-9_]/', '', $name);

if (!str_ends_with($name, 'Model')) {
    $name .= 'Model';
}

if (!$table) {
    $table = strtolower(str_replace('Model', '', $name)) . 's';
}

$path = dirname(__DIR__) . '/app/Models/' . $name . '.php';

if (file_exists($path)) {
    Console::error("Model already exists: {$name}");
    exit(1);
}

$template = <<<PHP
<?php

namespace App\Models;

class {$name} extends BaseModel
{
    protected string \$table = '{$table}';

    protected array \$fillable = [
        //
    ];
}
PHP;

file_put_contents($path, $template);

Console::success("Model created: {$name}.php");