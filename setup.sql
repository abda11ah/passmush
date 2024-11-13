-- Create the database
CREATE DATABASE password_share;

-- Create the passwords table
CREATE TABLE IF NOT EXISTS passwords (
    id VARCHAR(32) PRIMARY KEY,
    password TEXT NOT NULL,
    expires_at INTEGER NOT NULL,
    view_limit INTEGER NOT NULL DEFAULT 0,
    view_count INTEGER NOT NULL DEFAULT 0,
    created_at INTEGER NOT NULL DEFAULT EXTRACT(EPOCH FROM CURRENT_TIMESTAMP)
);
