<?php

namespace App\Controllers;

use App\Core\ApiResponse;

abstract class ApiController extends BaseController
{
    protected function success(
        mixed $data = null,
        string $message = 'Success',
        int $status = 200,
        array $meta = []
    ): void {
        ApiResponse::success($data, $message, $status, $meta);
    }

    protected function error(
        string $message = 'Error',
        int $status = 400,
        mixed $errors = null
    ): void {
        ApiResponse::error($message, $status, $errors);
    }
}