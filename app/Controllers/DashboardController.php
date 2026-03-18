<?php

namespace App\Controllers;

use App\Middleware\Auth;

class DashboardController extends BaseController
{
    public function index(): void
    {
        Auth::handle();
        $this->view('dashboard/admin');
    }
}