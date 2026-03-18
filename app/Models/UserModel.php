<?php

namespace App\Models;

class UserModel extends BaseModel
{
    protected string $table = 'users';

    protected array $fillable = [
        'name',
        'email',
        'password',
        'roles',
        'jpn_id',
        'ppd_id',
        'state',
        'created_at',
        'updated_at',
    ];

    public function findByEmail(string $email): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM users WHERE email = ? LIMIT 1",
            [trim(strtolower($email))]
        );
    }

    public function register(array $data): bool
    {
        if ($this->findByEmail($data['email'])) {
            return false;
        }

        $id = $this->create([
            'name'       => trim($data['name']),
            'email'      => trim(strtolower($data['email'])),
            'password'   => password_hash($data['password'], PASSWORD_DEFAULT),
            'roles'      => $data['roles'],
            'jpn_id'     => $data['jpn_id'] !== '' ? $data['jpn_id'] : null,
            'ppd_id'     => $data['ppd_id'] !== '' ? $data['ppd_id'] : null,
            'state'      => $data['state'] !== '' ? $data['state'] : null,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return $id > 0;
    }

    public function login(string $email, string $password): ?array
    {
        $user = $this->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            return null;
        }

        if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
            $newHash = password_hash($password, PASSWORD_DEFAULT);

            $this->update((int)$user['id'], [
                'password' => $newHash,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $user['password'] = $newHash;
        }

        return $user;
    }

    public function emailExistsForOther(string $email, int $id): bool
    {
        $row = $this->db->fetch(
            "SELECT id FROM users WHERE email = ? AND id != ? LIMIT 1",
            [trim(strtolower($email)), $id]
        );

        return !empty($row);
    }
}