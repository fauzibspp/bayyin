<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Core\Console;

$module = $argv[1] ?? null;
$table = $argv[2] ?? null;
$schema = $argv[3] ?? null;

if (!$module || !$table) {
    Console::error('Usage: php cli/make-full-module.php Product products "name:string,price:decimal"');
    exit(1);
}

$module = preg_replace('/[^A-Za-z0-9_]/', '', $module);
$table = strtolower(trim($table));

Console::info("Generating full module for {$module}...");
/*
|--------------------------------------------------------------------------
| Step 1: Generate Web CRUD
|--------------------------------------------------------------------------
*/
$argv = [$argv[0], $module, $table, $schema];
require dirname(__DIR__) . '/cli/make-crud.php';

/*
|--------------------------------------------------------------------------
| Step 2: Generate API CRUD
|--------------------------------------------------------------------------
*/
$argv = [$argv[0], $module, $table, $schema];
require dirname(__DIR__) . '/cli/make-api-crud.php';

Console::success("Full module generated successfully for {$module}");
Console::line('Included:');
Console::line('- Web CRUD scaffold');
Console::line('- Web modal create/edit forms');
Console::line('- Web AJAX create/update/delete');
Console::line('- Web DataTables-ready listing');
Console::line('- Web bulk delete flow');
Console::line('- API CRUD scaffold');
Console::line('- API JWT protection');
Console::line('- API show endpoint');
Console::line('- API datatable endpoint');
Console::line('- API pagination');
Console::line('- API search/filter support');
Console::line('- API validation helper');
Console::line('- API bulk delete endpoint');
Console::line('- Migration');
Console::line('- Seeder');
Console::line('- Request class');
Console::line('- Web route registration');
Console::line('- API route registration');
Console::line('- Better AJAX-ready admin structure');

exit;