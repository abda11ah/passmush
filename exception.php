<?php
// Define secure access constant
define('SECURE_ACCESS', true);

require_once 'lang.php';
require_once 'config.inc.php';
require_once 'header_warning.php';

// Get error message from session
$error_message = $_SESSION['exception_error'] ?? 'An unknown error occurred';
unset($_SESSION['exception_error']);
?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang']; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo __('error_occurred'); ?></title>
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
            margin-top: 2rem;
        }
        .error-icon {
            font-size: 3rem;
            color: var(--color-error);
            text-align: center;
            margin-bottom: 1rem;
        }
        .error-message {
            color: var(--color-error);
            background: #fff5f5;
            border: 1px solid #feb2b2;
            padding: 1rem;
            border-radius: 4px;
            margin: 1rem 0;
        }
        .button-container {
            margin-top: 2rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <?php showHeader(); ?>
    <div class="container">
        <?php showInstallWarning(); ?>
        <div class="card">
            <div class="error-icon">⚠️</div>
            <h2 class="text-center"><?php echo __('error_occurred'); ?></h2>
            <div class="error-message">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
            <div class="button-container">
                <a href="index.php" class="button primary"><?php echo __('return_home'); ?></a>
            </div>
        </div>
    </div>
</body>
</html>