<?php

namespace App\Controllers;

class HomeController extends BaseController
{
    public function index(): void
    {
        if (!empty($_SESSION['user_id'])) {
            $this->redirect('/');
        }

        $this->redirect('/login');
    }
}