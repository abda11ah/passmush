<?php
// Define secure access constant
define('SECURE_ACCESS', true);

require_once 'lang.php';
require_once 'env.inc.php';
require_once 'checkenv.inc.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'db.inc.php';
    require_once 'crypt.inc.php';

    $enc = new Encryption();
    $encrypted = $enc->encrypt($_POST['data']);
    $expires = (int) $_POST['expires'];
    $view_limit = (int) $_POST['view_limit'];

    // Generate unique ID
    $id = bin2hex(random_bytes(16));

    // Calculate expiration timestamp
    // Use 64-bit max integer for unlimited
    $expiration = $expires === -1 ? 9223372036854775807 : time() + ($expires * 3600);

    // Store the encrypted data
    $table = DBTABLE_PREFIX . DBTABLE_NAME;
    $stmt = $pdo->prepare("INSERT INTO {$table} (id, data, expires_at, view_limit, created_at) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$id, $encrypted, $expiration, $view_limit, time()]);

    $http = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://');
    $share_url = $http . $_SERVER['HTTP_HOST'] . '/view.php?id=' . $id;

    // Return success page with URL
    require_once 'success.php';
} else {
    header('Location: index.php');
    exit();
}