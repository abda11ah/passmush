<?php
// Define secure access constant
define('SECURE_ACCESS', true);

require_once 'db.inc.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'])) {
    echo json_encode(['success' => false]);
    exit();
}

try {
    $table = DBTABLE_PREFIX . DBTABLE_NAME;
    $stmt = $pdo->prepare("DELETE FROM {$table} WHERE id = ?");
    $success = $stmt->execute([$_POST['id']]);
    echo json_encode(['success' => $success]);
} catch (Exception $e) {
    echo json_encode(['success' => false]);
}