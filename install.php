<?php
session_start();

require_once 'lang.php';
require_once 'env.inc.php';
require 'config.inc.php';

class Installer {
    private $messages = [];
    private $errors = [];
    private $config = [
        'host' => DBHOST,
        'dbname' => DBNAME,
        'username' => DBUSER,
        'password' => DBPASS
    ];

    public function run() {
        $envChecker = new EnvironmentChecker();
        $envChecker->checkPHPVersion()
                  ->checkPDOExtension()
                  ->checkOpenSSLExtension()
                  ->checkKeysDirectory();

        $this->messages = array_merge($this->messages, $envChecker->getMessages());
        $this->errors = array_merge($this->errors, $envChecker->getErrors());

        if (!$envChecker->hasErrors()) {
            $this->generateSSLKeys()
                 ->createDatabase()
                 ->createTables();
        }

        $this->displayResults();
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
                "mysql:host={$this->config['host']}", 
                $this->config['username'], 
                $this->config['password']
            );
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $dbname = $this->config['dbname'];
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` 
                       DEFAULT CHARACTER SET utf8mb4 
                       DEFAULT COLLATE utf8mb4_unicode_ci");
            
            $this->messages[] = "✓ " . __('db_created');
        } catch (PDOException $e) {
            $this->errors[] = "✗ " . __('db_error', $e->getMessage());
        }

        return $this;
    }

    private function createTables() {
        try {
            $pdo = new PDO(
                "mysql:host={$this->config['host']};dbname={$this->config['dbname']}", 
                $this->config['username'], 
                $this->config['password']
            );
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $pdo->exec("CREATE TABLE IF NOT EXISTS passwords (
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

    private function displayResults() {
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
            </style>
        </head>
        <body>
            <div class="container">
                <div class="card">
                    <div class="text-right">
                        <a href="?lang=fr" class="<?php echo $lang === 'fr' ? 'active' : ''; ?>">Français</a> |
                        <a href="?lang=en" class="<?php echo $lang === 'en' ? 'active' : ''; ?>">English</a>
                    </div>

                    <h1 class="text-center"><?php echo __('installation'); ?></h1>
                    
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

                    <div class="row">
                        <div class="col">
                            <?php if (empty($this->errors)): ?>
                                <p class="success">✓ <?php echo __('success'); ?></p>
                                <a href="index.php" class="button primary"><?php echo __('go_to_app'); ?></a>
                            <?php else: ?>
                                <p class="error">✗ <?php echo __('failure'); ?></p>
                                <button onclick="location.reload()" class="button error"><?php echo __('retry'); ?></button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </body>
        </html>
        <?php
    }
}

$installer = new Installer();
$installer->run();