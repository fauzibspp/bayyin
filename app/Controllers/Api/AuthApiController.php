<?php

namespace App\Controllers\Api;

use App\Controllers\ApiController;
use App\Core\Jwt;
use App\Core\JsonRequest;
use App\Middleware\JwtAuth;
use App\Models\UserModel;

class AuthApiController extends ApiController
{
    private UserModel $users;

    public function __construct()
    {
        $this->users = new UserModel();
    }

    public function login(): void
    {
        $input = JsonRequest::all();

        $email = trim(strtolower((string) ($input['email'] ?? '')));
        $password = (string) ($input['password'] ?? '');

        $errors = [];

        if ($email === '') {
            $errors['email'][] = 'Email is required.';
        }

        if ($password === '') {
            $errors['password'][] = 'Password is required.';
        }

        if (!empty($errors)) {
            $this->error('Validation failed.', 422, $errors);
        }

        $user = $this->users->login($email, $password);

        if (!$user) {
            $this->error('Invalid credentials.', 401, [
                'auth' => ['Email or password is incorrect.']
            ]);
        }

        $token = Jwt::encode([
            'sub' => (int) $user['id'],
            'email' => $user['email'],
            'role' => $user['roles'] ?? 'user',
            'name' => $user['name'] ?? '',
        ]);

        $this->success([
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => Jwt::ttl(),
            'user' => [
                'id' => (int) $user['id'],
                'name' => $user['name'] ?? '',
                'email' => $user['email'],
                'role' => $user['roles'] ?? 'user',
            ],
        ], 'Login successful.');
    }

    public function me(): void
    {
        JwtAuth::handle();

        $user = JwtAuth::user();

        $this->success([
            'id' => $user['sub'] ?? null,
            'name' => $user['name'] ?? '',
            'email' => $user['email'] ?? '',
            'role' => $user['role'] ?? '',
            'iat' => $user['iat'] ?? null,
            'exp' => $user['exp'] ?? null,
        ], 'Authenticated user fetched successfully.');
    }
}