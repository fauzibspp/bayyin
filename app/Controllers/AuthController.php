<?php

namespace App\Controllers;

use App\Core\CSRF;
use App\Core\Logger;
use App\Core\RateLimiter;
use App\Core\Request;
use App\Core\Session;
use App\Core\Validator;
use App\Core\Flash;
use App\Middleware\Guest;
use App\Models\UserModel;


class AuthController extends BaseController
{
    private UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function login(): void
    {
        Guest::handle();

        $error = null;
        $success = Flash::get('success');

        if (Request::isPost()) {
            RateLimiter::hit('login_' . Request::ip(), 5, 60);

            if (!CSRF::verify((string) ($_POST['csrf'] ?? ''))) {
                $error = 'CSRF token mismatch.';
                $this->view('auth/login', compact('error', 'success'), null);
                return;
            }

            $data = [
                'email'    => trim(strtolower((string) ($_POST['email'] ?? ''))),
                'password' => (string) ($_POST['password'] ?? ''),
            ];

            $validator = new Validator();

            if (!$validator->validate($data, [
                'email'    => 'required|email',
                'password' => 'required|min:6',
            ])) {
                $error = 'Please enter valid login information.';
                $this->view('auth/login', compact('error', 'success'), null);
                return;
            }

            $user = $this->userModel->login($data['email'], $data['password']);

            if ($user) {
                Session::regenerate();
                Session::put('user_id', $user['id']);
                Session::put('user', $user['name']);
                Session::put('email', $user['email']);
                Session::put('role', $user['roles']);

                Logger::info('Successful login', $user['email']);
                $this->redirect('/');
            }

            Logger::warning('Failed login attempt', $data['email']);
            $error = 'Invalid email or password.';
        }

        $this->view('auth/login', compact('error', 'success'), null);
    }

    public function register(): void
    {
        Guest::handle();

        $error = null;

        if (Request::isPost()) {
            if (!CSRF::verify((string) ($_POST['csrf'] ?? ''))) {
                $error = 'CSRF token mismatch.';
                $this->view('auth/register', compact('error'), null);
                return;
            }

            $data = [
                'name'     => trim((string) ($_POST['name'] ?? '')),
                'email'    => trim(strtolower((string) ($_POST['email'] ?? ''))),
                'password' => (string) ($_POST['password'] ?? ''),
                'roles'    => trim((string) ($_POST['roles'] ?? '')),
                'jpn_id'   => trim((string) ($_POST['jpn_id'] ?? '')),
                'ppd_id'   => trim((string) ($_POST['ppd_id'] ?? '')),
                'state'    => trim((string) ($_POST['state'] ?? '')),
            ];

            $validator = new Validator();

            if (!$validator->validate($data, [
                'name'     => 'required|min:3|max:100',
                'email'    => 'required|email|max:150',
                'password' => 'required|min:6|max:100',
                'roles'    => 'required|alpha_dash|max:50',
            ])) {
                $error = 'Please complete the registration form correctly.';
                $this->view('auth/register', compact('error'), null);
                return;
            }

            if ($this->userModel->register($data)) {
                Logger::info('New user registered', $data['email']);
                Flash::set('success', 'Registration successful. Please sign in.');
                $this->redirect('/login');
            }

            $error = 'Email already registered or registration failed.';
        }

        $this->view('auth/register', compact('error'), null);
    }

    public function logout(): void
    {
        Session::destroy();

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'] ?? '/',
                $params['domain'] ?? '',
                $params['secure'] ?? false,
                $params['httponly'] ?? true
            );
        }

        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        $this->redirect('/login');
    }
}