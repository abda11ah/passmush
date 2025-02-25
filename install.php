<?php
// Define secure access constant
define('SECURE_ACCESS', true);
session_start();

require_once 'lang.php';
require_once 'env.inc.php';

class Installer {

    private $messages = [];
    private $errors = [];
    private $config = [];
    private $envChecker;
    private $uploadsDir = 'uploads';

    public function __construct() {
        $this->envChecker = new EnvironmentChecker();
    }

    public function run() {
        // Check environment requirements
        $this->envChecker->checkPHPVersion()
                ->checkPDOExtension()
                ->checkOpenSSLExtension()
                ->checkKeysDirectory()
                ->checkConfigWritable();

        $this->messages = array_merge($this->messages, $this->envChecker->getMessages());
        $this->errors = array_merge($this->errors, $this->envChecker->getErrors());

        // Create uploads directory if it doesn't exist
        if (!file_exists($this->uploadsDir)) {
            if (mkdir($this->uploadsDir, 0755, true)) {
                $this->messages[] = "✓ " . __('uploads_dir_created');
            } else {
                $this->errors[] = "✗ " . __('uploads_dir_error');
            }
        }

        // Handle form submissions
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['test_connection'])) {
                $this->testConnection();
            } elseif (isset($_POST['install'])) {
                $logoPath = $this->handleLogoUpload();
                $this->saveConfig($logoPath)
                        ->generateSSLKeys()
                        ->createDatabase()
                        ->createTables();

                if (!$this->hasErrors()) {
                    $_SESSION['install_messages'] = $this->messages;
                    header('Location: install_success.php');
                    exit;
                }
            }
        }

        $this->displayForm();
    }

    private function handleLogoUpload() {
        if (!isset($_FILES['company_logo']) || $_FILES['company_logo']['error'] === UPLOAD_ERR_NO_FILE) {
            return '';
        }

        $file = $_FILES['company_logo'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        // Validate file type
        if (!in_array($file['type'], $allowedTypes)) {
            $this->errors[] = "✗ " . __('logo_type_error');
            return '';
        }

        // Validate file size
        if ($file['size'] > $maxSize) {
            $this->errors[] = "✗ " . __('logo_size_error');
            return '';
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'company_logo_' . uniqid() . '.' . $extension;
        $destination = $this->uploadsDir . '/' . $filename;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            $this->messages[] = "✓ " . __('logo_uploaded');
            return $destination;
        } else {
            $this->errors[] = "✗ " . __('logo_upload_error');
            return '';
        }
    }

    private function testConnection() {
        try {
            $pdo = new PDO(
                    "mysql:host={$_POST['db_host']}",
                    $_POST['db_user'],
                    $_POST['db_pass']
            );
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            if ($_POST['db_create'] === 'existing' && !empty($_POST['existing_db_name'])) {
                try {
                    $pdo->query("USE " . $pdo->quote($_POST['existing_db_name']));
                    $this->messages[] = "✓ " . __('db_connected');
                } catch (PDOException $e) {
                    $this->errors[] = "✗ " . __('db_not_exists');
                    return;
                }
            }

            $this->messages[] = "✓ " . __('db_connection_success');
        } catch (PDOException $e) {
            $this->errors[] = "✗ " . __('db_connection_error', $e->getMessage());
        }
    }

    private function saveConfig($logoPath = '') {
        $config = "<?php\n";
        $config .= "// Prevent direct access to this file\n";
        $config .= "defined('SECURE_ACCESS') or die('Direct access to this file is not allowed');\n\n";
        $config .= "// Database configuration\n";
        $config .= "define('DBHOST', '" . addslashes($_POST['db_host']) . "');\n";
        $config .= "define('DBNAME', '" . addslashes($_POST['db_create'] === 'existing' ? $_POST['existing_db_name'] : $_POST['db_name']) . "');\n";
        $config .= "define('DBUSER', '" . addslashes($_POST['db_user']) . "');\n";
        $config .= "define('DBPASS', '" . addslashes($_POST['db_pass']) . "');\n";
        $config .= "define('DBTABLE_PREFIX', '" . addslashes($_POST['table_prefix']) . "'); // Optional table prefix\n";
        $config .= "define('DBTABLE_NAME', '" . addslashes($_POST['table_name']) . "'); // Table name for storing passwords\n\n";
        $config .= "// Company configuration\n";
        $config .= "define('COMPANY_LOGO', '" . addslashes($logoPath) . "'); // Path to company logo\n";

        if (file_put_contents('config.inc.php', $config) === false) {
            $this->errors[] = "✗ " . __('config_write_error');
        } else {
            $this->messages[] = "✓ " . __('config_write_success');
            $this->config = $_POST;
        }
        return $this;
    }

    private function generateSSLKeys() {
        $privateKeyPath = __DIR__ . '/keys/private.key';
        $publicKeyPath = __DIR__ . '/keys/public.key';

        if (file_exists($privateKeyPath) && file_exists($publicKeyPath)) {
            $this->messages[] = "✓ " . __('keys_exist');
            return $this;
        }

        $config = [
            "private_key_bits" => 2048,
            "private_key_type" => OPENSSL_KEYTYPE_RSA
        ];

        try {
            $privateKey = openssl_pkey_new($config);
            if (!$privateKey) {
                throw new Exception("Failed to generate private key");
            }

            openssl_pkey_export($privateKey, $privateKeyPEM);
            file_put_contents($privateKeyPath, $privateKeyPEM);

            $keyDetails = openssl_pkey_get_details($privateKey);
            file_put_contents($publicKeyPath, $keyDetails["key"]);

            $this->messages[] = "✓ " . __('keys_generated');
        } catch (Exception $e) {
            $this->errors[] = "✗ " . __('keys_error', $e->getMessage());
        }

        return $this;
    }

    private function createDatabase() {
        try {
            $pdo = new PDO(
                    "mysql:host={$_POST['db_host']}",
                    $_POST['db_user'],
                    $_POST['db_pass']
            );
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            if ($_POST['db_create'] === 'new') {
                $dbname = $_POST['db_name'];
                $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` 
                           DEFAULT CHARACTER SET utf8mb4 
                           DEFAULT COLLATE utf8mb4_unicode_ci");
                $this->messages[] = "✓ " . __('db_created');
            } else {
                $dbname = $_POST['existing_db_name'];
                $pdo->query("USE `$dbname`");
                $this->messages[] = "✓ " . __('db_exists');
            }
        } catch (PDOException $e) {
            $this->errors[] = "✗ " . __('db_error', $e->getMessage());
        }

        return $this;
    }

    private function createTables() {
        try {
            $dbname = $_POST['db_create'] === 'existing' ? $_POST['existing_db_name'] : $_POST['db_name'];
            $pdo = new PDO(
                    "mysql:host={$_POST['db_host']};dbname=$dbname",
                    $_POST['db_user'],
                    $_POST['db_pass']
            );
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $prefix = $_POST['table_prefix'];
            $tableName = $prefix . $_POST['table_name'];

            $pdo->exec("CREATE TABLE IF NOT EXISTS `$tableName` (
            id VARCHAR(32) PRIMARY KEY,
            data TEXT NOT NULL,
            expires_at BIGINT UNSIGNED NOT NULL,
            view_limit INT NOT NULL DEFAULT 0,
            view_count INT NOT NULL DEFAULT 0,
            created_at BIGINT UNSIGNED NOT NULL,
            ip_address VARCHAR(45) NOT NULL,
            INDEX idx_ip_created (ip_address, created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

            $this->messages[] = "✓ " . __('tables_created');
        } catch (PDOException $e) {
            $this->errors[] = "✗ " . __('tables_error', $e->getMessage());
        }

        return $this;
    }

    private function hasErrors() {
        return !empty($this->errors);
    }

    private function displayForm() {
        global $lang;
        $current_uri = $_SERVER['REQUEST_URI'];
        $base_uri = preg_replace('/([?&])lang=[^&]*(&|$)/', '$1', $current_uri);
        $separator = (strpos($base_uri, '?') !== false) ? '&' : '?';
        if (substr($base_uri, -1) === '&') {
            $base_uri = rtrim($base_uri, '&');
            $separator = '&';
        }
        ?>
        <!DOCTYPE html>
        <html lang="<?= $lang; ?>">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title><?= __('installation'); ?></title>
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
                    .success {
                        color: var(--color-success);
                    }
                    .error {
                        color: var(--color-error);
                    }
                    .message {
                        margin: 0.5rem 0;
                    }
                    .text-right {
                        text-align: right;
                    }
                    fieldset {
                        margin-bottom: 2rem;
                    }
                    .hidden {
                        display: none;
                    }
                    .button[disabled] {
                        opacity: 0.5;
                        cursor: not-allowed;
                        pointer-events: none;
                    }
                    .config-warning {
                        background-color: #fff3cd;
                        border: 1px solid #ffeeba;
                        color: #856404;
                        padding: 1rem;
                        margin-bottom: 1rem;
                        border-radius: 4px;
                    }
                </style>
                <script>
                    function toggleDatabaseFields() {
                        const createType = document.querySelector('input[name="db_create"]:checked').value;
                        const newDbFields = document.getElementById('new-db-fields');
                        const existingDbFields = document.getElementById('existing-db-fields');

                        newDbFields.classList.toggle('hidden', createType !== 'new');
                        existingDbFields.classList.toggle('hidden', createType !== 'existing');
                    }
                </script>
            </head>
            <body>
                <div class="container">
                    <div class="card">
                        <div class="text-right">
                            <a href="<?= $base_uri . $separator ?>lang=fr" class="<?= $lang === 'fr' ? 'active' : ''; ?>">Français</a> |
                            <a href="<?= $base_uri . $separator ?>lang=en" class="<?= $lang === 'en' ? 'active' : ''; ?>">English</a>
                        </div>

                        <h1 class="text-center"><?= __('installation'); ?></h1>

                        <?php if (!$this->envChecker->isConfigWritable()): ?>
                            <div class="config-warning">
                                <?= __('config_not_writable'); ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($this->messages)): ?>
                            <div class="row">
                                <div class="col">
                                    <h3><?= __('progress'); ?></h3>
                                    <?php foreach ($this->messages as $message): ?>
                                        <p class="message success"><?= htmlspecialchars($message); ?></p>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($this->errors)): ?>
                            <div class="row">
                                <div class="col">
                                    <h3><?= __('errors'); ?></h3>
                                    <?php foreach ($this->errors as $error): ?>
                                        <p class="message error"><?= htmlspecialchars($error); ?></p>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Test Connection Form -->
                        <form method="post" action="">
                            <fieldset>
                                <legend><?= __('test_connection'); ?></legend>
                                <div class="row">
                                    <div class="col">
                                        <label for="test_host"><?= __('db_host'); ?></label>
                                        <input type="text" id="test_host" name="db_host" value="localhost" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <label for="test_user"><?= __('db_user'); ?></label>
                                        <input type="text" id="test_user" name="db_user" value="root" required>
                                    </div>
                                    <div class="col">
                                        <label for="test_pass"><?= __('db_pass'); ?></label>
                                        <input type="password" id="test_pass" name="db_pass">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <label><?= __('db_create_type'); ?></label>
                                        <label>
                                            <input type="radio" name="db_create" value="new" checked onchange="toggleDatabaseFields()">
                                            <?= __('db_create_new'); ?>
                                        </label>
                                        <label>
                                            <input type="radio" name="db_create" value="existing" onchange="toggleDatabaseFields()">
                                            <?= __('db_use_existing'); ?>
                                        </label>
                                    </div>
                                </div>
                                <div id="existing-db-fields" class="hidden">
                                    <div class="row">
                                        <div class="col">
                                            <label for="existing_db_name"><?= __('existing_db_name'); ?></label>
                                            <input type="text" id="existing_db_name" name="existing_db_name" placeholder="<?= __('enter_existing_db'); ?>">
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" name="test_connection" class="button"><?= __('test_connection'); ?></button>
                            </fieldset>
                        </form>

                        <!-- Installation Form -->
                        <form method="post" action="" enctype="multipart/form-data">
                            <fieldset>
                                <legend><?= __('company_info'); ?></legend>
                                <div class="row">
                                    <div class="col">
                                        <label for="company_logo"><?= __('company_logo'); ?></label>
                                        <input type="file" id="company_logo" name="company_logo" accept="image/jpeg,image/png,image/gif">
                                        <small><?= __('logo_requirements'); ?></small>
                                    </div>
                                </div>
                            </fieldset>

                            <fieldset>
                                <legend><?= __('db_configuration'); ?></legend>
                                <div class="row">
                                    <div class="col">
                                        <label for="db_host"><?= __('db_host'); ?></label>
                                        <input type="text" id="db_host" name="db_host" value="localhost" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <label for="db_user"><?= __('db_user'); ?></label>
                                        <input type="text" id="db_user" name="db_user" value="root" required>
                                    </div>
                                    <div class="col">
                                        <label for="db_pass"><?= __('db_pass'); ?></label>
                                        <input type="password" id="db_pass" name="db_pass">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <label><?= __('db_create_type'); ?></label>
                                        <label>
                                            <input type="radio" name="db_create" value="new" checked onchange="toggleDatabaseFields()">
                                            <?= __('db_create_new'); ?>
                                        </label>
                                        <label>
                                            <input type="radio" name="db_create" value="existing" onchange="toggleDatabaseFields()">
                                            <?= __('db_use_existing'); ?>
                                        </label>
                                    </div>
                                </div>
                                <div id="new-db-fields">
                                    <div class="row">
                                        <div class="col">
                                            <label for="db_name"><?= __('db_name'); ?></label>
                                            <input type="text" id="db_name" name="db_name" value="password_share" required>
                                        </div>
                                    </div>
                                </div>
                                <div id="existing-db-fields" class="hidden">
                                    <div class="row">
                                        <div class="col">
                                            <label for="existing_db_name"><?= __('existing_db_name'); ?></label>
                                            <input type="text" id="existing_db_name" name="existing_db_name" placeholder="<?= __('enter_existing_db'); ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <label for="table_name"><?= __('table_name'); ?></label>
                                        <input type="text" id="table_name" name="table_name" value="passwords" required>
                                    </div>
                                    <div class="col">
                                        <label for="table_prefix"><?= __('table_prefix'); ?></label>
                                        <input type="text" id="table_prefix" name="table_prefix" placeholder="<?= __('optional'); ?>">
                                    </div>
                                </div>
                            </fieldset>

                            <div class="row">
                                <div class="col">
                                    <button type="submit" name="install" class="button primary" <?= $this->envChecker->isConfigWritable() ? '' : 'disabled'; ?>><?= __('install'); ?></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </body>
        </html>
        <?php
    }
}

$installer = new Installer();
$installer->run();
?>