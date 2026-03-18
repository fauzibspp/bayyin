<?php

namespace App\Controllers;

class CategoryController extends CrudController
{
    protected string $viewPath = 'categories';
    protected string $routePath = '/categories';

    public function __construct()
    {
        $this->model = new \App\Models\CategoryModel();
    }

    public function index(): void
    {
        $this->listing();
    }
}