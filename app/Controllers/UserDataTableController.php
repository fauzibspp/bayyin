<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Middleware\Auth;
use App\Middleware\Role;

class UserDataTableController extends BaseController
{
    public function index(): void
    {
        Auth::handle();
        Role::handle('admin');

        $model = new UserModel();

        $draw = (int) ($_GET['draw'] ?? 1);
        $start = max(0, (int) ($_GET['start'] ?? 0));
        $length = max(1, min(100, (int) ($_GET['length'] ?? 10)));
        $search = trim((string) ($_GET['search']['value'] ?? ''));

        $page = (int) floor($start / $length) + 1;

        if ($search !== '') {
            $items = $model->searchPaginate(['name', 'email', 'roles', 'state'], $search, $page, $length);
            $filtered = $model->countSearch(['name', 'email', 'roles', 'state'], $search);
        } else {
            $items = $model->paginate($page, $length);
            $filtered = $model->countAll();
        }

        $total = $model->countAll();

        foreach ($items as &$item) {
            $id = (int) $item['id'];

            $item['actions'] =
                '<a href="/users/edit?id=' . $id . '" class="btn btn-warning btn-sm">Edit</a> ' .
                '<button class="btn btn-danger btn-sm deleteBtn" data-id="' . $id . '">Delete</button>';
        }

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'draw' => $draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $items,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
}