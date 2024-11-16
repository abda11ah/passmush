<?php
// Define secure access constant
define('SECURE_ACCESS', true);

session_start();
require_once 'lang.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (file_exists('install.php') && unlink('install.php')) {
        $_SESSION['success_message'] = __('install_deleted');
    } else {
        $_SESSION['error_message'] = __('install_delete_error');
    }
}

// Redirect back to the referring page
header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php'));
exit;
?>