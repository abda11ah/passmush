<?php
// Define secure access constant
define('SECURE_ACCESS', true);
session_start();

require_once 'lang.php';
require_once 'env.inc.php';
require_once 'header_warning.php';
require_once 'checkenv.inc.php';
require_once 'db.inc.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$id = $_GET['id'];
$current_time = time();
$table = DBTABLE_PREFIX . DBTABLE_NAME;
$stmt = $pdo->prepare("SELECT * FROM {$table} WHERE id = ? AND (expires_at > ? OR expires_at = 9223372036854775807)");
$stmt->execute([$id, $current_time]);
$row = $stmt->fetch();

if (!$row) {
    $error = __('link_expired');
} elseif ($row['view_limit'] > 0 && $row['view_count'] >= $row['view_limit']) {
    $error = __('max_views_reached');
} else {
    $stmt = $pdo->prepare("UPDATE {$table} SET view_count = view_count + 1 WHERE id = ?");
    $stmt->execute([$id]);
    $row['view_count']++;
}
?>
<!DOCTYPE html>
<html lang="<?= $_SESSION['lang']; ?>">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= __('shared_password'); ?></title>
        <link rel="stylesheet" href="chota.min.css">
        <style>
            body {
                padding: 0;
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
            .password-display {
                background: var(--bg-secondary);
                padding: 1rem;
                border-radius: 4px;
                font-family: monospace;
            }
            .error {
                color: var(--color-error);
            }
            .button-group {
                display: flex;
                gap: 1rem;
                margin-top: 1rem;
            }
            .button.error {
                background: var(--color-error);
                border-color: var(--color-error);
            }
            .button.error:hover {
                background: var(--color-error);
                filter: brightness(90%);
            }
        </style>
        <script>
            function copyToClipboard() {
                const passwordText = document.getElementById('password-text').textContent;
                navigator.clipboard.writeText(passwordText).then(() => {
                    const copyBtn = document.getElementById('copy-btn');
                    copyBtn.textContent = '<?= __('copied'); ?>';
                    setTimeout(() => {
                        copyBtn.textContent = '<?= __('copy_clipboard'); ?>';
                    }, 2000);
                });
            }

            function destroyPassword() {
                if (confirm('<?= __('confirm_destroy'); ?>')) {
                    fetch('destroy.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'id=<?= urlencode($id); ?>'
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('<?= __('password_destroyed'); ?>');
                            window.location.href = 'index.php';
                        } else {
                            alert('<?= __('destroy_error'); ?>');
                        }
                    })
                    .catch(() => alert('<?= __('destroy_error'); ?>'));
                }
            }
        </script>
    </head>
    <body>
        <?php showHeader(); ?>
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
                <?php if (isset($error)): ?>
                    <p class="error text-center"><?= htmlspecialchars($error); ?></p>
                <?php else: ?>
                    <h3 class="text-center"><?= __('shared_password'); ?></h3>
                    <div class="row">
                        <div class="col">
                            <label><?= __('password'); ?></label>
                            <div class="password-display">
                                <?php require_once 'crypt.inc.php';
                                $enc = new Encryption();
                                ?>
                                <span id="password-text"><?= nl2br(htmlspecialchars($enc->decrypt($row['data']))); ?></span>
                            </div>
                            <button id="copy-btn" onclick="copyToClipboard()" class="button primary"><?= __('copy_clipboard'); ?></button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <small>
                                <?php if ($row['expires_at'] !== 9223372036854775807): ?>
                                    <p><?= __('expires'); ?> <?= date(__('date_format'), $row['expires_at']); ?></p>
                                <?php else: ?>
                                    <p><?= __('no_expiration'); ?></p>
                                <?php endif; ?>
                                <?php if ($row['view_limit'] > 0): ?>
                                    <p><?= __('views_remaining'); ?> <?= $row['view_limit'] - $row['view_count']; ?> <?= __('of'); ?> <?= $row['view_limit']; ?></p>
                                <?php endif; ?>
                            </small>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <div class="col">
                        <div class="button-group">
                            <a href="index.php" class="button primary"><?= __('share_another'); ?></a>
                            <?php if (!isset($error)): ?>
                                <button onclick="destroyPassword()" class="button error"><?= __('destroy_password'); ?></button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>