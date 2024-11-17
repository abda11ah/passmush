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
                padding: 1.5rem;
                border-radius: 4px;
                word-break: break-all;
                font-family: monospace;
                margin-bottom: 1rem;
                border: 1px solid var(--color-primary);
                background-color: rgba(var(--color-primary-rgb), 0.1);
                position: relative;
            }
            .success-title {
                color: var(--color-success);
            }
            .url-container {
                position: relative;
                margin-bottom: 2rem;
            }
            .copy-button {
                position: absolute;
                top: 50%;
                right: 1rem;
                transform: translateY(-50%);
                background: var(--color-primary);
                color: white;
                border: none;
                padding: 0.5rem 1rem;
                border-radius: 4px;
                cursor: pointer;
                transition: opacity 0.2s ease;
            }
            .copy-button:hover {
                opacity: 0.9;
            }
            .copy-button.copied {
                background: var(--color-success);
            }
            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }
            .copied-tooltip {
                position: absolute;
                background: var(--color-success);
                color: white;
                padding: 0.5rem 1rem;
                border-radius: 4px;
                font-size: 0.9rem;
                bottom: calc(100% + 10px);
                right: 0;
                animation: fadeIn 0.3s ease;
            }
            .copied-tooltip::after {
                content: '';
                position: absolute;
                top: 100%;
                right: 1rem;
                border-left: 8px solid transparent;
                border-right: 8px solid transparent;
                border-top: 8px solid var(--color-success);
            }
        </style>
        <script>
            function copyToClipboard() {
                const urlText = document.querySelector('.share-url').textContent;
                const button = document.querySelector('.copy-button');
                const container = document.querySelector('.url-container');
                
                navigator.clipboard.writeText(urlText).then(() => {
                    // Add copied class to button
                    button.classList.add('copied');
                    
                    // Create and show tooltip
                    const tooltip = document.createElement('div');
                    tooltip.className = 'copied-tooltip';
                    tooltip.textContent = '<?= __('copied'); ?>';
                    container.appendChild(tooltip);
                    
                    // Reset after 2 seconds
                    setTimeout(() => {
                        button.classList.remove('copied');
                        tooltip.remove();
                    }, 2000);
                });
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
                <h3 class="text-center success-title"><?= __('share_success'); ?></h3>

                <div class="row">
                    <div class="col">
                        <label><?= __('share_link'); ?></label>
                        <div class="url-container">
                            <div class="share-url"><?= htmlspecialchars($share_url); ?></div>
                            <button onclick="copyToClipboard()" class="copy-button"><?= __('copy_clipboard'); ?></button>
                        </div>
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