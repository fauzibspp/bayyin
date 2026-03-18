<?php

namespace App\Controllers;

use App\Core\Flash;
use App\Core\Pagination;

abstract class CrudController extends BaseController
{
    protected string $viewPath = '';
    protected string $routePath = '';
    protected object $model;

    protected function listing(): void
    {
        $page = Pagination::page();
        $perPage = Pagination::perPage(10);

        $items = $this->model->paginate($page, $perPage);
        $total = $this->model->countAll();
        $meta = Pagination::meta($total, $page, $perPage);

        $success = Flash::get('success');
        $error = Flash::get('error');

        $this->view($this->viewPath . '/index', compact('items', 'meta', 'success', 'error'));
    }

    protected function remove(int $id): void
    {
        if ($this->model->delete($id)) {
            Flash::set('success', 'Record deleted successfully.');
        } else {
            Flash::set('error', 'Failed to delete record.');
        }

        $this->redirect($this->routePath);
    }
}