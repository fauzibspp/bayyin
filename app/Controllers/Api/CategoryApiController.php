<?php

namespace App\Controllers\Api;

use App\Controllers\ApiController;
use App\Core\JsonRequest;
use App\Models\CategoryModel;

class CategoryApiController extends ApiController
{
    private CategoryModel $model;

    public function __construct()
    {
        $this->model = new CategoryModel();
    }

    public function index(): void
    {
        $items = $this->model->all();
        $this->success($items, 'Category list fetched successfully.');
    }

    public function store(): void
    {
        $input = JsonRequest::all();

        if (!array_key_exists('name', $input)) {
            $this->error('name is required.', 422);
        }
        if (!array_key_exists('description', $input)) {
            $this->error('description is required.', 422);
        }
        if (!array_key_exists('is_active', $input)) {
            $this->error('is_active is required.', 422);
        }

        $id = $this->model->create([
            'name' => $input['name'],
            'description' => $input['description'],
            'is_active' => $input['is_active'],
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $this->success(['id' => $id], 'Category created successfully.');
    }

    public function update(): void
    {
        $input = JsonRequest::all();

        if (empty($input['id'])) {
            $this->error('id is required.', 422);
        }

        if (!array_key_exists('name', $input)) {
            $this->error('name is required.', 422);
        }
        if (!array_key_exists('description', $input)) {
            $this->error('description is required.', 422);
        }
        if (!array_key_exists('is_active', $input)) {
            $this->error('is_active is required.', 422);
        }

        $this->model->update((int)$input['id'], [
            'name' => $input['name'],
            'description' => $input['description'],
            'is_active' => $input['is_active'],
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $this->success(null, 'Category updated successfully.');
    }

    public function delete(): void
    {
        $input = JsonRequest::all();

        if (empty($input['id'])) {
            $this->error('id is required.', 422);
        }

        $this->model->delete((int)$input['id']);

        $this->success(null, 'Category deleted successfully.');
    }
}