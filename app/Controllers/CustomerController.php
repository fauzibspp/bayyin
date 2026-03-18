<?php

namespace App\Controllers;

use App\Core\CSRF;
use App\Core\Flash;
use App\Core\Request;

class CustomerController extends CrudController
{
    protected string $viewPath = 'customers';
    protected string $routePath = '/customers';

    public function __construct()
    {
        $this->model = new \App\Models\CustomerModel();
    }

    public function index(): void
    {
        $success = Flash::get('success');
        $error = Flash::get('error');
        $this->view('customers/index', compact('success', 'error'));
    }

    public function show(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $data = $this->model->find($id);

        if (!$data) {
            Flash::set('error', 'Record not found.');
            $this->redirect($this->routePath);
        }

        $this->view('customers/show', compact('data'));
    }

    public function create(): void
    {
        $error = null;
        $validationErrors = [];
        $old = [];

        if (Request::isPost()) {
            if (!CSRF::verify((string) ($_POST['csrf'] ?? ''))) {
                $error = 'CSRF token mismatch.';
                $this->view('customers/create', compact('error', 'validationErrors', 'old'));
                return;
            }

            $old = $_POST;

            $data = [
                'name' => $_POST['name'] ?? null,
                'email' => $_POST['email'] ?? null,
                'phone' => $_POST['phone'] ?? null,
                'notes' => $_POST['notes'] ?? null,
                'is_active' => $_POST['is_active'] ?? null,
                'created_at' => date('Y-m-d H:i:s'),
            ];

            $id = $this->model->create($data);

            if ($id > 0) {
                Flash::set('success', 'Customer created successfully.');
                $this->redirect($this->routePath);
            }

            $error = 'Failed to create record.';
        }

        $this->view('customers/create', compact('error', 'validationErrors', 'old'));
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
                $this->view('customers/edit', compact('error', 'validationErrors', 'data'));
                return;
            }

            $updateData = [
                'name' => $_POST['name'] ?? null,
                'email' => $_POST['email'] ?? null,
                'phone' => $_POST['phone'] ?? null,
                'notes' => $_POST['notes'] ?? null,
                'is_active' => $_POST['is_active'] ?? null,
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            if ($this->model->update($id, $updateData)) {
                Flash::set('success', 'Customer updated successfully.');
                $this->redirect($this->routePath);
            }

            $error = 'Failed to update record.';
            $data = array_merge($data, $updateData);
        }

        $this->view('customers/edit', compact('error', 'validationErrors', 'data'));
    }

    public function delete(): void
    {
        $id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);

        if ($id <= 0) {
            Flash::set('error', 'Invalid record id.');
            $this->redirect($this->routePath);
        }
        if ($this->model->update($id, ['deleted_at' => date('Y-m-d H:i:s')])) {
            Flash::set('success', 'Customer soft deleted successfully.');
        } else {
            Flash::set('error', 'Failed to delete record.');
        }
        $this->redirect($this->routePath);
    }
}