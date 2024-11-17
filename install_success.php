<?php
// Define secure access constant
define('SECURE_ACCESS', true);

require_once 'lang.php';
?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang']; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo __('installation'); ?></title>
    <link rel="stylesheet" href="chota.min.css">
    <style>
        body { padding: 2rem; background: var(--bg-secondary); }
        .container { max-width: 800px; margin: 0 auto; }
        .card { background: white; padding: 2rem; border-radius: 4px; }
        .success { color: var(--color-success); }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="text-right">
                <a href="?lang=fr" class="<?php echo $_SESSION['lang'] === 'fr' ? 'active' : ''; ?>">Français</a> |
                <a href="?lang=en" class="<?php echo $_SESSION['lang'] === 'en' ? 'active' : ''; ?>">English</a>
            </div>

            <h1 class="text-center"><?php echo __('installation'); ?></h1>
            
            <div class="row">
                <div class="col">
                    <h3><?php echo __('progress'); ?></h3>
                    <?php foreach ($_SESSION['install_messages'] as $message): ?>
                        <p class="message success"><?php echo htmlspecialchars($message); ?></p>
                    <?php endforeach; ?>
                    <p class="success">✓ <?php echo __('success'); ?></p>
                    <a href="index.php" class="button primary"><?php echo __('go_to_app'); ?></a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>