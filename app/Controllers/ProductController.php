<?php

namespace App\Controllers;

class ProductController extends CrudController
{
    protected string $viewPath = 'products';
    protected string $routePath = '/products';

    public function __construct()
    {
        $this->model = new \App\Models\ProductModel();
    }

    public function index(): void
    {
        $this->listing();
    }
}