<?php

// require_once dirname(__DIR__) . '/app/Core/Autoloader.php';
require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Core\Console;

$module = $argv[1] ?? null;
$viewFolder = $argv[2] ?? null;

if (!$module || !$viewFolder) {
    Console::error('Usage: php cli/make-module.php Product products');
    exit(1);
}

$module = preg_replace('/[^A-Za-z0-9_]/', '', $module);
$viewFolder = strtolower(trim($viewFolder));

$controllerName = $module . 'Controller';
$modelName = $module . 'Model';

$controllerPath = dirname(__DIR__) . '/app/Controllers/' . $controllerName . '.php';
$modelPath = dirname(__DIR__) . '/app/Models/' . $modelName . '.php';
$viewPath = dirname(__DIR__) . '/app/Views/' . $viewFolder;

if (!is_dir($viewPath)) {
    mkdir($viewPath, 0775, true);
}

if (!file_exists($controllerPath)) {
    $controllerTemplate = <<<PHP
<?php

namespace App\Controllers;

class {$controllerName} extends CrudController
{
    protected string \$viewPath = '{$viewFolder}';
    protected string \$routePath = '/{$viewFolder}';

    public function __construct()
    {
        \$this->model = new \\App\\Models\\{$modelName}();
    }

    public function index(): void
    {
        \$this->listing();
    }
}
PHP;
    file_put_contents($controllerPath, $controllerTemplate);
}

if (!file_exists($modelPath)) {
    $modelTemplate = <<<PHP
<?php

namespace App\Models;

class {$modelName} extends BaseModel
{
    protected string \$table = '{$viewFolder}';

    protected array \$fillable = [
        //
    ];
}
PHP;
    file_put_contents($modelPath, $modelTemplate);
}

$views = [
    'index.php' => "<h1>{$module} Listing</h1>",
    'create.php' => "<h1>Create {$module}</h1>",
    'edit.php' => "<h1>Edit {$module}</h1>",
];

foreach ($views as $file => $content) {
    $fullPath = $viewPath . '/' . $file;

    if (!file_exists($fullPath)) {
        file_put_contents($fullPath, $content);
    }
}

Console::success("Module scaffold created: {$module}");
Console::line("Generated:");
Console::line("- Controller: {$controllerName}.php");
Console::line("- Model: {$modelName}.php");
Console::line("- Views folder: {$viewFolder}/");