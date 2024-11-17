<?php
// Define secure access constant
define('SECURE_ACCESS', true);
session_start();

require_once 'lang.php';
require_once 'env.inc.php';
require_once 'checkenv.inc.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'crypt.inc.php';
    require_once 'db.inc.php';

    // Get client IP address
    $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
    
    // Check rate limit (5 attempts per minute)
    $table = DBTABLE_PREFIX . DBTABLE_NAME;
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM {$table} 
                          WHERE ip_address = ? 
                          AND created_at > ?");
    $oneMinuteAgo = time() - 60;
    $stmt->execute([$ip_address, $oneMinuteAgo]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result['count'] >= 5) {
        
        $_SESSION['error_message'] = __('rate_limit_exceeded');
        header('Location: index.php');
        exit();
    }

    $enc = new Encryption();
    $encrypted = $enc->encrypt($_POST['data']);
    $expires = (int) $_POST['expires'];
    $view_limit = (int) $_POST['view_limit'];

    // Generate unique ID
    $id = bin2hex(random_bytes(16));

    // Calculate expiration timestamp
    // Use 64-bit max integer for unlimited
    $expiration = $expires === -1 ? 9223372036854775807 : time() + ($expires * 3600);

    // Store the encrypted data with IP address
    $stmt = $pdo->prepare("INSERT INTO {$table} 
                          (id, data, expires_at, view_limit, view_count, created_at, ip_address) 
                          VALUES (?, ?, ?, ?, 0, ?, ?)");
    $stmt->execute([$id, $encrypted, $expiration, $view_limit, time(), $ip_address]);

    $http = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://');
    $share_url = $http . $_SERVER['HTTP_HOST'] . '/view.php?id=' . $id;

    // Return success page with URL
    require_once 'success.php';
} else {
    header('Location: index.php');
    exit();
}