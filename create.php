<?php
require_once 'db.php';
require_once 'encryption.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $encryption = new Encryption();
    $password = $_POST['password'];
    $encrypted_password = $encryption->encrypt($password);
    $expires = (int)$_POST['expires'];
    $view_limit = (int)$_POST['view_limit'];
    
    // Generate unique ID
    $id = bin2hex(random_bytes(16));
    
    // Calculate expiration timestamp
    $expiration = time() + ($expires * 3600);
    
    // Store only the encrypted password
    $stmt = $pdo->prepare("INSERT INTO passwords (id, password, expires_at, view_limit, created_at) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$id, $encrypted_password, $expiration, $view_limit, time()]);
    
    $http = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://');
    $share_url = $http . $_SERVER['HTTP_HOST'] . "/view.php?id=" . $id;
    
    // Return success page with URL
    require_once 'success.php';
} else {
    header('Location: index.php');
    exit();
}