<?php

require_once dirname(__DIR__) . '/app/Core/Autoloader.php';

use App\Core\Console;
use App\Core\RouteEditor;
use App\Core\SchemaParser;

$module = $argv[1] ?? null;
$table = $argv[2] ?? null;
$schema = $argv[3] ?? null;

if (!$module || !$table) {
    Console::error('Usage: php cli/make-crud.php Product products "name:string,price:decimal,stock:int"');
    exit(1);
}

$module = preg_replace('/[^A-Za-z0-9_]/', '', $module);
$table = strtolower(trim($table));
$fields = SchemaParser::parse($schema);

$controllerName = $module . 'Controller';
$modelName = $module . 'Model';
$requestName = 'Store' . $module . 'Request';
$viewFolder = $table;
$seederName = $module . 'Seeder';
$migrationName = 'create_' . $table . '_table';

$controllersDir = dirname(__DIR__) . '/app/Controllers';
$modelsDir = dirname(__DIR__) . '/app/Models';
$viewsDir = dirname(__DIR__) . '/app/Views/' . $viewFolder;
$requestsDir = dirname(__DIR__) . '/app/Requests';
$seedersDir = dirname(__DIR__) . '/app/Database/Seeders';
$migrationsDir = dirname(__DIR__) . '/database/migrations';

foreach ([$controllersDir, $modelsDir, $viewsDir, $requestsDir, $seedersDir, $migrationsDir] as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
}

$controllerPath = $controllersDir . '/' . $controllerName . '.php';
$modelPath = $modelsDir . '/' . $modelName . '.php';
$requestPath = $requestsDir . '/' . $requestName . '.php';
$seederPath = $seedersDir . '/' . $seederName . '.php';

$fillableLines = [];
foreach ($fields as $field) {
    $fillableLines[] = "        '{$field['name']}',";
}
$fillableLines[] = "        'created_at',";
$fillableLines[] = "        'updated_at',";
$fillableBlock = implode("\n", $fillableLines);

$ruleLines = [];
foreach ($fields as $field) {
    $rule = SchemaParser::validationRule($field['type']);
    $ruleLines[] = "            '{$field['name']}' => '{$rule}',";
}
$rulesBlock = implode("\n", $ruleLines);

if (!file_exists($controllerPath)) {
    $controllerTemplate = <<<PHP
<?php

namespace App\Controllers;

use App\Core\CSRF;
use App\Core\Flash;
use App\Core\Request;

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

    public function show(): void
    {
        \$id = (int) (\$_GET['id'] ?? 0);
        \$data = \$this->model->find(\$id);

        if (!\$data) {
            Flash::set('error', 'Record not found.');
            \$this->redirect(\$this->routePath);
        }

        \$this->view('{$viewFolder}/show', compact('data'));
    }

    public function create(): void
    {
        \$error = null;
        \$validationErrors = [];
        \$old = [];

        if (Request::isPost()) {
            if (!CSRF::verify((string) (\$_POST['csrf'] ?? ''))) {
                \$error = 'CSRF token mismatch.';
                \$this->view('{$viewFolder}/create', compact('error', 'validationErrors', 'old'));
                return;
            }

            \$old = \$_POST;

            \$data = [
PHP;

    foreach ($fields as $field) {
        $controllerTemplate .= "\n                '{$field['name']}' => \$_POST['{$field['name']}'] ?? null,";
    }

    $controllerTemplate .= <<<PHP

                'created_at' => date('Y-m-d H:i:s'),
            ];

            \$id = \$this->model->create(\$data);

            if (\$id > 0) {
                Flash::set('success', '{$module} created successfully.');
                \$this->redirect(\$this->routePath);
            }

            \$error = 'Failed to create record.';
        }

        \$this->view('{$viewFolder}/create', compact('error', 'validationErrors', 'old'));
    }

    public function edit(): void
    {
        \$id = (int) (\$_GET['id'] ?? \$_POST['id'] ?? 0);
        \$data = \$this->model->find(\$id);

        if (!\$data) {
            Flash::set('error', 'Record not found.');
            \$this->redirect(\$this->routePath);
        }

        \$error = null;
        \$validationErrors = [];

        if (Request::isPost()) {
            if (!CSRF::verify((string) (\$_POST['csrf'] ?? ''))) {
                \$error = 'CSRF token mismatch.';
                \$this->view('{$viewFolder}/edit', compact('error', 'validationErrors', 'data'));
                return;
            }

            \$updateData = [
PHP;

    foreach ($fields as $field) {
        $controllerTemplate .= "\n                '{$field['name']}' => \$_POST['{$field['name']}'] ?? null,";
    }

    $controllerTemplate .= <<<PHP

                'updated_at' => date('Y-m-d H:i:s'),
            ];

            if (\$this->model->update(\$id, \$updateData)) {
                Flash::set('success', '{$module} updated successfully.');
                \$this->redirect(\$this->routePath);
            }

            \$error = 'Failed to update record.';
            \$data = array_merge(\$data, \$updateData);
        }

        \$this->view('{$viewFolder}/edit', compact('error', 'validationErrors', 'data'));
    }

    public function delete(): void
    {
        \$id = (int) (\$_GET['id'] ?? \$_POST['id'] ?? 0);

        if (\$id <= 0) {
            Flash::set('error', 'Invalid record id.');
            \$this->redirect(\$this->routePath);
        }

        if (\$this->model->delete(\$id)) {
            Flash::set('success', '{$module} deleted successfully.');
        } else {
            Flash::set('error', 'Failed to delete record.');
        }

        \$this->redirect(\$this->routePath);
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
    protected string \$table = '{$table}';

    protected array \$fillable = [
{$fillableBlock}
    ];
}
PHP;
    file_put_contents($modelPath, $modelTemplate);
}

if (!file_exists($requestPath)) {
    $requestTemplate = <<<PHP
<?php

namespace App\Requests;

class {$requestName}
{
    public static function rules(): array
    {
        return [
{$rulesBlock}
        ];
    }

    public static function messages(): array
    {
        return [
PHP;

    foreach ($fields as $field) {
        $requestTemplate .= "\n            '{$field['name']}.required' => '" . ucwords(str_replace('_', ' ', $field['name'])) . " is required.',";
    }

    $requestTemplate .= <<<PHP

        ];
    }
}
PHP;
    file_put_contents($requestPath, $requestTemplate);
}

if (!file_exists($seederPath)) {
    $seederTemplate = <<<PHP
<?php

namespace App\Database\Seeders;

use App\Core\Database;

class {$seederName}
{
    public function run(): void
    {
        \$db = Database::getInstance();

        // Write seeder logic here
    }
}
PHP;
    file_put_contents($seederPath, $seederTemplate);
}

$indexView = <<<PHP
<?php
\$moduleTitle = '{$module}';
\$viewPath = '{$viewFolder}';
\$success = \$success ?? null;
\$error = \$error ?? null;
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="mb-0"><?= htmlspecialchars(\$moduleTitle) ?> Listing</h1>
    <a href="/<?= htmlspecialchars(\$viewPath) ?>/create" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New
    </a>
</div>

<?php if (!empty(\$success)): ?>
    <div class="alert alert-success"><?= htmlspecialchars(\$success) ?></div>
<?php endif; ?>

<?php if (!empty(\$error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars(\$error) ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title mb-0"><?= htmlspecialchars(\$moduleTitle) ?> Records</h3>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th width="80">ID</th>
PHP;

foreach ($fields as $field) {
    $label = ucwords(str_replace('_', ' ', $field['name']));
    $indexView .= "\n                    <th>{$label}</th>";
}

$indexView .= <<<PHP

                    <th width="260">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="99" class="text-center text-muted">
                        Generated module ready. Connect controller listing data for live records.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
PHP;

$createFields = '';
$editFields = '';
$showFields = '';

foreach ($fields as $field) {
    $name = $field['name'];
    $label = ucwords(str_replace('_', ' ', $name));
    $type = $field['type'];

    $showFields .= <<<PHP

        <tr>
            <th width="220">{$label}</th>
            <td><?= htmlspecialchars((string) (\$data['{$name}'] ?? '')) ?></td>
        </tr>
PHP;

    if (SchemaParser::isTextarea($type)) {
        $createFields .= <<<PHP

            <div class="form-group">
                <label for="{$name}">{$label}</label>
                <textarea name="{$name}" id="{$name}" class="form-control" rows="4" required><?= htmlspecialchars((string) (\$old['{$name}'] ?? '')) ?></textarea>
            </div>
PHP;

        $editFields .= <<<PHP

            <div class="form-group">
                <label for="{$name}">{$label}</label>
                <textarea name="{$name}" id="{$name}" class="form-control" rows="4" required><?= htmlspecialchars((string) (\$data['{$name}'] ?? '')) ?></textarea>
            </div>
PHP;
    } elseif (SchemaParser::isCheckbox($type)) {
        $createFields .= <<<PHP

            <div class="form-group form-check">
                <input type="hidden" name="{$name}" value="0">
                <input type="checkbox" name="{$name}" id="{$name}" value="1" class="form-check-input" <?= !empty(\$old['{$name}']) ? 'checked' : '' ?>>
                <label class="form-check-label" for="{$name}">{$label}</label>
            </div>
PHP;

        $editFields .= <<<PHP

            <div class="form-group form-check">
                <input type="hidden" name="{$name}" value="0">
                <input type="checkbox" name="{$name}" id="{$name}" value="1" class="form-check-input" <?= !empty(\$data['{$name}']) ? 'checked' : '' ?>>
                <label class="form-check-label" for="{$name}">{$label}</label>
            </div>
PHP;
    } else {
        $inputType = SchemaParser::inputType($type);
        $step = $type === 'decimal' ? ' step="0.01"' : '';

        $createFields .= <<<PHP

            <div class="form-group">
                <label for="{$name}">{$label}</label>
                <input type="{$inputType}" name="{$name}" id="{$name}" value="<?= htmlspecialchars((string) (\$old['{$name}'] ?? '')) ?>" class="form-control"{$step} required>
            </div>
PHP;

        $editFields .= <<<PHP

            <div class="form-group">
                <label for="{$name}">{$label}</label>
                <input type="{$inputType}" name="{$name}" id="{$name}" value="<?= htmlspecialchars((string) (\$data['{$name}'] ?? '')) ?>" class="form-control"{$step} required>
            </div>
PHP;
    }
}

$validationBlock = <<<PHP
<?php if (!empty(\$validationErrors) && is_array(\$validationErrors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach (\$validationErrors as \$fieldErrors): ?>
                <?php foreach ((array) \$fieldErrors as \$message): ?>
                    <li><?= htmlspecialchars(\$message) ?></li>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
PHP;

$createView = <<<PHP
<?php
use App\Core\CSRF;

\$moduleTitle = '{$module}';
\$viewPath = '{$viewFolder}';
\$old = \$old ?? [];
\$validationErrors = \$validationErrors ?? [];
?>

<div class="mb-3">
    <h1 class="mb-0">Create <?= htmlspecialchars(\$moduleTitle) ?></h1>
</div>

<?php if (!empty(\$error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars(\$error) ?></div>
<?php endif; ?>

{$validationBlock}

<div class="card">
    <div class="card-body">
        <form method="POST" action="/<?= htmlspecialchars(\$viewPath) ?>/create">
            <input type="hidden" name="csrf" value="<?= htmlspecialchars(CSRF::generate()) ?>">
{$createFields}

            <div class="mt-3">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Save
                </button>
                <a href="/<?= htmlspecialchars(\$viewPath) ?>" class="btn btn-secondary">Back</a>
            </div>
        </form>
    </div>
</div>
PHP;

$editView = <<<PHP
<?php
use App\Core\CSRF;

\$moduleTitle = '{$module}';
\$viewPath = '{$viewFolder}';
\$data = \$data ?? ['id' => ''];
\$validationErrors = \$validationErrors ?? [];
?>

<div class="mb-3">
    <h1 class="mb-0">Edit <?= htmlspecialchars(\$moduleTitle) ?></h1>
</div>

<?php if (!empty(\$error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars(\$error) ?></div>
<?php endif; ?>

{$validationBlock}

<div class="card">
    <div class="card-body">
        <form method="POST" action="/<?= htmlspecialchars(\$viewPath) ?>/edit">
            <input type="hidden" name="csrf" value="<?= htmlspecialchars(CSRF::generate()) ?>">
            <input type="hidden" name="id" value="<?= htmlspecialchars((string) (\$data['id'] ?? '')) ?>">
{$editFields}

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update
                </button>
                <a href="/<?= htmlspecialchars(\$viewPath) ?>" class="btn btn-secondary">Back</a>
            </div>
        </form>
    </div>
</div>
PHP;

$showView = <<<PHP
<?php
\$moduleTitle = '{$module}';
\$viewPath = '{$viewFolder}';
\$data = \$data ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="mb-0">View <?= htmlspecialchars(\$moduleTitle) ?></h1>
    <div>
        <a href="/<?= htmlspecialchars(\$viewPath) ?>/edit?id=<?= htmlspecialchars((string) (\$data['id'] ?? '')) ?>" class="btn btn-warning">Edit</a>
        <a href="/<?= htmlspecialchars(\$viewPath) ?>" class="btn btn-secondary">Back</a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <table class="table table-bordered">
            <tbody>{$showFields}
            </tbody>
        </table>
    </div>
</div>
PHP;

$views = [
    'index.php' => $indexView,
    'create.php' => $createView,
    'edit.php' => $editView,
    'show.php' => $showView,
];

foreach ($views as $file => $content) {
    $path = $viewsDir . '/' . $file;
    if (!file_exists($path)) {
        file_put_contents($path, $content);
    }
}

$timestamp = date('Y_m_d_His');
$migrationFile = $migrationsDir . '/' . $timestamp . '_' . $migrationName . '.php';

$migrationColumns = [];
foreach ($fields as $field) {
    $columnType = SchemaParser::migrationType($field['type']);
    $migrationColumns[] = "                {$field['name']} {$columnType} NOT NULL";
}
$columnsBlock = implode(",\n", $migrationColumns);

if (!file_exists($migrationFile)) {
    $migrationTemplate = <<<PHP
<?php

use App\Core\Database;

return new class {
    public function up(Database \$db): void
    {
        \$db->execute("
            CREATE TABLE IF NOT EXISTS {$table} (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
{$columnsBlock},
                created_at DATETIME NULL,
                updated_at DATETIME NULL
            )
        ");
    }

    public function down(Database \$db): void
    {
        \$db->execute("DROP TABLE IF EXISTS {$table}");
    }
};
PHP;
    file_put_contents($migrationFile, $migrationTemplate);
}

if (class_exists(RouteEditor::class)) {
    RouteEditor::addWebRoute("/{$viewFolder}", "{$controllerName}", "index");
    RouteEditor::addWebRoute("/{$viewFolder}/create", "{$controllerName}", "create");
    RouteEditor::addWebRoute("/{$viewFolder}/edit", "{$controllerName}", "edit");
    RouteEditor::addWebRoute("/{$viewFolder}/delete", "{$controllerName}", "delete");
    RouteEditor::addWebRoute("/{$viewFolder}/show", "{$controllerName}", "show");
}

Console::success("CRUD scaffold created for {$module}");
Console::line("Generated:");
Console::line("- Controller: {$controllerName}.php");
Console::line("- Model: {$modelName}.php");
Console::line("- Request: {$requestName}.php");
Console::line("- Seeder: {$seederName}.php");
Console::line("- Views: {$viewFolder}/");
Console::line("- Migration: " . basename($migrationFile));
Console::line("- Routes auto registered: /{$viewFolder}, /create, /edit, /delete, /show");
Console::line("- Fields: " . implode(', ', array_map(fn($f) => $f['name'] . ':' . $f['type'], $fields)));