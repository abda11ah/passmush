<?php
// Prevent direct access to this file
defined('SECURE_ACCESS') or die('Direct access to this file is not allowed');

require_once 'config.inc.php';

try {
    $pdo = new PDO('mysql:host='.DBHOST.';dbname='.DBNAME.';charset=utf8mb4', DBUSER, DBPASS);
} catch(PDOException $e) {
    $_SESSION['exception_error'] = __("error_occurred"). $e->getMessage();
    header('Location: exception.php');
    exit();
}