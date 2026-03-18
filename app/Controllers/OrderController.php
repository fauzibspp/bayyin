<?php

namespace App\Controllers;

use App\Core\CSRF;
use App\Core\Flash;
use App\Core\Request;

class OrderController extends CrudController
{
    protected string $viewPath = 'orders';
    protected string $routePath = '/orders';

    public function __construct()
    {
        $this->model = new \App\Models\OrderModel();
    }

    public function index(): void
    {
        $this->listing();
    }

    public function show(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $data = $this->model->find($id);

        if (!$data) {
            Flash::set('error', 'Record not found.');
            $this->redirect($this->routePath);
        }

        $this->view('orders/show', compact('data'));
    }

    public function create(): void
    {
        $error = null;
        $validationErrors = [];
        $old = [];

        if (Request::isPost()) {
            if (!CSRF::verify((string) ($_POST['csrf'] ?? ''))) {
                $error = 'CSRF token mismatch.';
                $this->view('orders/create', compact('error', 'validationErrors', 'old'));
                return;
            }

            $old = $_POST;

            $data = [
                'name' => $_POST['name'] ?? null,
                'total' => $_POST['total'] ?? null,
                'status' => $_POST['status'] ?? null,
                'notes' => $_POST['notes'] ?? null,
                'is_paid' => $_POST['is_paid'] ?? null,
                'created_at' => date('Y-m-d H:i:s'),
            ];

            $id = $this->model->create($data);

            if ($id > 0) {
                Flash::set('success', 'Order created successfully.');
                $this->redirect($this->routePath);
            }

            $error = 'Failed to create record.';
        }

        $this->view('orders/create', compact('error', 'validationErrors', 'old'));
    }

    public function edit(): void
    {
        $id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
        $data = $this->model->find($id);

        if (!$data) {
            Flash::set('error', 'Record not found.');
            $this->redirect($this->routePath);
        }

        $error = null;
        $validationErrors = [];

        if (Request::isPost()) {
            if (!CSRF::verify((string) ($_POST['csrf'] ?? ''))) {
                $error = 'CSRF token mismatch.';
                $this->view('orders/edit', compact('error', 'validationErrors', 'data'));
                return;
            }

            $updateData = [
                'name' => $_POST['name'] ?? null,
                'total' => $_POST['total'] ?? null,
                'status' => $_POST['status'] ?? null,
                'notes' => $_POST['notes'] ?? null,
                'is_paid' => $_POST['is_paid'] ?? null,
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            if ($this->model->update($id, $updateData)) {
                Flash::set('success', 'Order updated successfully.');
                $this->redirect($this->routePath);
            }

            $error = 'Failed to update record.';
            $data = array_merge($data, $updateData);
        }

        $this->view('orders/edit', compact('error', 'validationErrors', 'data'));
    }

    public function delete(): void
    {
        $id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);

        if ($id <= 0) {
            Flash::set('error', 'Invalid record id.');
            $this->redirect($this->routePath);
        }

        if ($this->model->delete($id)) {
            Flash::set('success', 'Order deleted successfully.');
        } else {
            Flash::set('error', 'Failed to delete record.');
        }

        $this->redirect($this->routePath);
    }
}