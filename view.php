<?php
require_once 'lang.php';
require_once 'db.php';

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$id = $_GET['id'];
$current_time = time();
$stmt = $pdo->prepare("SELECT * FROM passwords WHERE id = ? AND expires_at > ?");
$stmt->execute([$id, $current_time]);
$password = $stmt->fetch();

if (!$password) {
    $error = __('link_expired');
} elseif ($password['view_limit'] > 0 && $password['view_count'] >= $password['view_limit']) {
    $error = __('max_views_reached');
} else {
    $stmt = $pdo->prepare("UPDATE passwords SET view_count = view_count + 1 WHERE id = ?");
    $stmt->execute([$id]);
    $password['view_count']++;
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
        body { padding: 2rem; background: var(--bg-secondary); }
        .container { max-width: 600px; margin: 0 auto; }
        .card { background: white; padding: 2rem; border-radius: 4px; }
        .text-right { text-align: right; }
        .password-display { 
            background: var(--bg-secondary);
            padding: 1rem;
            border-radius: 4px;
            font-family: monospace;
        }
        .error { color: var(--color-error); }
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
    </script>
</head>
<body>
    <div class="container">
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
                            <span id="password-text"><?php echo htmlspecialchars($password['original_password']); ?></span>
                        </div>
                        <button id="copy-btn" onclick="copyToClipboard()" class="button primary"><?php echo __('copy_clipboard'); ?></button>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <small>
                            <p><?php echo __('expires'); ?> <?php echo date('Y-m-d H:i:s', $password['expires_at']); ?></p>
                            <?php if ($password['view_limit'] > 0): ?>
                                <p><?php echo __('views_remaining'); ?> <?php echo $password['view_limit'] - $password['view_count']; ?> <?php echo __('of'); ?> <?php echo $password['view_limit']; ?></p>
                            <?php endif; ?>
                        </small>
                    </div>
                </div>
            <?php endif; ?>
            
            <div class="row">
                <div class="col">
                    <a href="index.php" class="button primary"><?php echo __('share_another'); ?></a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
