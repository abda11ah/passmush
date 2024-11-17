<?php
// Prevent direct access to this file
defined('SECURE_ACCESS') or die('Direct access to this file is not allowed');

// Check environment before proceeding
$envChecker = new EnvironmentChecker();
$envChecker->checkPHPVersion()
          ->checkPDOExtension()
          ->checkOpenSSLExtension()
          ->checkKeysDirectory();

if ($envChecker->hasErrors()) {
    $errors = $envChecker->getErrors();
    ?>
    <!DOCTYPE html>
    <html lang="<?= $_SESSION['lang']; ?>">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= __('page_title'); ?></title>
        <link rel="stylesheet" href="chota.min.css">
        <style>
            body { padding: 0; background: var(--bg-secondary); }
            .container { margin: 0 auto; }
            .card { background: white; padding: 2rem; border-radius: 4px; }
            .text-right { text-align: right; }
            .error { color: var(--color-error); }
        </style>
    </head>
    <body>
        <?php showHeader(); ?>
        <div class="container">
            <div class="card">
                <h3 class="error"><?= __('errors'); ?></h3>
                <?php foreach ($errors as $error): ?>
                    <p class="error"><?= htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
                <a href="install.php" class="button primary"><?= __('go_to_install'); ?></a>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}