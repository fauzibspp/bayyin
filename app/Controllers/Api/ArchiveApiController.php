<?php

namespace App\Controllers\Api;

use App\Controllers\ApiController;
use App\Core\AuditLogger;
use App\Core\JsonRequest;
use App\Middleware\JwtAuth;
use App\Models\ArchiveModel;

class ArchiveApiController extends ApiController
{
    private ArchiveModel $model;

    public function __construct()
    {
        $this->model = new ArchiveModel();
    }

    private function validateInput(array $input, bool $requireId = false): array
    {
        $errors = [];

        if ($requireId && empty($input['id'])) {
            $errors['id'][] = 'Id is required.';
        }

        if (!array_key_exists('name', $input) || $input['name'] === '') {
            $errors['name'][] = 'Name is required.';
        }
        if (!array_key_exists('code', $input) || $input['code'] === '') {
            $errors['code'][] = 'Code is required.';
        }
        if (!array_key_exists('notes', $input) || $input['notes'] === '') {
            $errors['notes'][] = 'Notes is required.';
        }
        if (!array_key_exists('is_active', $input) || $input['is_active'] === '') {
            $errors['is_active'][] = 'Is Active is required.';
        }

        return $errors;
    }

    public function index(): void
    {
        JwtAuth::handle();

        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = max(1, min(100, (int) ($_GET['per_page'] ?? 10)));
        $search = trim((string) ($_GET['search'] ?? ''));

        if ($search !== '') {
            $items = $this->model->searchPaginate(['name', 'code', 'notes', 'is_active'], $search, $page, $perPage);
            $total = $this->model->countSearch(['name', 'code', 'notes', 'is_active'], $search);
        } else {
            $items = $this->model->paginate($page, $perPage);
            $total = $this->model->countAll();
        }

        $this->success(
            $items,
            'Archive list fetched successfully.',
            200,
            [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'search' => $search,
            ]
        );
    }

    public function datatable(): void
    {
        JwtAuth::handle();

        $draw = (int) ($_GET['draw'] ?? 1);
        $start = max(0, (int) ($_GET['start'] ?? 0));
        $length = max(1, min(100, (int) ($_GET['length'] ?? 10)));
        $search = trim((string) ($_GET['search']['value'] ?? ''));

        $page = (int) floor($start / $length) + 1;

        if ($search !== '') {
            $items = $this->model->searchPaginate(['name', 'code', 'notes', 'is_active'], $search, $page, $length);
            $filtered = $this->model->countSearch(['name', 'code', 'notes', 'is_active'], $search);
        } else {
            $items = $this->model->paginate($page, $length);
            $filtered = $this->model->countAll();
        }

        $total = $this->model->countAll();

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $items,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    public function show(): void
    {
        JwtAuth::handle();

        $id = (int) ($_GET['id'] ?? 0);

        if ($id <= 0) {
            $this->error('id is required.', 422, ['id' => ['Id is required.']]);
        }

        $item = $this->model->find($id);

        if (!$item) {
            $this->error('Record not found.', 404);
        }

        $this->success($item, 'Archive fetched successfully.');
    }

    public function trash(): void
    {
        JwtAuth::handle();

        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = max(1, min(100, (int) ($_GET['per_page'] ?? 10)));

        $items = $this->model->trash($page, $perPage);
        $total = $this->model->countTrash();

        $this->success(
            $items,
            'Archive trash fetched successfully.',
            200,
            [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
            ]
        );
    }

    public function restore(): void
    {
        JwtAuth::role('admin');

        $input = JsonRequest::all();

        if (empty($input['id'])) {
            $this->error('Validation failed.', 422, ['id' => ['Id is required.']]);
        }

        if (!$this->model->restore((int) $input['id'])) {
            $this->error('Failed to restore record.', 500);
        }

        AuditLogger::log('archives', 'restore', (int) $input['id']);

        $this->success(null, 'Archive restored successfully.');
    }

    public function exportCsv(): void
    {
        JwtAuth::handle();

        $rows = $this->model->all();

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="archives.csv"');

        $out = fopen('php://output', 'w');

        if (!$out) {
            exit;
        }

        $headers = ['id', 'name', 'code', 'notes', 'is_active', 'deleted_at'];
        fputcsv($out, $headers);

        foreach ($rows as $row) {
            $line = [];
            foreach ($headers as $header) {
                $line[] = $row[$header] ?? '';
            }
            fputcsv($out, $line);
        }

        fclose($out);
        exit;
    }

    public function store(): void
    {
        JwtAuth::role('admin');

        $input = JsonRequest::all();
        $errors = $this->validateInput($input);

        if (!empty($errors)) {
            $this->error('Validation failed.', 422, $errors);
        }

        $id = $this->model->create([
            'name' => $input['name'],
            'code' => $input['code'],
            'notes' => $input['notes'],
            'is_active' => $input['is_active'],
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        AuditLogger::log('archives', 'create', (int) $id, $input);

        $this->success(['id' => $id], 'Archive created successfully.');
    }

    public function update(): void
    {
        JwtAuth::role('admin');

        $input = JsonRequest::all();
        $errors = $this->validateInput($input, true);

        if (!empty($errors)) {
            $this->error('Validation failed.', 422, $errors);
        }

        $this->model->update((int) $input['id'], [
            'name' => $input['name'],
            'code' => $input['code'],
            'notes' => $input['notes'],
            'is_active' => $input['is_active'],
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        AuditLogger::log('archives', 'update', (int) $input['id'], $input);

        $this->success(null, 'Archive updated successfully.');
    }

    public function delete(): void
    {
        JwtAuth::role('admin');

        $input = JsonRequest::all();

        if (empty($input['id'])) {
            $this->error('Validation failed.', 422, ['id' => ['Id is required.']]);
        }

        $this->model->update((int) $input['id'], ['deleted_at' => date('Y-m-d H:i:s')]);

        AuditLogger::log('archives', 'delete', (int) $input['id']);

        $this->success(null, 'Archive deleted successfully.');
    }

    public function bulkDelete(): void
    {
        JwtAuth::role('admin');

        $input = JsonRequest::all();
        $ids = $input['ids'] ?? [];

        if (!is_array($ids) || empty($ids)) {
            $this->error('Validation failed.', 422, ['ids' => ['Ids array is required.']]);
        }

        foreach ($ids as $id) {
            $this->model->update((int) $id, ['deleted_at' => date('Y-m-d H:i:s')]);
            AuditLogger::log('archives', 'bulk_delete_item', (int) $id);
        }

        $this->success(null, 'Archive bulk deleted successfully.');
    }
}