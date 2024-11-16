<?php
// Prevent direct access to this file
defined('SECURE_ACCESS') or die('Direct access to this file is not allowed');

require 'config.inc.php';

try {
    $pdo = new PDO('mysql:host='.DBHOST.';dbname='.DBNAME.';charset=utf8mb4', DBUSER, DBPASS);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}