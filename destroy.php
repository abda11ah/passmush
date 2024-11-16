<?php
require_once 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'])) {
    echo json_encode(['success' => false]);
    exit();
}

try {
    $table = DBTABLE_PREFIX . 'passwords';
    $stmt = $pdo->prepare("DELETE FROM {$table} WHERE id = ?");
    $success = $stmt->execute([$_POST['id']]);
    echo json_encode(['success' => $success]);
} catch (Exception $e) {
    echo json_encode(['success' => false]);
}