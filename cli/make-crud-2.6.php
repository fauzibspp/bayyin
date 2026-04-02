<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

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
$hasDeletedAt = false;

foreach ($fields as $field) {
    if ($field['name'] === 'deleted_at') {
        $hasDeletedAt = true;
    }
    $fillableLines[] = "        '{$field['name']}',";
}
$fillableLines[] = "        'created_at',";
$fillableLines[] = "        'updated_at',";
$fillableBlock = implode("\n", $fillableLines);

$ruleLines = [];
foreach ($fields as $field) {
    if ($field['name'] === 'deleted_at') {
        continue;
    }
    $rule = SchemaParser::validationRule($field['type']);
    $ruleLines[] = "            '{$field['name']}' => '{$rule}',";
}
$rulesBlock = implode("\n", $ruleLines);

$trashMethods = $hasDeletedAt ? <<<PHP

    public function trash(): void
    {
        \$success = Flash::get('success');
        \$error = Flash::get('error');
        \$items = method_exists(\$this->model, 'trash') ? \$this->model->trash(1, 100) : [];
        \$this->view('{$viewFolder}/trash', compact('success', 'error', 'items'));
    }

    public function restore(): void
    {
        \$id = (int) (\$_GET['id'] ?? \$_POST['id'] ?? 0);

        if (\$id <= 0) {
            Flash::set('error', 'Invalid record id.');
            \$this->redirect(\$this->routePath . '/trash');
        }

        if (method_exists(\$this->model, 'restore') && \$this->model->restore(\$id)) {
            \\App\\Core\\AuditLogger::log('{$table}', 'restore', \$id);
            Flash::set('success', '{$module} restored successfully.');
        } else {
            Flash::set('error', 'Failed to restore record.');
        }

        \$this->redirect(\$this->routePath . '/trash');
    }
PHP : '';

$deleteLogic = $hasDeletedAt
    ? <<<PHP
        if (\$this->model->update(\$id, ['deleted_at' => date('Y-m-d H:i:s')])) {
            \\App\\Core\\AuditLogger::log('{$table}', 'soft_delete', \$id);
            Flash::set('success', '{$module} soft deleted successfully.');
        } else {
            Flash::set('error', 'Failed to delete record.');
        }
PHP
    : <<<PHP
        if (\$this->model->delete(\$id)) {
            \\App\\Core\\AuditLogger::log('{$table}', 'delete', \$id);
            Flash::set('success', '{$module} deleted successfully.');
        } else {
            Flash::set('error', 'Failed to delete record.');
        }
PHP;

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
        \$success = Flash::get('success');
        \$error = Flash::get('error');
        \$this->view('{$viewFolder}/index', compact('success', 'error'));
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

    public function export(): void
    {
        \$rows = method_exists(\$this->model, 'allActive') ? \$this->model->allActive() : \$this->model->all();

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="{$table}.csv"');

        \$out = fopen('php://output', 'w');

        if (!\$out) {
            exit;
        }

        if (!empty(\$rows)) {
            fputcsv(\$out, array_keys(\$rows[0]));
            foreach (\$rows as \$row) {
                fputcsv(\$out, \$row);
            }
        }

        fclose(\$out);
        exit;
    }
{$trashMethods}

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
        if ($field['name'] === 'deleted_at') {
            continue;
        }
        $controllerTemplate .= "\n                '{$field['name']}' => \$_POST['{$field['name']}'] ?? null,";
    }

    $controllerTemplate .= <<<PHP

                'created_at' => date('Y-m-d H:i:s'),
            ];

            \$id = \$this->model->create(\$data);

            if (\$id > 0) {
                \\App\\Core\\AuditLogger::log('{$table}', 'create', \$id, \$data);
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
        if ($field['name'] === 'deleted_at') {
            continue;
        }
        $controllerTemplate .= "\n                '{$field['name']}' => \$_POST['{$field['name']}'] ?? null,";
    }

    $controllerTemplate .= <<<PHP

                'updated_at' => date('Y-m-d H:i:s'),
            ];

            if (\$this->model->update(\$id, \$updateData)) {
                \\App\\Core\\AuditLogger::log('{$table}', 'update', \$id, \$updateData);
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

{$deleteLogic}

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
        if ($field['name'] === 'deleted_at') {
            continue;
        }
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

$columnHeaders = '';
$columnsJs = '';

foreach ($fields as $field) {
    if ($field['name'] === 'deleted_at') {
        continue;
    }
    $label = ucwords(str_replace('_', ' ', $field['name']));
    $columnHeaders .= "\n                    <th>{$label}</th>";
    $columnsJs .= "\n                { data: '{$field['name']}' },";
}

$createFields = '';
$editFields = '';
$showFields = '';

foreach ($fields as $field) {
    $name = $field['name'];

    if ($name === 'deleted_at') {
        continue;
    }

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

$trashView = $hasDeletedAt ? <<<PHP
<?php
\$moduleTitle = '{$module}';
\$viewPath = '{$viewFolder}';
\$items = \$items ?? [];
\$success = \$success ?? null;
\$error = \$error ?? null;
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="mb-0"><?= htmlspecialchars(\$moduleTitle) ?> Trash</h1>
    <a href="/<?= htmlspecialchars(\$viewPath) ?>" class="btn btn-secondary">Back</a>
</div>

<?php if (!empty(\$success)): ?>
    <div class="alert alert-success"><?= htmlspecialchars(\$success) ?></div>
<?php endif; ?>

<?php if (!empty(\$error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars(\$error) ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th width="80">ID</th>
                    <th>Name</th>
                    <th width="180">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty(\$items)): ?>
                    <?php foreach (\$items as \$row): ?>
                        <tr>
                            <td><?= (int) (\$row['id'] ?? 0) ?></td>
                            <td><?= htmlspecialchars((string) (\$row['name'] ?? '')) ?></td>
                            <td>
                                <a href="/<?= htmlspecialchars(\$viewPath) ?>/restore?id=<?= (int) (\$row['id'] ?? 0) ?>" class="btn btn-success btn-sm">
                                    Restore
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center text-muted">No deleted records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
PHP : '';

$indexExtraButtons = $hasDeletedAt
    ? '<a href="/<?= htmlspecialchars($viewPath) ?>/trash" class="btn btn-warning mr-2">Trash</a>'
    : '';

$indexView = <<<PHP
<?php
\$moduleTitle = '{$module}';
\$viewPath = '{$viewFolder}';
\$success = \$success ?? null;
\$error = \$error ?? null;
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="mb-0"><?= htmlspecialchars(\$moduleTitle) ?> Listing</h1>
    <div>
        {$indexExtraButtons}
        <a href="/<?= htmlspecialchars(\$viewPath) ?>/export" class="btn btn-info mr-2">Export CSV</a>
        <button type="button" class="btn btn-success mr-2" data-toggle="modal" data-target="#modal-create-{$viewFolder}">
            <i class="fas fa-plus"></i> Add New
        </button>
        <button type="button" id="btn-bulk-delete-{$viewFolder}" class="btn btn-danger">
            Bulk Delete
        </button>
    </div>
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
        <table id="datatable-{$viewFolder}" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th width="40"><input type="checkbox" id="check-all-{$viewFolder}"></th>
                    <th width="80">ID</th>{$columnHeaders}
                    <th width="260">Actions</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<div class="modal fade" id="modal-create-{$viewFolder}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form id="form-create-{$viewFolder}">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create <?= htmlspecialchars(\$moduleTitle) ?></h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div id="create-errors-{$viewFolder}" class="alert alert-danger d-none"></div>
PHP;

$modalCreateFields = '';
$modalEditFields = '';

foreach ($fields as $field) {
    $name = $field['name'];

    if ($name === 'deleted_at') {
        continue;
    }

    $label = ucwords(str_replace('_', ' ', $name));
    $type = $field['type'];

    if (SchemaParser::isTextarea($type)) {
        $modalCreateFields .= <<<PHP

                    <div class="form-group">
                        <label for="create_{$name}">{$label}</label>
                        <textarea name="{$name}" id="create_{$name}" class="form-control" rows="4" required></textarea>
                    </div>
PHP;

        $modalEditFields .= <<<PHP

                    <div class="form-group">
                        <label for="edit_{$name}">{$label}</label>
                        <textarea name="{$name}" id="edit_{$name}" class="form-control" rows="4" required></textarea>
                    </div>
PHP;
    } elseif (SchemaParser::isCheckbox($type)) {
        $modalCreateFields .= <<<PHP

                    <div class="form-group form-check">
                        <input type="hidden" name="{$name}" value="0">
                        <input type="checkbox" name="{$name}" id="create_{$name}" value="1" class="form-check-input">
                        <label class="form-check-label" for="create_{$name}">{$label}</label>
                    </div>
PHP;

        $modalEditFields .= <<<PHP

                    <div class="form-group form-check">
                        <input type="hidden" name="{$name}" value="0">
                        <input type="checkbox" name="{$name}" id="edit_{$name}" value="1" class="form-check-input">
                        <label class="form-check-label" for="edit_{$name}">{$label}</label>
                    </div>
PHP;
    } else {
        $inputType = SchemaParser::inputType($type);
        $step = $type === 'decimal' ? ' step="0.01"' : '';

        $modalCreateFields .= <<<PHP

                    <div class="form-group">
                        <label for="create_{$name}">{$label}</label>
                        <input type="{$inputType}" name="{$name}" id="create_{$name}" class="form-control"{$step} required>
                    </div>
PHP;

        $modalEditFields .= <<<PHP

                    <div class="form-group">
                        <label for="edit_{$name}">{$label}</label>
                        <input type="{$inputType}" name="{$name}" id="edit_{$name}" class="form-control"{$step} required>
                    </div>
PHP;
    }
}

$indexView .= $modalCreateFields;

$indexView .= <<<PHP

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Save</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modal-edit-{$viewFolder}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form id="form-edit-{$viewFolder}">
            <input type="hidden" name="id" id="edit_id_{$viewFolder}">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit <?= htmlspecialchars(\$moduleTitle) ?></h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div id="edit-errors-{$viewFolder}" class="alert alert-danger d-none"></div>
PHP;

$indexView .= $modalEditFields;

$indexView .= <<<PHP

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
$(function () {
    const table = $('#datatable-{$viewFolder}').DataTable({
        processing: true,
        serverSide: true,
        ajax: '/api/{$viewFolder}/datatable',
        columns: [
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    return '<input type="checkbox" class="row-check" value="' + row.id + '">';
                }
            },
            { data: 'id' },{$columnsJs}
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    return ''
                        + '<a href="/{$viewFolder}/show?id=' + row.id + '" class="btn btn-info btn-sm mr-1">View</a>'
                        + '<button type="button" class="btn btn-warning btn-sm mr-1 btn-edit" data-row=\'' + JSON.stringify(row) + '\'>Edit</button>'
                        + '<button type="button" class="btn btn-danger btn-sm btn-delete" data-id="' + row.id + '">Delete</button>';
                }
            }
        ]
    });

    function showErrors(container, errors) {
        let html = '<ul class="mb-0">';
        $.each(errors || {}, function (field, messages) {
            $.each(messages, function (_, message) {
                html += '<li>' + message + '</li>';
            });
        });
        html += '</ul>';
        $(container).removeClass('d-none').html(html);
    }

    function clearErrors(container) {
        $(container).addClass('d-none').html('');
    }

    $('#form-create-{$viewFolder}').on('submit', function (e) {
        e.preventDefault();
        clearErrors('#create-errors-{$viewFolder}');

        const data = {};
        $(this).serializeArray().forEach(function (item) {
            data[item.name] = item.value;
        });

        $.ajax({
            url: '/api/{$viewFolder}/store',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(data),
            success: function () {
                $('#modal-create-{$viewFolder}').modal('hide');
                $('#form-create-{$viewFolder}')[0].reset();
                table.ajax.reload(null, false);
                alert('Record created successfully.');
            },
            error: function (xhr) {
                const response = xhr.responseJSON || {};
                showErrors('#create-errors-{$viewFolder}', response.errors || { general: ['Failed to create record.'] });
            }
        });
    });

    $(document).on('click', '.btn-edit', function () {
        const row = $(this).data('row');
        clearErrors('#edit-errors-{$viewFolder}');
        $('#edit_id_{$viewFolder}').val(row.id);
PHP;

foreach ($fields as $field) {
    $name = $field['name'];
    if ($name === 'deleted_at') {
        continue;
    }
    if (SchemaParser::isCheckbox($field['type'])) {
        $indexView .= "\n        $('#edit_{$name}').prop('checked', !!parseInt(row.{$name} || 0, 10));";
    } else {
        $indexView .= "\n        $('#edit_{$name}').val(row.{$name} || '');";
    }
}

$indexView .= <<<PHP

        $('#modal-edit-{$viewFolder}').modal('show');
    });

    $('#form-edit-{$viewFolder}').on('submit', function (e) {
        e.preventDefault();
        clearErrors('#edit-errors-{$viewFolder}');

        const data = {};
        $(this).serializeArray().forEach(function (item) {
            data[item.name] = item.value;
        });
PHP;

foreach ($fields as $field) {
    $name = $field['name'];
    if ($name === 'deleted_at') {
        continue;
    }
    if (SchemaParser::isCheckbox($field['type'])) {
        $indexView .= "\n        data['{$name}'] = $('#edit_{$name}').is(':checked') ? 1 : 0;";
    }
}

$indexView .= <<<PHP

        $.ajax({
            url: '/api/{$viewFolder}/update',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(data),
            success: function () {
                $('#modal-edit-{$viewFolder}').modal('hide');
                table.ajax.reload(null, false);
                alert('Record updated successfully.');
            },
            error: function (xhr) {
                const response = xhr.responseJSON || {};
                showErrors('#edit-errors-{$viewFolder}', response.errors || { general: ['Failed to update record.'] });
            }
        });
    });

    $('#check-all-{$viewFolder}').on('change', function () {
        $('.row-check').prop('checked', $(this).is(':checked'));
    });

    $(document).on('click', '.btn-delete', function () {
        const id = $(this).data('id');

        if (!confirm('Are you sure you want to delete this record?')) {
            return;
        }

        $.ajax({
            url: '/api/{$viewFolder}/delete',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ id: id }),
            success: function () {
                table.ajax.reload(null, false);
                alert('Record deleted successfully.');
            },
            error: function () {
                alert('Failed to delete record.');
            }
        });
    });

    $('#btn-bulk-delete-{$viewFolder}').on('click', function () {
        const ids = $('.row-check:checked').map(function () {
            return parseInt($(this).val(), 10);
        }).get();

        if (!ids.length) {
            alert('Please select at least one record.');
            return;
        }

        if (!confirm('Are you sure you want to bulk delete selected records?')) {
            return;
        }

        $.ajax({
            url: '/api/{$viewFolder}/bulk-delete',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ ids: ids }),
            success: function () {
                table.ajax.reload(null, false);
                alert('Selected records deleted successfully.');
            },
            error: function () {
                alert('Failed to bulk delete records.');
            }
        });
    });
});
</script>
PHP;

$showFields = '';
foreach ($fields as $field) {
    $name = $field['name'];
    if ($name === 'deleted_at') {
        continue;
    }
    $label = ucwords(str_replace('_', ' ', $name));
    $showFields .= <<<PHP

        <tr>
            <th width="220">{$label}</th>
            <td><?= htmlspecialchars((string) (\$data['{$name}'] ?? '')) ?></td>
        </tr>
PHP;
}

$showView = <<<PHP
<?php
\$moduleTitle = '{$module}';
\$viewPath = '{$viewFolder}';
\$data = \$data ?? [];
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="mb-0">View <?= htmlspecialchars(\$moduleTitle) ?></h1>
    <div>
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

$createView = <<<PHP
<?php
header('Location: /{$viewFolder}');
exit;
PHP;

$editView = <<<PHP
<?php
header('Location: /{$viewFolder}');
exit;
PHP;

$views = [
    'index.php' => $indexView,
    'create.php' => $createView,
    'edit.php' => $editView,
    'show.php' => $showView,
];

if ($hasDeletedAt) {
    $views['trash.php'] = $trashView;
}

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
    $migrationColumns[] = "                {$field['name']} {$columnType} NULL";
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
    RouteEditor::addWebRoute("/{$viewFolder}/export", "{$controllerName}", "export");

    if ($hasDeletedAt) {
        RouteEditor::addWebRoute("/{$viewFolder}/trash", "{$controllerName}", "trash");
        RouteEditor::addWebRoute("/{$viewFolder}/restore", "{$controllerName}", "restore");
    }
}

Console::success("CRUD scaffold created for {$module}");
Console::line("Generated:");
Console::line("- Controller: {$controllerName}.php");
Console::line("- Model: {$modelName}.php");
Console::line("- Request: {$requestName}.php");
Console::line("- Seeder: {$seederName}.php");
Console::line("- Views: {$viewFolder}/");
Console::line("- Migration: " . basename($migrationFile));
Console::line("- Routes auto registered: /{$viewFolder}, /create, /edit, /delete, /show, /export" . ($hasDeletedAt ? ', /trash, /restore' : ''));
Console::line("- Features: audit log integration, CSV export, modal forms, AJAX create/update/delete, bulk delete, DataTables-ready index" . ($hasDeletedAt ? ', soft delete ready' : ''));
Console::line("- Fields: " . implode(', ', array_map(fn($f) => $f['name'] . ':' . $f['type'], $fields)));