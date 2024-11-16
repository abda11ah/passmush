<?php
require_once 'lang.php';
require_once 'env.inc.php';
require_once 'header_warning.php';

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
            .error { color: var(--color-error); }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="card">
                <h3 class="error"><?php echo __('errors'); ?></h3>
                <?php foreach ($errors as $error): ?>
                    <p class="error"><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
                <a href="install.php" class="button primary"><?php echo __('go_to_install'); ?></a>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}
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
        
        <h1 class="text-center"><?php echo __('page_title'); ?></h1>
        
        <div class="card">
            <nav class="tabs">
                <a href="#" class="active" onclick="switchTab('password-tab', this); return false;"><?php echo __('share_password'); ?></a>
                <a href="#" onclick="switchTab('text-tab', this); return false;"><?php echo __('share_text'); ?></a>
            </nav>

            <div id="password-tab" class="tab-content active">
                <form action="create.php" method="POST">
                    <input type="hidden" name="type" value="password">
                    <div class="row">
                        <div class="col">
                            <label><?php echo __('password_to_share'); ?></label>
                            <div class="password-group">
                                <input type="text" name="data" id="password-input" required onmouseover="this.classList.remove('blur');" onmouseout="if (this.value.trim() !== '') {this.classList.add('blur');}">
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
                    
                    <div align="center">
                        <button type="submit" class="button primary"><?php echo __('generate_link'); ?></button>
                    </div>
                </form>
            </div>

            <div id="text-tab" class="tab-content">
                <form action="create.php" method="POST">
                    <input type="hidden" name="type" value="text">
                    <div class="row">
                        <div class="col">
                            <label><?php echo __('text_to_share'); ?></label>
                            <textarea name="data" required></textarea>
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
                    
                    <div align="center">
                        <button type="submit" class="button primary"><?php echo __('generate_link'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
