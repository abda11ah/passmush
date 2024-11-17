<?php
require_once 'header_warning.php';
?>
<!DOCTYPE html>
<html lang="<?= $_SESSION['lang']; ?>">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= __('share_success'); ?></title>
        <link rel="stylesheet" href="chota.min.css">
        <style>
            body {
                padding: 2rem;
                background: var(--bg-secondary);
            }
            .container {
                margin: 0 auto;
            }
            .card {
                background: white;
                padding: 2rem;
                border-radius: 4px;
            }
            .text-right {
                text-align: right;
            }
            .share-url {
                background: var(--bg-secondary);
                padding: 1rem;
                border-radius: 4px;
                word-break: break-all;
                font-family: monospace;
            }
            .success-title {
                color: var(--color-success);
            }
        </style>
    </head>
    <body>
        <?php showHeader();?>
        <div class="container">
            <?php showInstallWarning(); ?>
            <?php if (isset($_SESSION['success_message'])): ?>
            <div class="success-message" style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 1rem; margin-bottom: 2rem; border-radius: 4px;">
                <?php
                echo htmlspecialchars($_SESSION['success_message']);
                unset($_SESSION['success_message']);
                ?>
            </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="error-message" style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 1rem; margin-bottom: 2rem; border-radius: 4px;">
                    <?php
                    echo htmlspecialchars($_SESSION['error_message']);
                    unset($_SESSION['error_message']);
                    ?>
                </div>
            <?php endif; ?>
            <div class="card">
                <h3 class="text-center success-title"><?= __('share_success'); ?></h3>

                <div class="row">
                    <div class="col">
                        <label><?= __('share_link'); ?></label>
                        <div class="share-url"><?= htmlspecialchars($share_url); ?></div>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <a href="index.php" class="button primary"><?= __('share_another'); ?></a>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>