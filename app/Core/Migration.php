<?php

namespace App\Core;

use App\Core\Database;
use PDOException;

class Migration
{
    private Database $db;
    private string $migrationPath;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->migrationPath = dirname(__DIR__, 2) . '/database/migrations';
        $this->ensureMigrationsTable();
    }

    private function ensureMigrationsTable(): void
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS migrations (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(255) NOT NULL UNIQUE,
                batch INT NOT NULL,
                created_at DATETIME NOT NULL
            )
        ";

        $this->db->execute($sql);
    }

    public function getMigrationFiles(): array
    {
        if (!is_dir($this->migrationPath)) {
            mkdir($this->migrationPath, 0775, true);
        }

        $files = glob($this->migrationPath . '/*.php') ?: [];
        sort($files);

        return $files;
    }

    public function getRanMigrations(): array
    {
        $rows = $this->db->fetchAll("SELECT migration FROM migrations ORDER BY migration ASC");
        return array_column($rows, 'migration');
    }

    public function getNextBatchNumber(): int
    {
        $row = $this->db->fetch("SELECT MAX(batch) AS max_batch FROM migrations");
        return ((int)($row['max_batch'] ?? 0)) + 1;
    }

    public function migrate(): void
    {
        $files = $this->getMigrationFiles();
        $ran = $this->getRanMigrations();
        $batch = $this->getNextBatchNumber();

        $pending = [];

        foreach ($files as $file) {
            $name = basename($file);
            if (!in_array($name, $ran, true)) {
                $pending[] = $file;
            }
        }

        if (empty($pending)) {
            Console::info('No pending migrations.');
            return;
        }

        foreach ($pending as $file) {
            $name = basename($file);
            $migration = require $file;

            if (!is_object($migration) || !method_exists($migration, 'up')) {
                Console::error("Invalid migration file: {$name}");
                continue;
            }

            try {
                // $this->db->begin();
                $migration->up($this->db);
                $this->db->execute(
                    "INSERT INTO migrations (migration, batch, created_at) VALUES (?, ?, NOW())",
                    [$name, $batch]
                );
                // $this->db->commit();

                Console::success("Migrated: {$name}");
            } catch (\Throwable $e) {
                // $this->db->rollback();
                Console::error("Failed: {$name}");
                Console::error($e->getMessage());
                break;
            }
        }
    }

    public function rollback(): void
    {
        $row = $this->db->fetch("SELECT MAX(batch) AS batch FROM migrations");

        $batch = (int)($row['batch'] ?? 0);

        if ($batch <= 0) {
            Console::info('No migrations to rollback.');
            return;
        }

        $rows = $this->db->fetchAll(
            "SELECT migration FROM migrations WHERE batch = ? ORDER BY id DESC",
            [$batch]
        );

        if (empty($rows)) {
            Console::info('No migrations to rollback.');
            return;
        }

        foreach ($rows as $row) {
            $name = $row['migration'];
            $file = $this->migrationPath . '/' . $name;

            if (!file_exists($file)) {
                Console::warning("Migration file missing: {$name}");
                continue;
            }

            $migration = require $file;

            if (!is_object($migration) || !method_exists($migration, 'down')) {
                Console::error("Invalid migration file: {$name}");
                continue;
            }

            try {
                // $this->db->begin();
                $migration->down($this->db);
                $this->db->execute(
                    "DELETE FROM migrations WHERE migration = ?", 
                    [$name]
                );
                // $this->db->commit();

                Console::success("Rolled back: {$name}");
            } catch (\Throwable $e) {
                // $this->db->rollback();
                Console::error("Rollback failed: {$name}");
                Console::error($e->getMessage());
                break;
            }
        }
    }

    public function status(): void
    {
        $files = $this->getMigrationFiles();
        $ran = $this->getRanMigrations();

        if (empty($files)) {
            Console::info('No migration files found.');
            return;
        }

        foreach ($files as $file) {
            $name = basename($file);
            $status = in_array($name, $ran, true) ? 'Ran' : 'Pending';
            Console::line(str_pad($status, 10) . ' ' . $name);
        }
    }
}