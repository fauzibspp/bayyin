<?php

namespace App\Controllers\Api;

use App\Controllers\ApiController;
use App\Core\JsonRequest;
use App\Models\CustomerModel;

class CustomerApiController extends ApiController
{
    private CustomerModel $model;

    public function __construct()
    {
        $this->model = new CustomerModel();
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
        if (!array_key_exists('email', $input) || $input['email'] === '') {
            $errors['email'][] = 'Email is required.';
        }
        if (!array_key_exists('phone', $input) || $input['phone'] === '') {
            $errors['phone'][] = 'Phone is required.';
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
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = max(1, min(100, (int) ($_GET['per_page'] ?? 10)));
        $search = trim((string) ($_GET['search'] ?? ''));

        if ($search !== '') {
            $items = $this->model->searchPaginate(['name', 'email', 'phone', 'notes', 'is_active'], $search, $page, $perPage);
            $total = $this->model->countSearch(['name', 'email', 'phone', 'notes', 'is_active'], $search);
        } else {
            $items = $this->model->paginate($page, $perPage);
            $total = $this->model->countAll();
        }

        $this->success(
            $items,
            'Customer list fetched successfully.',
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
        $draw = (int) ($_GET['draw'] ?? 1);
        $start = max(0, (int) ($_GET['start'] ?? 0));
        $length = max(1, min(100, (int) ($_GET['length'] ?? 10)));
        $search = trim((string) ($_GET['search']['value'] ?? ''));

        $page = (int) floor($start / $length) + 1;

        if ($search !== '') {
            $items = $this->model->searchPaginate(['name', 'email', 'phone', 'notes', 'is_active'], $search, $page, $length);
            $filtered = $this->model->countSearch(['name', 'email', 'phone', 'notes', 'is_active'], $search);
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
        $id = (int) ($_GET['id'] ?? 0);

        if ($id <= 0) {
            $this->error('id is required.', 422, ['id' => ['Id is required.']]);
        }

        $item = $this->model->find($id);

        if (!$item) {
            $this->error('Record not found.', 404);
        }

        $this->success($item, 'Customer fetched successfully.');
    }

    public function store(): void
    {
        $input = JsonRequest::all();
        $errors = $this->validateInput($input);

        if (!empty($errors)) {
            $this->error('Validation failed.', 422, $errors);
        }

        $id = $this->model->create([
            'name' => $input['name'],
            'email' => $input['email'],
            'phone' => $input['phone'],
            'notes' => $input['notes'],
            'is_active' => $input['is_active'],
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $this->success(['id' => $id], 'Customer created successfully.');
    }

    public function update(): void
    {
        $input = JsonRequest::all();
        $errors = $this->validateInput($input, true);

        if (!empty($errors)) {
            $this->error('Validation failed.', 422, $errors);
        }

        $this->model->update((int) $input['id'], [
            'name' => $input['name'],
            'email' => $input['email'],
            'phone' => $input['phone'],
            'notes' => $input['notes'],
            'is_active' => $input['is_active'],
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $this->success(null, 'Customer updated successfully.');
    }

    public function delete(): void
    {
        $input = JsonRequest::all();

        if (empty($input['id'])) {
            $this->error('Validation failed.', 422, ['id' => ['Id is required.']]);
        }

        $this->model->update((int) $input['id'], ['deleted_at' => date('Y-m-d H:i:s')]);

        $this->success(null, 'Customer deleted successfully.');
    }

    public function bulkDelete(): void
    {
        $input = JsonRequest::all();
        $ids = $input['ids'] ?? [];

        if (!is_array($ids) || empty($ids)) {
            $this->error('Validation failed.', 422, ['ids' => ['Ids array is required.']]);
        }

        foreach ($ids as $id) {
            $this->model->update((int) $id, ['deleted_at' => date('Y-m-d H:i:s')]);
        }

        $this->success(null, 'Customer bulk deleted successfully.');
    }
}