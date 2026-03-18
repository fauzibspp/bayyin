<?php

namespace App\Core;

use PDO;
use PDOException;
use PDOStatement;

class Database
{
    private static ?Database $instance = null;
    private PDO $pdo;

    private function __construct()
    {
        try {
            $host = Config::get('DB_HOST', '127.0.0.1');
            $port = Config::get('DB_PORT', '3307');
            $db   = Config::get('DB_NAME', 'prismariskdb');
            $user = Config::get('DB_USER', 'root');
            $pass = Config::get('DB_PASS', '');

            $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";

            $this->pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            $logDir = dirname(__DIR__, 2) . '/storage/logs';

            if (!is_dir($logDir)) {
                mkdir($logDir, 0775, true);
            }

            file_put_contents(
                $logDir . '/db.log',
                '[' . date('Y-m-d H:i:s') . '] ' . $e->getMessage() . PHP_EOL,
                FILE_APPEND
            );

            die('Database connection failed.');
        }
    }

    public static function getInstance(): self
    {
        return self::$instance ??= new self();
    }

    public function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt;
    }

    public function fetch(string $sql, array $params = []): ?array
    {
        $row = $this->query($sql, $params)->fetch();
        return $row ?: null;
    }

    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
    }

    public function execute(string $sql, array $params = []): bool
    {
        return $this->query($sql, $params)->rowCount() >= 0;
    }

    public function insert(string $sql, array $params = []): int
    {
        $this->query($sql, $params);
        return (int) $this->pdo->lastInsertId();
    }

    public function begin(): bool
    {
        if ($this->pdo->inTransaction()) {
            return true;
        }

        return $this->pdo->beginTransaction();
    }

    public function commit(): bool
    {
        if (!$this->pdo->inTransaction()) {
            return true;
        }

        return $this->pdo->commit();
    }

    public function rollback(): bool
    {
        if (!$this->pdo->inTransaction()) {
            return true;
        }

        return $this->pdo->rollBack();
    }

    public function inTransaction(): bool
    {
        return $this->pdo->inTransaction();
    }

    public function pdo(): PDO
    {
        return $this->pdo;
    }
}