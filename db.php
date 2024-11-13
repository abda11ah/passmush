<?php
$host = 'localhost';
$dbname = 'password_share';
$username = 'postgres';
$password = '0000';

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create table if it doesn't exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS passwords (
        id VARCHAR(32) PRIMARY KEY,
        password TEXT NOT NULL,
        expires_at INTEGER NOT NULL,
        view_limit INTEGER NOT NULL DEFAULT 0,
        view_count INTEGER NOT NULL DEFAULT 0,
        created_at INTEGER NOT NULL DEFAULT EXTRACT(EPOCH FROM CURRENT_TIMESTAMP)
    )");
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
