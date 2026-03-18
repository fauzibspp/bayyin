<?php

namespace App\Controllers;

use App\Core\AuditLogger;
use App\Core\CSRF;
use App\Core\Filter;
use App\Core\Flash;
use App\Core\Pagination;
use App\Core\Request;
use App\Core\Validator;
use App\Middleware\Auth;
use App\Middleware\Role;
use App\Models\UserModel;

class UserController extends CrudController
{
    protected string $viewPath = 'users';
    protected string $routePath = '/users';

    public function __construct()
    {
        $this->model = new UserModel();
    }

    public function index(): void
    {
        Auth::handle();
        Role::handle('admin');

        $page = Pagination::page();
        $perPage = Pagination::perPage(10);
        $keyword = Filter::keyword('q');

        if ($keyword !== '') {
            $items = $this->model->searchPaginate(['name', 'email', 'roles', 'state'], $keyword, $page, $perPage);
            $total = $this->model->countSearch(['name', 'email', 'roles', 'state'], $keyword);
        } else {
            $items = $this->model->paginate($page, $perPage);
            $total = $this->model->countAll();
        }

        $meta = Pagination::meta($total, $page, $perPage);
        $success = Flash::get('success');
        $error = Flash::get('error');

        $this->view('users/index', compact('items', 'meta', 'success', 'error', 'keyword'));
    }

    public function create(): void
    {
        Auth::handle();
        Role::handle('admin');

        $error = null;
        $old = [];

        if (Request::isPost()) {
            if (!CSRF::verify((string)($_POST['csrf'] ?? ''))) {
                $error = 'CSRF token mismatch.';
                $this->view('users/create', compact('error', 'old'));
                return;
            }

            $old = [
                'name' => trim((string)($_POST['name'] ?? '')),
                'email' => trim(strtolower((string)($_POST['email'] ?? ''))),
                'roles' => trim((string)($_POST['roles'] ?? '')),
                'jpn_id' => trim((string)($_POST['jpn_id'] ?? '')),
                'ppd_id' => trim((string)($_POST['ppd_id'] ?? '')),
                'state' => trim((string)($_POST['state'] ?? '')),
            ];

            $data = $old + [
                'password' => (string)($_POST['password'] ?? ''),
            ];

            $validator = new Validator();

            if (!$validator->validate($data, [
                'name' => 'required|min:3|max:100',
                'email' => 'required|email|max:150',
                'password' => 'required|min:6|max:100',
                'roles' => 'required|alpha_dash|max:50',
            ])) {
                $validationErrors = $validator->errors();
                $error = 'Please complete the form correctly.';
                $this->view('users/create', compact('error', 'old', 'validationErrors'));
                return;
            }

            if ($this->model->findByEmail($data['email'])) {
                $error = 'Email already exists.';
                $this->view('users/create', compact('error', 'old'));
                return;
            }

            $ok = $this->model->register($data);

            if ($ok) {
                Flash::set('success', 'User created successfully.');
                $this->redirect('/users');
            }

            $error = 'Failed to create user.';
        }

        $this->view('users/create', compact('error', 'old'));
        
    }

    public function edit(): void
    {
        Auth::handle();
        Role::handle('admin');

        $id = (int)($_GET['id'] ?? 0);
        $user = $this->model->find($id);

        if (!$user) {
            Flash::set('error', 'User not found.');
            $this->redirect('/users');
        }

        $error = null;

        if (Request::isPost()) {
            if (!CSRF::verify((string)($_POST['csrf'] ?? ''))) {
                $error = 'CSRF token mismatch.';
                $this->view('users/edit', compact('error', 'user'));
                return;
            }

            $data = [
                'name' => trim((string)($_POST['name'] ?? '')),
                'email' => trim(strtolower((string)($_POST['email'] ?? ''))),
                'roles' => trim((string)($_POST['roles'] ?? '')),
                'jpn_id' => trim((string)($_POST['jpn_id'] ?? '')),
                'ppd_id' => trim((string)($_POST['ppd_id'] ?? '')),
                'state' => trim((string)($_POST['state'] ?? '')),
            ];

            $validator = new Validator();

            if (!$validator->validate($data, [
                'name' => 'required|min:3|max:100',
                'email' => 'required|email|max:150',
                'roles' => 'required|alpha_dash|max:50',
            ])) {
                $validationErrors = $validator->errors();
                $error = 'Please complete the form correctly.';
                $user = array_merge($user, $data);
                $this->view('users/edit', compact('error', 'user', 'validationErrors'));
                return;
            }

            if ($this->model->emailExistsForOther($data['email'], $id)) {
                $error = 'Email already exists for another user.';
                $user = array_merge($user, $data);
                $this->view('users/edit', compact('error', 'user'));
                return;
            }

            if (!empty($_POST['password'])) {
                $data['password'] = password_hash((string)$_POST['password'], PASSWORD_DEFAULT);
            }

            $data['jpn_id'] = $data['jpn_id'] !== '' ? $data['jpn_id'] : null;
            $data['ppd_id'] = $data['ppd_id'] !== '' ? $data['ppd_id'] : null;
            $data['state'] = $data['state'] !== '' ? $data['state'] : null;
            $data['updated_at'] = date('Y-m-d H:i:s');

            if ($this->model->update($id, $data)) {
                AuditLogger::log('users', 'update', $id, [
                    'email' => $data['email'],
                    'role' => $data['roles'],
                ]);

                Flash::set('success', 'User updated successfully.');
                $this->redirect('/users');
            }

            $error = 'Failed to update user.';
            $user = array_merge($user, $data);
        }

        $this->view('users/edit', compact('error', 'user'));
        
        
    }

    // public function delete(): void
    // {
    //     Auth::handle();
    //     Role::handle('admin');

    //     $id = (int)($_GET['id'] ?? 0);

    //     if ($id <= 0) {
    //         Flash::set('error', 'Invalid user id.');
    //         $this->redirect('/users');
    //     }

    //     $this->remove($id);
    // }
    public function delete(): void
    {
        Auth::handle();
        Role::handle('admin');

        $id = (int) ($_GET['id'] ?? 0);

        if ($id <= 0) {
            Flash::set('error', 'Invalid user id.');
            $this->redirect('/users');
        }

        if ($this->model->delete($id)) {
            AuditLogger::log('users', 'delete', $id);
            Flash::set('success', 'User deleted successfully.');
        } else {
            Flash::set('error', 'Failed to delete user.');
        }

        $this->redirect('/users');
    }

    public function deleteAjax(): void
    {
        Auth::handle();
        Role::handle('admin');

        if (!Request::isAjax()) {
            $this->json([
                'success' => false,
                'message' => 'Invalid request type.'
            ], 400);
            return;
        }

        $id = (int)($_POST['id'] ?? 0);

        if ($id <= 0) {
            $this->json([
                'success' => false,
                'message' => 'Invalid user id.'
            ], 422);
            return;
        }

        if ($this->model->delete($id)) {
            AuditLogger::log('users', 'delete_ajax', $id);

            $this->json([
                'success' => true,
                'message' => 'User deleted successfully.'
            ]);
            return;
        }

        $this->json([
            'success' => false,
            'message' => 'Failed to delete user.'
        ], 500);
    }
}