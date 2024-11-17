<?php
// Define secure access constant
define('SECURE_ACCESS', true);


if (!file_exists('config.inc.php') || (filesize('config.inc.php') === 0)) {
    header('Location: install.php');
    exit();
}

session_start();

require_once 'lang.php';
require_once 'env.inc.php';
require_once 'config.inc.php';
require_once 'header_warning.php';
require_once 'checkenv.inc.php';

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
        .password-group {
            display: flex;
            gap: 0.5rem;
        }
        .password-group input {
            flex: 1;
            transition: filter 0.3s ease;
        }
        .password-group input.blur {
            filter: blur(5px);
        }
        .password-group input.blur:focus {
            filter: none;
        }
        .tab-content {
            display: none;
            padding-top: 2rem;
        }
        .tab-content.active {
            display: block;
        }
        textarea {
            min-height: 150px;
            resize: vertical;
        }
    </style>
    <script>
    function generatePassword() {
        const length = Math.floor(Math.random() * (16 - 8 + 1)) + 8; // Random length between 8 and 16
        const charset = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789?!%@_-$*';
        let password = '';
        
        // Ensure at least one of each required type
        password += 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'[Math.floor(Math.random() * 26)]; // Uppercase
        password += 'abcdefghijklmnopqrstuvwxyz'[Math.floor(Math.random() * 26)]; // Lowercase
        password += '0123456789'[Math.floor(Math.random() * 10)]; // Number
        password += '?!%@_-$*'[Math.floor(Math.random() * 8)]; // Special char
        
        // Fill the rest randomly
        for (let i = password.length; i < length; i++) {
            password += charset[Math.floor(Math.random() * charset.length)];
        }
        
        // Shuffle the password
        password = password.split('').sort(() => Math.random() - 0.5).join('');
        
        const input = document.getElementById('password-input');
        input.value = password;
        input.classList.remove('blur');
        
        setTimeout(() => {
            input.classList.add('blur');
        }, 3000);
    }

    function switchTab(tabId, element) {
        // Remove active class from all tabs and contents
        document.querySelectorAll('.tabs a').forEach(tab => tab.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
        
        // Add active class to selected tab and content
        element.classList.add('active');
        document.getElementById(tabId).classList.add('active');
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
            <nav class="tabs">
                <a href="#" class="active" onclick="switchTab('password-tab', this); return false;"><?= __('share_password'); ?></a>
                <a href="#" onclick="switchTab('text-tab', this); return false;"><?= __('share_text'); ?></a>
            </nav>
            <div id="password-tab" class="tab-content active">
                <form action="create.inc.php" method="POST">
                    <input type="hidden" name="type" value="password">
                    <div class="row">
                        <div class="col">
                            <label><?= __('password_to_share'); ?></label>
                            <div class="password-group">
                                <input type="text" name="data" id="password-input" required onmouseover="this.classList.remove('blur');" onmouseout="if (this.value.trim() !== '') {this.classList.add('blur');}">
                                <button type="button" onclick="generatePassword()" class="button outline">
                                    <?= __('generate'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col">
                            <label><?= __('expires_after'); ?></label>
                            <select name="expires" required>
                                <?php foreach (__('time_options') as $value => $label): ?>
                                    <option value="<?= $value; ?>"><?= $label; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            <label><?= __('view_limit'); ?></label>
                            <select name="view_limit" required>
                                <?php foreach (__('view_options') as $value => $label): ?>
                                    <option value="<?= $value; ?>"><?= $label; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div align="center">
                        <button type="submit" class="button primary"><?= __('generate_link'); ?></button>
                    </div>
                </form>
            </div>

            <div id="text-tab" class="tab-content">
                <form action="create.inc.php" method="POST">
                    <input type="hidden" name="type" value="text">
                    <div class="row">
                        <div class="col">
                            <label><?= __('text_to_share'); ?></label>
                            <textarea name="data" required></textarea>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col">
                            <label><?= __('expires_after'); ?></label>
                            <select name="expires" required>
                                <?php foreach (__('time_options') as $value => $label): ?>
                                    <option value="<?= $value; ?>"><?= $label; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            <label><?= __('view_limit'); ?></label>
                            <select name="view_limit" required>
                                <?php foreach (__('view_options') as $value => $label): ?>
                                    <option value="<?= $value; ?>"><?= $label; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div align="center">
                        <button type="submit" class="button primary"><?= __('generate_link'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>