<?php

namespace App\Core;

class Upload
{
    public static function save(
        array $file,
        string $destination = 'uploads',
        array $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf'],
        int $maxSize = 5242880
    ): ?string {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return null;
        }

        if (($file['size'] ?? 0) > $maxSize) {
            return null;
        }

        $ext = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExtensions, true)) {
            return null;
        }

        $basePath = dirname(__DIR__, 2) . '/public/' . trim($destination, '/');

        if (!is_dir($basePath)) {
            mkdir($basePath, 0775, true);
        }

        $filename = uniqid('file_', true) . '.' . $ext;
        $target = $basePath . '/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $target)) {
            return null;
        }

        return '/' . trim($destination, '/') . '/' . $filename;
    }
}