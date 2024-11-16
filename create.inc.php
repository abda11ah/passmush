<?php
// Define secure access constant
define('SECURE_ACCESS', true);

if (!file_exists('config.inc.php') || (filesize('config.inc.php') === 0)) {
    header('Location: install.php');
    exit();
}

require_once 'lang.php';
require_once 'env.inc.php';

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
            <title><?php echo __('shared_password'); ?></title>
            <link rel="stylesheet" href="chota.min.css">
            <style>
                body {
                    padding: 2rem;
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
                .error {
                    color: var(--color-error);
                }
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'db.inc.php';
    require_once 'enc.inc.php';

    $enc = new Encryption();
    $encrypted = $enc->encrypt($_POST['data']);
    $expires = (int) $_POST['expires'];
    $view_limit = (int) $_POST['view_limit'];

    // Generate unique ID
    $id = bin2hex(random_bytes(16));

    // Calculate expiration timestamp
    $expiration = time() + ($expires * 3600);

    // Store the encrypted data
    $table = DBTABLE_PREFIX . DBTABLE_NAME;
    $stmt = $pdo->prepare("INSERT INTO {$table} (id, data, expires_at, view_limit, created_at) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$id, $encrypted, $expiration, $view_limit, time()]);

    $http = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://');
    $share_url = $http . $_SERVER['HTTP_HOST'] . '/view.php?id=' . $id;

    // Return success page with URL
    require_once 'success.php';
} else {
    header('Location: index.php');
    exit();
}