<?php

// require_once dirname(__DIR__) . '/app/Core/Autoloader.php';
require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Core\Console;

$name = $argv[1] ?? null;

if (!$name) {
    Console::error('Usage: php cli/make-migration.php create_users_table');
    exit(1);
}

$name = strtolower(trim($name));
$name = preg_replace('/[^a-z0-9_]/', '_', $name);

$dir = dirname(__DIR__) . '/database/migrations';

if (!is_dir($dir)) {
    mkdir($dir, 0775, true);
}

$timestamp = date('Y_m_d_His');
$filename = $timestamp . '_' . $name . '.php';
$path = $dir . '/' . $filename;

$template = <<<PHP
<?php

use App\Core\Database;

return new class {
    /**
     * Run the migration.
     */
    public function up(Database \$db): void
    {
        \$db->execute("
            -- Write migration SQL here
        ");
    }
    
    /**
     * Reverse the migration.
     */
    public function down(Database \$db): void
    {
        \$db->execute("
            -- Write rollback SQL here
        ");
    }
};
PHP;

file_put_contents($path, $template);

Console::success("Migration created: {$filename}");