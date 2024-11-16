<?php
function showInstallWarning() {
    if (file_exists(__DIR__ . '/install.php')) {
        ?>
        <div class="warning-banner" style="background: #fff3cd; border: 1px solid #ffeeba; color: #856404; padding: 1rem; margin-bottom: 2rem; border-radius: 4px; display: flex; justify-content: space-between; align-items: center;">
            <span>⚠️ <?php echo __('install_warning'); ?></span>
            <form method="POST" action="delete_install.php" style="margin: 0;" onsubmit="return confirm('<?php echo __('confirm_delete_install'); ?>');">
                <button type="submit" class="button error"><?php echo __('delete_install'); ?></button>
            </form>
        </div>
        <?php
    }
}
?>