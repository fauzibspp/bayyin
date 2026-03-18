<?php

use App\Core\Database;

return new class {
    public function up(Database $db): void
    {
        $db->execute("
            CREATE TABLE IF NOT EXISTS users (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                email VARCHAR(150) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                roles VARCHAR(50) NOT NULL DEFAULT 'sekolah',
                jpn_id BIGINT NULL,
                ppd_id BIGINT NULL,
                state VARCHAR(100) NULL,
                created_at DATETIME NULL,
                updated_at DATETIME NULL
            )
        ");
    }

    public function down(Database $db): void
    {
        $db->execute("DROP TABLE IF EXISTS users");
    }
};