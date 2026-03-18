<?php

namespace App\Core;

class AuditLogger
{
    public static function log(
        string $module,
        string $action,
        ?int $recordId = null,
        array $context = []
    ): void {
        $logDir = dirname(__DIR__, 2) . '/storage/logs';

        if (!is_dir($logDir)) {
            mkdir($logDir, 0775, true);
        }

        $entry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'user_id' => $_SESSION['user_id'] ?? null,
            'user' => $_SESSION['email'] ?? $_SESSION['user'] ?? 'Guest',
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'module' => $module,
            'action' => $action,
            'record_id' => $recordId,
            'context' => $context,
        ];

        file_put_contents(
            $logDir . '/audit.log',
            json_encode($entry, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL,
            FILE_APPEND
        );
    }
}