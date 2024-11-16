<?php
require 'config.inc.php';

try {
    $pdo = new PDO('mysql:host='.DBHOST.';dbname='.DBNAME.';charset=utf8mb4', DBUSER, DBPASS);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}