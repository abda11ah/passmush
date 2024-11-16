<?php
// Define secure access constant
define('SECURE_ACCESS', true);

if (!file_exists('config.inc.php') || (filesize('config.inc.php') === 0)) {
    header('Location: install.php');
    exit();
}

require_once 'lang.php';
require_once 'env.inc.php';
require_once 'config.inc.php';
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
$stmt = $pdo->prepare("SELECT * FROM {$table} WHERE id = ? AND expires_at > ?");
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
<html lang="<?php echo $_SESSION['lang']; ?>">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo __('shared_password'); ?></title>
        <link rel="stylesheet" href="chota.min.css">
        <style>
            body {
                padding: 0;
                background: var(--bg-secondary);
            }
            .container {
                max-width: 600px;
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
                    copyBtn.textContent = '<?php echo __('copied'); ?>';
                    setTimeout(() => {
                        copyBtn.textContent = '<?php echo __('copy_clipboard'); ?>';
                    }, 2000);
                });
            }

            function destroyPassword() {
                if (confirm('<?php echo __('confirm_destroy'); ?>')) {
                    fetch('destroy.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'id=<?php echo urlencode($id); ?>'
                    })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    alert('<?php echo __('password_destroyed'); ?>');
                                    window.location.href = 'index.php';
                                } else {
                                    alert('<?php echo __('destroy_error'); ?>');
                                }
                            })
                            .catch(() => alert('<?php echo __('destroy_error'); ?>'));
                }
            }
        </script>
    </head>
    <body>
        <div class="container">
            <?php
            showInstallWarning();
            if (isset($_SESSION['success_message'])):
                ?>
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

            <nav class="text-right">
                <a href="?lang=fr&id=<?php echo htmlspecialchars($id); ?>" class="<?php echo $_SESSION['lang'] === 'fr' ? 'active' : ''; ?>">Français</a> |
                <a href="?lang=en&id=<?php echo htmlspecialchars($id); ?>" class="<?php echo $_SESSION['lang'] === 'en' ? 'active' : ''; ?>">English</a>
            </nav>

            <div class="card">
                <?php if (isset($error)): ?>
                    <p class="error text-center"><?php echo htmlspecialchars($error); ?></p>
                <?php else: ?>
                    <h3 class="text-center"><?php echo __('shared_password'); ?></h3>
                    <div class="row">
                        <div class="col">
                            <label><?php echo __('password'); ?></label>
                            <div class="password-display">
                                <?php require_once 'enc.inc.php';
                                $enc = new Encryption();
                                ?>
                                <span id="password-text"><?php echo htmlspecialchars($enc->decrypt($row['data'])); ?></span>
                            </div>
                            <button id="copy-btn" onclick="copyToClipboard()" class="button primary"><?php echo __('copy_clipboard'); ?></button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <small>
                                <p><?php echo __('expires'); ?> <?php echo date(__('date_format'), $row['expires_at']); ?></p>
                                <?php if ($row['view_limit'] > 0): ?>
                                    <p><?php echo __('views_remaining'); ?> <?php echo $row['view_limit'] - $row['view_count']; ?> <?php echo __('of'); ?> <?php echo $row['view_limit']; ?></p>
    <?php endif; ?>
                            </small>
                        </div>
                    </div>
<?php endif; ?>

                <div class="row">
                    <div class="col">
                        <div class="button-group">
                            <a href="index.php" class="button primary"><?php echo __('share_another'); ?></a>
                            <?php if (!isset($error)): ?>
                                <button onclick="destroyPassword()" class="button error"><?php echo __('destroy_password'); ?></button>
<?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>