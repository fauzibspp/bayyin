<?php

namespace App\Controllers;

use App\Core\CSRF;
use App\Core\Flash;
use App\Core\Request;

class DemoController extends CrudController
{
    protected string $viewPath = 'demos';
    protected string $routePath = '/demos';

    public function __construct()
    {
        $this->model = new \App\Models\DemoModel();
    }

    public function index(): void
    {
        $success = Flash::get('success');
        $error = Flash::get('error');
        $this->view('demos/index', compact('success', 'error'));
    }

    public function show(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $data = $this->model->find($id);

        if (!$data) {
            Flash::set('error', 'Record not found.');
            $this->redirect($this->routePath);
        }

        $this->view('demos/show', compact('data'));
    }

    public function export(): void
    {
        $rows = method_exists($this->model, 'allActive') ? $this->model->allActive() : $this->model->all();

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="demos.csv"');

        $out = fopen('php://output', 'w');

        if (!$out) {
            exit;
        }

        if (!empty($rows)) {
            fputcsv($out, array_keys($rows[0]));
            foreach ($rows as $row) {
                fputcsv($out, $row);
            }
        }

        fclose($out);
        exit;
    }

    public function trash(): void
    {
        $success = Flash::get('success');
        $error = Flash::get('error');
        $items = method_exists($this->model, 'trash') ? $this->model->trash(1, 100) : [];
        $this->view('demos/trash', compact('success', 'error', 'items'));
    }

    public function restore(): void
    {
        $id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);

        if ($id <= 0) {
            Flash::set('error', 'Invalid record id.');
            $this->redirect($this->routePath . '/trash');
        }

        if (method_exists($this->model, 'restore') && $this->model->restore($id)) {
            \App\Core\AuditLogger::log('demos', 'restore', $id);
            Flash::set('success', 'Demo restored successfully.');
        } else {
            Flash::set('error', 'Failed to restore record.');
        }

        $this->redirect($this->routePath . '/trash');
    }

    public function create(): void
    {
        $error = null;
        $validationErrors = [];
        $old = [];

        if (Request::isPost()) {
            if (!CSRF::verify((string) ($_POST['csrf'] ?? ''))) {
                $error = 'CSRF token mismatch.';
                $this->view('demos/create', compact('error', 'validationErrors', 'old'));
                return;
            }

            $old = $_POST;

            $data = [                'name' => $_POST['name'] ?? null,
                'status' => $_POST['status'] ?? null,
                'notes' => $_POST['notes'] ?? null,
                'is_active' => $_POST['is_active'] ?? null,
                'created_at' => date('Y-m-d H:i:s'),
            ];

            $id = $this->model->create($data);

            if ($id > 0) {
                \App\Core\AuditLogger::log('demos', 'create', $id, $data);
                Flash::set('success', 'Demo created successfully.');
                $this->redirect($this->routePath);
            }

            $error = 'Failed to create record.';
        }

        $this->view('demos/create', compact('error', 'validationErrors', 'old'));
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
                $this->view('demos/edit', compact('error', 'validationErrors', 'data'));
                return;
            }

            $updateData = [                'name' => $_POST['name'] ?? null,
                'status' => $_POST['status'] ?? null,
                'notes' => $_POST['notes'] ?? null,
                'is_active' => $_POST['is_active'] ?? null,
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            if ($this->model->update($id, $updateData)) {
                \App\Core\AuditLogger::log('demos', 'update', $id, $updateData);
                Flash::set('success', 'Demo updated successfully.');
                $this->redirect($this->routePath);
            }

            $error = 'Failed to update record.';
            $data = array_merge($data, $updateData);
        }

        $this->view('demos/edit', compact('error', 'validationErrors', 'data'));
    }

    public function delete(): void
    {
        $id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);

        if ($id <= 0) {
            Flash::set('error', 'Invalid record id.');
            $this->redirect($this->routePath);
        }

        if ($this->model->update($id, ['deleted_at' => date('Y-m-d H:i:s')])) {
            \App\Core\AuditLogger::log('demos', 'soft_delete', $id);
            Flash::set('success', 'Demo soft deleted successfully.');
        } else {
            Flash::set('error', 'Failed to delete record.');
        }

        $this->redirect($this->routePath);
    }
}