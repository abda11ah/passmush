<?php
require_once 'lang.php';
?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang']; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo __('page_title'); ?></title>
    <link rel="stylesheet" href="chota.min.css">
    <style>
        body { padding: 2rem; background: var(--bg-secondary); }
        .container { max-width: 600px; margin: 0 auto; }
        .card { background: white; padding: 2rem; border-radius: 4px; }
        .text-right { text-align: right; }
        .password-group {
            display: flex;
            gap: 0.5rem;
        }
        .password-group input {
            flex: 1;
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
        
        document.getElementById('password-input').value = password;
        document.getElementById('password-input').type = 'text';
        // The generated password will be visible for 3 seconds before being masked again
        setTimeout(() => {
            document.getElementById('password-input').type = 'password';
        }, 3000);
    }
    </script>
</head>
<body>
    <div class="container">
        <nav class="text-right">
            <a href="?lang=fr" class="<?php echo $_SESSION['lang'] === 'fr' ? 'active' : ''; ?>">Français</a> |
            <a href="?lang=en" class="<?php echo $_SESSION['lang'] === 'en' ? 'active' : ''; ?>">English</a>
        </nav>
        
        <h1 class="text-center"><?php echo __('page_title'); ?></h1>
        
        <div class="card">
            <form action="create.php" method="POST">
                <div class="row">
                    <div class="col">
                        <label><?php echo __('password_to_share'); ?></label>
                        <div class="password-group">
                            <input type="password" name="password" id="password-input" required>
                            <button type="button" onclick="generatePassword()" class="button outline">
                                <?php echo __('generate'); ?>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col">
                        <label><?php echo __('expires_after'); ?></label>
                        <select name="expires" required>
                            <?php foreach (__('time_options') as $value => $label): ?>
                                <option value="<?php echo $value; ?>"><?php echo $label; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <label><?php echo __('view_limit'); ?></label>
                        <select name="view_limit" required>
                            <?php foreach (__('view_options') as $value => $label): ?>
                                <option value="<?php echo $value; ?>"><?php echo $label; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <button type="submit" class="button primary"><?php echo __('generate_link'); ?></button>
            </form>
        </div>
    </div>
</body>
</html>
