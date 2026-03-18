<?php

namespace App\Controllers;

use App\Core\Response;
use App\Core\View;

abstract class BaseController
{
    protected function view(string $view, array $data = [], ?string $layout = 'layouts/master'): void
    {
        View::render($view, $data, $layout);
    }

    protected function json(array $data, int $status = 200): void
    {
        Response::json($data, $status);
    }

    protected function redirect(string $url): void
    {
        Response::redirect($url);
    }
}