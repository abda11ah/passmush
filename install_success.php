<?php
// Define secure access constant
define('SECURE_ACCESS', true);
session_start();
require_once 'lang.php';

$current_uri = $_SERVER['REQUEST_URI'];
$base_uri = preg_replace('/([?&])lang=[^&]*(&|$)/', '$1', $current_uri);
$separator = (strpos($base_uri, '?') !== false) ? '&' : '?';
if (substr($base_uri, -1) === '&') {
    $base_uri = rtrim($base_uri, '&');
    $separator = '&';
}
?>
<!DOCTYPE html>
<html lang="<?= $_SESSION['lang']; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= __('installation'); ?></title>
    <link rel="stylesheet" href="chota.min.css">
    <style>
        body { padding: 2rem; background: var(--bg-secondary); }
        .container { margin: 0 auto; }
        .card { background: white; padding: 2rem; border-radius: 4px; }
        .success { color: var(--color-success); }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="text-right">
                <a href="<?= $base_uri . $separator ?>lang=fr" class="<?= $_SESSION['lang'] === 'fr' ? 'active' : ''; ?>">Français</a> |
                <a href="<?= $base_uri . $separator ?>lang=en" class="<?= $_SESSION['lang'] === 'en' ? 'active' : ''; ?>">English</a>
            </div>

            <h1 class="text-center"><?= __('installation'); ?></h1>
            
            <div class="row">
                <div class="col">
                    <h3><?= __('progress'); ?></h3>
                    <?php foreach ($_SESSION['install_messages'] as $message): ?>
                        <p class="message success"><?= htmlspecialchars($message); ?></p>
                    <?php endforeach; ?>
                    <p class="success">✓ <?= __('success'); ?></p>
                    <a href="index.php" class="button primary"><?= __('go_to_app'); ?></a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>