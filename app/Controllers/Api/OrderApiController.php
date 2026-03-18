<?php

namespace App\Controllers\Api;

use App\Controllers\ApiController;
use App\Core\JsonRequest;
use App\Models\OrderModel;

class OrderApiController extends ApiController
{
    private OrderModel $model;

    public function __construct()
    {
        $this->model = new OrderModel();
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
        if (!array_key_exists('total', $input) || $input['total'] === '') {
            $errors['total'][] = 'Total is required.';
        }
        if (!array_key_exists('status', $input) || $input['status'] === '') {
            $errors['status'][] = 'Status is required.';
        }
        if (!array_key_exists('notes', $input) || $input['notes'] === '') {
            $errors['notes'][] = 'Notes is required.';
        }
        if (!array_key_exists('is_paid', $input) || $input['is_paid'] === '') {
            $errors['is_paid'][] = 'Is Paid is required.';
        }

        return $errors;
    }

    public function index(): void
    {
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = max(1, min(100, (int) ($_GET['per_page'] ?? 10)));
        $search = trim((string) ($_GET['search'] ?? ''));

        if ($search !== '') {
            $items = $this->model->searchPaginate(['name', 'total', 'status', 'notes', 'is_paid'], $search, $page, $perPage);
            $total = $this->model->countSearch(['name', 'total', 'status', 'notes', 'is_paid'], $search);
        } else {
            $items = $this->model->paginate($page, $perPage);
            $total = $this->model->countAll();
        }

        $this->success(
            $items,
            'Order list fetched successfully.',
            200,
            [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'search' => $search,
            ]
        );
    }

    public function show(): void
    {
        $id = (int) ($_GET['id'] ?? 0);

        if ($id <= 0) {
            $this->error('id is required.', 422);
        }

        $item = $this->model->find($id);

        if (!$item) {
            $this->error('Record not found.', 404);
        }

        $this->success($item, 'Order fetched successfully.');
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
            'total' => $input['total'],
            'status' => $input['status'],
            'notes' => $input['notes'],
            'is_paid' => $input['is_paid'],
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $this->success(['id' => $id], 'Order created successfully.');
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
            'total' => $input['total'],
            'status' => $input['status'],
            'notes' => $input['notes'],
            'is_paid' => $input['is_paid'],
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $this->success(null, 'Order updated successfully.');
    }

    public function delete(): void
    {
        $input = JsonRequest::all();

        if (empty($input['id'])) {
            $this->error('id is required.', 422);
        }

        $this->model->delete((int) $input['id']);

        $this->success(null, 'Order deleted successfully.');
    }
}