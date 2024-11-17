<?php
// Prevent direct access to this file
defined('SECURE_ACCESS') or die('Direct access to this file is not allowed');

function showHeader() {
    $logo = COMPANY_LOGO ? '<img src="' . htmlspecialchars(COMPANY_LOGO) . '" alt="Company Logo" style="max-height: 50px; margin-right: 1rem;">' : '';
    $current_uri = $_SERVER['REQUEST_URI'];
    // Remove existing lang parameter if present
    $base_uri = preg_replace('/([?&])lang=[^&]*(&|$)/', '$1', $current_uri);
    $separator = (strpos($base_uri, '?') !== false) ? '&' : '?';
    if (substr($base_uri, -1) === '&') {
        $base_uri = rtrim($base_uri, '&');
        $separator = '&';
    }
    ?>
    <header style="background: white; padding: 1rem; margin-bottom: 2rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <div class="container" style="display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center;">
                <?= $logo; ?>
                <h1 style="margin: 0;"><?= __('page_title'); ?></h1>
            </div>
            <nav>
                <a href="<?= $base_uri . $separator ?>lang=fr" class="<?= $_SESSION['lang'] === 'fr' ? 'active' : ''; ?>">Français</a> |
                <a href="<?= $base_uri . $separator ?>lang=en" class="<?= $_SESSION['lang'] === 'en' ? 'active' : ''; ?>">English</a>
            </nav>
        </div>
    </header>
    <?php
}

function showInstallWarning() {
    if (file_exists(__DIR__ . '/install.php')) {
        ?>
        <div class="warning-banner" style="background: #fff3cd; border: 1px solid #ffeeba; color: #856404; padding: 1rem; margin-bottom: 2rem; border-radius: 4px; display: flex; justify-content: space-between; align-items: center;">
            <span>⚠️ <?= __('install_warning'); ?></span>
            <form method="POST" action="delete_install.php" style="margin: 0;" onsubmit="return confirm('<?= __('confirm_delete_install'); ?>');">
                <button type="submit" class="button error"><?= __('delete_install'); ?></button>
            </form>
        </div>
        <?php
    }
}