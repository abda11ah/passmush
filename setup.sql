-- Create the database
CREATE DATABASE IF NOT EXISTS password_share
    DEFAULT CHARACTER SET utf8mb4
    DEFAULT COLLATE utf8mb4_unicode_ci;

-- Use the database
USE password_share;

-- Create the passwords table
CREATE TABLE IF NOT EXISTS passwords (
    id VARCHAR(32) PRIMARY KEY,
    password TEXT NOT NULL,
    expires_at INT NOT NULL,
    view_limit INT NOT NULL DEFAULT 0,
    view_count INT NOT NULL DEFAULT 0,
    created_at INT NOT NULL DEFAULT UNIX_TIMESTAMP()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;