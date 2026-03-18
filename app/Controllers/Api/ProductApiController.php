<?php

namespace App\Controllers\Api;

use App\Controllers\ApiController;
use App\Core\JsonRequest;
use App\Models\ProductModel;

class ProductApiController extends ApiController
{
    private ProductModel $model;

    public function __construct()
    {
        $this->model = new ProductModel();
    }

    public function index(): void
    {
        $items = $this->model->all();
        $this->success($items, 'Product list fetched successfully.');
    }

    public function store(): void
    {
        $input = JsonRequest::all();

        if (!array_key_exists('name', $input)) {
            $this->error('name is required.', 422);
        }
        if (!array_key_exists('price', $input)) {
            $this->error('price is required.', 422);
        }
        if (!array_key_exists('stock', $input)) {
            $this->error('stock is required.', 422);
        }
        if (!array_key_exists('is_active', $input)) {
            $this->error('is_active is required.', 422);
        }
        if (!array_key_exists('description', $input)) {
            $this->error('description is required.', 422);
        }

        $id = $this->model->create([
            'name' => $input['name'],
            'price' => $input['price'],
            'stock' => $input['stock'],
            'is_active' => $input['is_active'],
            'description' => $input['description'],
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $this->success(['id' => $id], 'Product created successfully.');
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
        if (!array_key_exists('price', $input)) {
            $this->error('price is required.', 422);
        }
        if (!array_key_exists('stock', $input)) {
            $this->error('stock is required.', 422);
        }
        if (!array_key_exists('is_active', $input)) {
            $this->error('is_active is required.', 422);
        }
        if (!array_key_exists('description', $input)) {
            $this->error('description is required.', 422);
        }

        $this->model->update((int)$input['id'], [
            'name' => $input['name'],
            'price' => $input['price'],
            'stock' => $input['stock'],
            'is_active' => $input['is_active'],
            'description' => $input['description'],
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $this->success(null, 'Product updated successfully.');
    }

    public function delete(): void
    {
        $input = JsonRequest::all();

        if (empty($input['id'])) {
            $this->error('id is required.', 422);
        }

        $this->model->delete((int)$input['id']);

        $this->success(null, 'Product deleted successfully.');
    }
}