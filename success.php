<?php
require_once 'lang.php';
require_once 'header_warning.php';
?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang']; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo __('share_success'); ?></title>
    <link rel="stylesheet" href="chota.min.css">
    <style>
        body { padding: 2rem; background: var(--bg-secondary); }
        .container { max-width: 600px; margin: 0 auto; }
        .card { background: white; padding: 2rem; border-radius: 4px; }
        .text-right { text-align: right; }
        .share-url {
            background: var(--bg-secondary);
            padding: 1rem;
            border-radius: 4px;
            word-break: break-all;
            font-family: monospace;
        }
        .success-title { color: var(--color-success); }
    </style>
</head>
<body>
    <div class="container">
        <?php 
        showInstallWarning();
        if (isset($_SESSION['success_message'])): ?>
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
            <a href="?lang=fr" class="<?php echo $_SESSION['lang'] === 'fr' ? 'active' : ''; ?>">Français</a> |
            <a href="?lang=en" class="<?php echo $_SESSION['lang'] === 'en' ? 'active' : ''; ?>">English</a>
        </nav>
        
        <div class="card">
            <h3 class="text-center success-title"><?php echo __('share_success'); ?></h3>
            
            <div class="row">
                <div class="col">
                    <label><?php echo __('share_link'); ?></label>
                    <div class="share-url"><?php echo htmlspecialchars($share_url); ?></div>
                </div>
            </div>
            
            <div class="row">
                <div class="col">
                    <a href="index.php" class="button primary"><?php echo __('share_another'); ?></a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>