<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Middleware\Auth;
use App\Middleware\Role;

// class UserApiController extends ApiController
// {
//     public function index(): void
//     {
//         Auth::handle();
//         Role::handle('admin');

//         $model = new UserModel();
//         $items = $model->all();

//         foreach ($items as &$item) {
//             unset($item['password']);
//         }

//         $this->success($items, 'Users fetched successfully');
//     }
// }
class UserApiController extends ApiController
{
    public function index(): void
    {
        ApiAuth::role('admin');

        $model = new UserModel();
        $items = $model->all();

        foreach ($items as &$item) {
            unset($item['password']);
        }

        $this->success($items, 'Users fetched successfully');
    }
}