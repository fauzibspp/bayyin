<?php

namespace App\Core;

class Pagination
{
    public static function page(): int
    {
        $page = (int)($_GET['page'] ?? 1);
        return max(1, $page);
    }

    public static function perPage(int $default = 10): int
    {
        $perPage = (int)($_GET['per_page'] ?? $default);
        return max(1, min($perPage, 100));
    }

    public static function offset(int $page, int $perPage): int
    {
        return ($page - 1) * $perPage;
    }

    public static function meta(int $total, int $page, int $perPage): array
    {
        $lastPage = (int)ceil($total / $perPage);

        return [
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'last_page' => max(1, $lastPage),
            'has_prev' => $page > 1,
            'has_next' => $page < $lastPage,
            'prev_page' => $page > 1 ? $page - 1 : null,
            'next_page' => $page < $lastPage ? $page + 1 : null,
        ];
    }
}