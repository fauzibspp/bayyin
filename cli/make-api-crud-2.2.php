<?php

require_once dirname(__DIR__) . '/app/Core/Autoloader.php';

use App\Core\Console;
use App\Core\RouteEditor;
use App\Core\SchemaParser;

$module = $argv[1] ?? null;
$table = $argv[2] ?? null;
$schema = $argv[3] ?? null;

if (!$module || !$table) {
    Console::error('Usage: php cli/make-api-crud.php Product products "name:string,price:decimal"');
    exit(1);
}

$module = preg_replace('/[^A-Za-z0-9_]/', '', $module);
$table = strtolower(trim($table));
$fields = SchemaParser::parse($schema);

$controllerName = $module . 'ApiController';
$modelName = $module . 'Model';

$controllersDir = dirname(__DIR__) . '/app/Controllers/Api';

if (!is_dir($controllersDir)) {
    mkdir($controllersDir, 0775, true);
}

$controllerPath = $controllersDir . '/' . $controllerName . '.php';

$fieldAssignments = [];
$updateAssignments = [];
$validationChecks = [];

foreach ($fields as $field) {
    $name = $field['name'];
    $label = ucwords(str_replace('_', ' ', $name));
    $validationChecks[] = "        if (!array_key_exists('{$name}', \$input) || \$input['{$name}'] === '') {\n            \$errors['{$name}'][] = '{$label} is required.';\n        }";
    $fieldAssignments[] = "            '{$name}' => \$input['{$name}'],";
    $updateAssignments[] = "            '{$name}' => \$input['{$name}'],";
}

$validationBlock = implode("\n", $validationChecks);
$createDataBlock = implode("\n", $fieldAssignments);
$updateDataBlock = implode("\n", $updateAssignments);

$searchableFields = [];
foreach ($fields as $field) {
    $searchableFields[] = "'{$field['name']}'";
}
$searchableFieldsBlock = implode(', ', $searchableFields);

if (!file_exists($controllerPath)) {
    $template = <<<PHP
<?php

namespace App\Controllers\Api;

use App\Controllers\ApiController;
use App\Core\JsonRequest;
use App\Models\\{$modelName};

class {$controllerName} extends ApiController
{
    private {$modelName} \$model;

    public function __construct()
    {
        \$this->model = new {$modelName}();
    }

    private function validateInput(array \$input, bool \$requireId = false): array
    {
        \$errors = [];

        if (\$requireId && empty(\$input['id'])) {
            \$errors['id'][] = 'Id is required.';
        }

{$validationBlock}

        return \$errors;
    }

    public function index(): void
    {
        \$page = max(1, (int) (\$_GET['page'] ?? 1));
        \$perPage = max(1, min(100, (int) (\$_GET['per_page'] ?? 10)));
        \$search = trim((string) (\$_GET['search'] ?? ''));

        if (\$search !== '') {
            \$items = \$this->model->searchPaginate([{$searchableFieldsBlock}], \$search, \$page, \$perPage);
            \$total = \$this->model->countSearch([{$searchableFieldsBlock}], \$search);
        } else {
            \$items = \$this->model->paginate(\$page, \$perPage);
            \$total = \$this->model->countAll();
        }

        \$this->success(
            \$items,
            '{$module} list fetched successfully.',
            200,
            [
                'page' => \$page,
                'per_page' => \$perPage,
                'total' => \$total,
                'search' => \$search,
            ]
        );
    }

    public function show(): void
    {
        \$id = (int) (\$_GET['id'] ?? 0);

        if (\$id <= 0) {
            \$this->error('id is required.', 422);
        }

        \$item = \$this->model->find(\$id);

        if (!\$item) {
            \$this->error('Record not found.', 404);
        }

        \$this->success(\$item, '{$module} fetched successfully.');
    }

    public function store(): void
    {
        \$input = JsonRequest::all();
        \$errors = \$this->validateInput(\$input);

        if (!empty(\$errors)) {
            \$this->error('Validation failed.', 422, \$errors);
        }

        \$id = \$this->model->create([
{$createDataBlock}
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        \$this->success(['id' => \$id], '{$module} created successfully.');
    }

    public function update(): void
    {
        \$input = JsonRequest::all();
        \$errors = \$this->validateInput(\$input, true);

        if (!empty(\$errors)) {
            \$this->error('Validation failed.', 422, \$errors);
        }

        \$this->model->update((int) \$input['id'], [
{$updateDataBlock}
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        \$this->success(null, '{$module} updated successfully.');
    }

    public function delete(): void
    {
        \$input = JsonRequest::all();

        if (empty(\$input['id'])) {
            \$this->error('id is required.', 422);
        }

        \$this->model->delete((int) \$input['id']);

        \$this->success(null, '{$module} deleted successfully.');
    }
}
PHP;

    file_put_contents($controllerPath, $template);
}

RouteEditor::addApiRoute('GET', "/api/{$table}", $controllerName, 'index');
RouteEditor::addApiRoute('GET', "/api/{$table}/show", $controllerName, 'show');
RouteEditor::addApiRoute('POST', "/api/{$table}/store", $controllerName, 'store');
RouteEditor::addApiRoute('POST', "/api/{$table}/update", $controllerName, 'update');
RouteEditor::addApiRoute('POST', "/api/{$table}/delete", $controllerName, 'delete');

Console::success("API CRUD scaffold created for {$module}");
Console::line("Generated:");
Console::line("- API Controller: Api/{$controllerName}.php");
Console::line("- API Routes:");
Console::line("  GET  /api/{$table}");
Console::line("  GET  /api/{$table}/show?id=1");
Console::line("  POST /api/{$table}/store");
Console::line("  POST /api/{$table}/update");
Console::line("  POST /api/{$table}/delete");
Console::line("- Features: show, pagination, search/filter, validation helper");
Console::line("- Fields: " . implode(', ', array_map(fn($f) => $f['name'] . ':' . $f['type'], $fields)));