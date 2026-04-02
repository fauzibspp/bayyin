<?php

namespace App\Models;

use App\Core\Database;

abstract class BaseModel
{
    protected Database $db;
    protected string $table = '';
    protected string $primaryKey = 'id';
    protected array $fillable = [];

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function find(int $id): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ? LIMIT 1",
            [$id]
        );
    }

    public function all(): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table} ORDER BY {$this->primaryKey} DESC"
        );
    }

    public function paginate(int $page = 1, int $perPage = 10): array
    {
        $page = max(1, $page);
        $perPage = max(1, min($perPage, 100));
        $offset = ($page - 1) * $perPage;

        return $this->db->fetchAll(
            "SELECT * FROM {$this->table}
             ORDER BY {$this->primaryKey} DESC
             LIMIT {$perPage} OFFSET {$offset}"
        );
    }

    public function searchPaginate(
        array $searchableFields,
        string $keyword = '',
        int $page = 1,
        int $perPage = 10
    ): array {
        $page = max(1, $page);
        $perPage = max(1, min($perPage, 100));
        $offset = ($page - 1) * $perPage;

        $params = [];
        $where = '';

        if ($keyword !== '' && !empty($searchableFields)) {
            $likes = [];

            foreach ($searchableFields as $field) {
                $likes[] = "{$field} LIKE ?";
                $params[] = '%' . $keyword . '%';
            }

            $where = 'WHERE ' . implode(' OR ', $likes);
        }

        $sql = "SELECT * FROM {$this->table} {$where}
                ORDER BY {$this->primaryKey} DESC
                LIMIT {$perPage} OFFSET {$offset}";

        return $this->db->fetchAll($sql, $params);
    }

    public function countAll(): int
    {
        $row = $this->db->fetch("SELECT COUNT(*) AS total FROM {$this->table}");
        return (int) ($row['total'] ?? 0);
    }

    public function countSearch(array $searchableFields, string $keyword = ''): int
    {
        $params = [];
        $where = '';

        if ($keyword !== '' && !empty($searchableFields)) {
            $likes = [];

            foreach ($searchableFields as $field) {
                $likes[] = "{$field} LIKE ?";
                $params[] = '%' . $keyword . '%';
            }

            $where = 'WHERE ' . implode(' OR ', $likes);
        }

        $row = $this->db->fetch(
            "SELECT COUNT(*) AS total FROM {$this->table} {$where}",
            $params
        );

        return (int) ($row['total'] ?? 0);
    }

    public function create(array $data): int
    {
        $filtered = $this->filterFillable($data);

        $columns = array_keys($filtered);
        $placeholders = implode(',', array_fill(0, count($columns), '?'));
        $columnList = implode(',', $columns);

        return $this->db->insert(
            "INSERT INTO {$this->table} ({$columnList}) VALUES ({$placeholders})",
            array_values($filtered)
        );
    }

    public function update(int $id, array $data): bool
    {
        $filtered = $this->filterFillable($data);

        $sets = [];
        $values = [];

        foreach ($filtered as $column => $value) {
            $sets[] = "{$column} = ?";
            $values[] = $value;
        }

        $values[] = $id;

        $setClause = implode(', ', $sets);

        return $this->db->execute(
            "UPDATE {$this->table} SET {$setClause} WHERE {$this->primaryKey} = ?",
            $values
        );
    }

    public function delete(int $id): bool
    {
        return $this->db->execute(
            "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?",
            [$id]
        );
    }

    public function trash(int $page = 1, int $perPage = 10): array
    {
        $page = max(1, $page);
        $perPage = max(1, min($perPage, 100));
        $offset = ($page - 1) * $perPage;

        return $this->db->fetchAll(
            "SELECT * FROM {$this->table}
             WHERE deleted_at IS NOT NULL
             ORDER BY {$this->primaryKey} DESC
             LIMIT {$perPage} OFFSET {$offset}"
        );
    }

    public function countTrash(): int
    {
        $row = $this->db->fetch(
            "SELECT COUNT(*) AS total FROM {$this->table} WHERE deleted_at IS NOT NULL"
        );

        return (int) ($row['total'] ?? 0);
    }

    public function restore(int $id): bool
    {
        return $this->db->execute(
            "UPDATE {$this->table} SET deleted_at = NULL WHERE {$this->primaryKey} = ?",
            [$id]
        );
    }

    public function allActive(): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table}
             WHERE deleted_at IS NULL
             ORDER BY {$this->primaryKey} DESC"
        );
    }

    protected function filterFillable(array $data): array
    {
        if (empty($this->fillable)) {
            return $data;
        }

        return array_filter(
            $data,
            fn ($key) => in_array($key, $this->fillable, true),
            ARRAY_FILTER_USE_KEY
        );
    }
}