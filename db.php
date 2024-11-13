<?php
$host = 'localhost';
$dbname = 'password_share';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create table if it doesn't exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS passwords (
        id VARCHAR(32) PRIMARY KEY,
        password TEXT NOT NULL,
        expires_at INT NOT NULL,
        view_limit INT NOT NULL DEFAULT 0,
        view_count INT NOT NULL DEFAULT 0,
        created_at INT NOT NULL DEFAULT UNIX_TIMESTAMP()
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}