<?php

use App\Core\Database;

return new class {
    public function up(Database $db): void
    {
        $db->execute("
            CREATE TABLE IF NOT EXISTS samples (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NULL,
                status VARCHAR(255) NULL,
                notes TEXT NULL,
                is_active TINYINT(1) NULL,
                deleted_at DATETIME NULL,
                created_at DATETIME NULL,
                updated_at DATETIME NULL
            )
        ");
    }

    public function down(Database $db): void
    {
        $db->execute("DROP TABLE IF EXISTS samples");
    }
};