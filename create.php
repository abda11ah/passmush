<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'];
    $expires = (int)$_POST['expires'];
    $view_limit = (int)$_POST['view_limit'];
    
    // Generate unique ID
    $id = bin2hex(random_bytes(16));
    
    // Calculate expiration timestamp
    $expiration = time() + ($expires * 3600);
    
    // Store the original password (not hashed since we need to display it later)
    $stmt = $pdo->prepare("INSERT INTO passwords (id, password, expires_at, view_limit, created_at) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$id, $password, $expiration, $view_limit, time()]);
    
    $share_url = "http://" . $_SERVER['HTTP_HOST'] . "/view.php?id=" . $id;
    
    // Return success page with URL
    require_once 'success.php';
} else {
    header('Location: index.php');
    exit();
}
