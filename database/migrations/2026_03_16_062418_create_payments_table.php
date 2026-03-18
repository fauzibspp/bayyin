<?php

use App\Core\Database;

return new class {
    public function up(Database $db): void
    {
        $db->execute("
            CREATE TABLE IF NOT EXISTS payments (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                amount DECIMAL(12,2) NOT NULL,
                status VARCHAR(255) NOT NULL,
                notes TEXT NOT NULL,
                is_paid TINYINT(1) NOT NULL,
                deleted_at DATETIME NOT NULL,
                created_at DATETIME NULL,
                updated_at DATETIME NULL
            )
        ");
    }

    public function down(Database $db): void
    {
        $db->execute("DROP TABLE IF EXISTS payments");
    }
};