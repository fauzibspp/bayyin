CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    roles VARCHAR(50) NOT NULL DEFAULT 'admin',
    created_at DATETIME NULL,
    updated_at DATETIME NULL
);

-- password: passwrord123
INSERT INTO users (name, email, password, roles, created_at)
VALUES (
    'Administrator',
    'admin@example.com',
    '$2y$10$8lN1EOnWB6owYW6mG0kpPuuSmPxv4kiEJIKPj5A3qB/W5QzEfNo8y',
    'admin',
    NOW()
);

