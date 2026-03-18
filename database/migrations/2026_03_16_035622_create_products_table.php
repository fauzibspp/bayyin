<?php

use App\Core\Database;

return new class {
    public function up(Database $db): void
    {
        $db->execute("
            CREATE TABLE IF NOT EXISTS products (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                price DECIMAL(12,2) NOT NULL,
                stock INT NOT NULL,
                is_active TINYINT(1) NOT NULL,
                description TEXT NOT NULL,
                created_at DATETIME NULL,
                updated_at DATETIME NULL
            )
        ");
    }

    public function down(Database $db): void
    {
        $db->execute("DROP TABLE IF EXISTS products");
    }
};