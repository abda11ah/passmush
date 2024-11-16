<?php
session_start();

require_once 'lang.php';
require_once 'env.inc.php';

class Installer {
    private $messages = [];
    private $errors = [];
    private $config = [];
    private $configWritable = false;

    public function run() {
        // Check if config file is writable
        $this->checkConfigWritable();

        $envChecker = new EnvironmentChecker();
        $envChecker->checkPHPVersion()
                  ->checkPDOExtension()
                  ->checkOpenSSLExtension()
                  ->checkKeysDirectory();

        $this->messages = array_merge($this->messages, $envChecker->getMessages());
        $this->errors = array_merge($this->errors, $envChecker->getErrors());

        // Handle form submissions
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['test_connection'])) {
                $this->testConnection();
            } elseif (isset($_POST['install'])) {
                $this->saveConfig()
                     ->generateSSLKeys()
                     ->createDatabase()
                     ->createTables();
                
                if (empty($this->errors)) {
                    $_SESSION['install_messages'] = $this->messages;
                    header('Location: install_success.php');
                    exit;
                }
            }
        }

        $this->displayForm();
    }

    private function checkConfigWritable() {
        $configFile = 'config.inc.php';
        if (file_exists($configFile)) {
            $this->configWritable = is_writable($configFile);
        } else {
            $this->configWritable = is_writable(dirname($configFile));
        }

        if (!$this->configWritable) {
            $this->errors[] = "✗ " . __('config_not_writable');
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

            // If testing with existing database, check if it exists
            if (isset($_POST['db_name']) && !empty($_POST['db_name'])) {
                $stmt = $pdo->query("SHOW DATABASES LIKE '{$_POST['db_name']}'");
                if ($stmt->rowCount() > 0) {
                    $this->messages[] = "✓ " . __('db_exists');
                } else {
                    $this->errors[] = "✗ " . __('db_not_exists');
                    return;
                }
            }

            $this->messages[] = "✓ " . __('db_connection_success');
        } catch (PDOException $e) {
            $this->errors[] = "✗ " . __('db_connection_error', $e->getMessage());
        }
    }

    private function saveConfig() {
        $config = "<?php\n";
        $config .= "// Database configuration\n";
        $config .= "define('DBHOST', '" . addslashes($_POST['db_host']) . "');\n";
        $config .= "define('DBNAME', '" . addslashes($_POST['db_name']) . "');\n";
        $config .= "define('DBUSER', '" . addslashes($_POST['db_user']) . "');\n";
        $config .= "define('DBPASS', '" . addslashes($_POST['db_pass']) . "');\n";

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
                // Verify the existing database is accessible
                $dbname = $_POST['db_name'];
                $pdo->exec("USE `$dbname`");
                $this->messages[] = "✓ " . __('db_connected');
            }
        } catch (PDOException $e) {
            $this->errors[] = "✗ " . __('db_error', $e->getMessage());
        }

        return $this;
    }

    private function createTables() {
        try {
            $pdo = new PDO(
                "mysql:host={$_POST['db_host']};dbname={$_POST['db_name']}", 
                $_POST['db_user'], 
                $_POST['db_pass']
            );
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $prefix = $_POST['table_prefix'];
            $tableName = $prefix . $_POST['table_name'];

            $pdo->exec("CREATE TABLE IF NOT EXISTS `$tableName` (
                id VARCHAR(32) PRIMARY KEY,
                data TEXT NOT NULL,
                expires_at INT NOT NULL,
                view_limit INT NOT NULL DEFAULT 0,
                view_count INT NOT NULL DEFAULT 0,
                created_at INT NOT NULL DEFAULT UNIX_TIMESTAMP()
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

            $this->messages[] = "✓ " . __('tables_created');
        } catch (PDOException $e) {
            $this->errors[] = "✗ " . __('tables_error', $e->getMessage());
        }

        return $this;
    }

    private function displayForm() {
        global $lang;
        ?>
        <!DOCTYPE html>
        <html lang="<?php echo $lang; ?>">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?php echo __('installation'); ?></title>
            <link rel="stylesheet" href="chota.min.css">
            <style>
                body { padding: 2rem; background: var(--bg-secondary); }
                .container { max-width: 800px; margin: 0 auto; }
                .card { background: white; padding: 2rem; border-radius: 4px; }
                .success { color: var(--color-success); }
                .error { color: var(--color-error); }
                .message { margin: 0.5rem 0; }
                .text-right { text-align: right; }
                fieldset { margin-bottom: 2rem; }
                .hidden { display: none; }
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
                        <a href="?lang=fr" class="<?php echo $lang === 'fr' ? 'active' : ''; ?>">Français</a> |
                        <a href="?lang=en" class="<?php echo $lang === 'en' ? 'active' : ''; ?>">English</a>
                    </div>

                    <h1 class="text-center"><?php echo __('installation'); ?></h1>
                    
                    <?php if (!$this->configWritable): ?>
                        <div class="config-warning">
                            <?php echo __('config_not_writable'); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($this->messages)): ?>
                        <div class="row">
                            <div class="col">
                                <h3><?php echo __('progress'); ?></h3>
                                <?php foreach ($this->messages as $message): ?>
                                    <p class="message success"><?php echo htmlspecialchars($message); ?></p>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($this->errors)): ?>
                        <div class="row">
                            <div class="col">
                                <h3><?php echo __('errors'); ?></h3>
                                <?php foreach ($this->errors as $error): ?>
                                    <p class="message error"><?php echo htmlspecialchars($error); ?></p>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Test Connection Form -->
                    <form method="post" action="">
                        <fieldset>
                            <legend><?php echo __('test_connection'); ?></legend>
                            <div class="row">
                                <div class="col">
                                    <label for="test_host"><?php echo __('db_host'); ?></label>
                                    <input type="text" id="test_host" name="db_host" value="localhost" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <label for="test_user"><?php echo __('db_user'); ?></label>
                                    <input type="text" id="test_user" name="db_user" value="root" required>
                                </div>
                                <div class="col">
                                    <label for="test_pass"><?php echo __('db_pass'); ?></label>
                                    <input type="password" id="test_pass" name="db_pass">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <label for="test_db"><?php echo __('db_name'); ?> (<?php echo __('optional'); ?>)</label>
                                    <input type="text" id="test_db" name="db_name" placeholder="<?php echo __('existing_db_name'); ?>">
                                </div>
                            </div>
                            <button type="submit" name="test_connection" class="button"><?php echo __('test_connection'); ?></button>
                        </fieldset>
                    </form>

                    <!-- Installation Form -->
                    <form method="post" action="">
                        <fieldset>
                            <legend><?php echo __('db_configuration'); ?></legend>
                            <div class="row">
                                <div class="col">
                                    <label for="db_host"><?php echo __('db_host'); ?></label>
                                    <input type="text" id="db_host" name="db_host" value="localhost" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <label for="db_user"><?php echo __('db_user'); ?></label>
                                    <input type="text" id="db_user" name="db_user" value="root" required>
                                </div>
                                <div class="col">
                                    <label for="db_pass"><?php echo __('db_pass'); ?></label>
                                    <input type="password" id="db_pass" name="db_pass">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <label><?php echo __('db_create_type'); ?></label>
                                    <label>
                                        <input type="radio" name="db_create" value="new" checked onchange="toggleDatabaseFields()">
                                        <?php echo __('db_create_new'); ?>
                                    </label>
                                    <label>
                                        <input type="radio" name="db_create" value="existing" onchange="toggleDatabaseFields()">
                                        <?php echo __('db_use_existing'); ?>
                                    </label>
                                </div>
                            </div>
                            <div id="new-db-fields">
                                <div class="row">
                                    <div class="col">
                                        <label for="new_db_name"><?php echo __('db_name'); ?></label>
                                        <input type="text" id="new_db_name" name="db_name" value="password_share">
                                    </div>
                                </div>
                            </div>
                            <div id="existing-db-fields" class="hidden">
                                <div class="row">
                                    <div class="col">
                                        <label for="existing_db_name"><?php echo __('existing_db_name'); ?></label>
                                        <input type="text" id="existing_db_name" name="db_name" placeholder="<?php echo __('enter_existing_db'); ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <label for="table_name"><?php echo __('table_name'); ?></label>
                                    <input type="text" id="table_name" name="table_name" value="passwords" required>
                                </div>
                                <div class="col">
                                    <label for="table_prefix"><?php echo __('table_prefix'); ?></label>
                                    <input type="text" id="table_prefix" name="table_prefix" placeholder="<?php echo __('optional'); ?>">
                                </div>
                            </div>
                        </fieldset>

                        <div class="row">
                            <div class="col">
                                <button type="submit" name="install" class="button primary" <?php echo $this->configWritable ? '' : 'disabled'; ?>><?php echo __('install'); ?></button>
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