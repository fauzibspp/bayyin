<?php

namespace App\Controllers\Api;

use App\Controllers\ApiController;
use App\Core\JsonRequest;
use App\Models\InvoiceModel;

class InvoiceApiController extends ApiController
{
    private InvoiceModel $model;

    public function __construct()
    {
        $this->model = new InvoiceModel();
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
        if (!array_key_exists('amount', $input) || $input['amount'] === '') {
            $errors['amount'][] = 'Amount is required.';
        }
        if (!array_key_exists('status', $input) || $input['status'] === '') {
            $errors['status'][] = 'Status is required.';
        }
        if (!array_key_exists('remarks', $input) || $input['remarks'] === '') {
            $errors['remarks'][] = 'Remarks is required.';
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
            $items = $this->model->searchPaginate(['name', 'amount', 'status', 'remarks', 'is_paid'], $search, $page, $perPage);
            $total = $this->model->countSearch(['name', 'amount', 'status', 'remarks', 'is_paid'], $search);
        } else {
            $items = $this->model->paginate($page, $perPage);
            $total = $this->model->countAll();
        }

        $this->success(
            $items,
            'Invoice list fetched successfully.',
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
            $items = $this->model->searchPaginate(['name', 'amount', 'status', 'remarks', 'is_paid'], $search, $page, $length);
            $filtered = $this->model->countSearch(['name', 'amount', 'status', 'remarks', 'is_paid'], $search);
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
            $this->error('id is required.', 422);
        }

        $item = $this->model->find($id);

        if (!$item) {
            $this->error('Record not found.', 404);
        }

        $this->success($item, 'Invoice fetched successfully.');
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
            'amount' => $input['amount'],
            'status' => $input['status'],
            'remarks' => $input['remarks'],
            'is_paid' => $input['is_paid'],
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $this->success(['id' => $id], 'Invoice created successfully.');
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
            'amount' => $input['amount'],
            'status' => $input['status'],
            'remarks' => $input['remarks'],
            'is_paid' => $input['is_paid'],
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $this->success(null, 'Invoice updated successfully.');
    }

    public function delete(): void
    {
        $input = JsonRequest::all();

        if (empty($input['id'])) {
            $this->error('id is required.', 422);
        }

        $this->model->delete((int) $input['id']);

        $this->success(null, 'Invoice deleted successfully.');
    }
}