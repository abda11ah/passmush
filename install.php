<?php
session_start();

// Set language based on browser preference
$browser_lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '', 0, 2);
$lang = in_array($browser_lang, ['fr', 'en']) ? $browser_lang : 'en';

if (isset($_GET['lang']) && in_array($_GET['lang'], ['fr', 'en'])) {
    $lang = $_GET['lang'];
}
require 'config.inc.php';
$translations = [
    'en' => [
        'installation' => 'Password Share Installation',
        'progress' => 'Installation Progress:',
        'errors' => 'Errors:',
        'success' => 'Installation completed successfully!',
        'failure' => 'Installation failed. Please fix the errors and try again.',
        'go_to_app' => 'Go to Application',
        'retry' => 'Retry Installation',
        'php_version_ok' => 'PHP version %s is compatible',
        'php_version_error' => 'PHP version 7.4.0 or higher is required. Current version: %s',
        'pdo_ok' => 'PDO MySQL extension is installed',
        'pdo_error' => 'PDO MySQL extension is required but not installed',
        'openssl_ok' => 'OpenSSL extension is installed',
        'openssl_error' => 'OpenSSL extension is required but not installed',
        'keys_dir_created' => 'Keys directory created successfully',
        'keys_dir_error' => 'Failed to create keys directory',
        'keys_dir_writable' => 'Keys directory is writable',
        'keys_dir_not_writable' => 'Keys directory is not writable. Please set proper permissions',
        'keys_exist' => 'SSL keys already exist',
        'keys_generated' => 'SSL keys generated successfully',
        'keys_error' => 'Failed to generate SSL keys: %s',
        'db_created' => 'Database created successfully',
        'db_error' => 'Database creation failed: %s',
        'tables_created' => 'Tables created successfully',
        'tables_error' => 'Table creation failed: %s'
    ],
    'fr' => [
        'installation' => 'Installation du Partage de Mot de Passe',
        'progress' => 'Progression de l\'installation :',
        'errors' => 'Erreurs :',
        'success' => 'Installation terminée avec succès !',
        'failure' => 'L\'installation a échoué. Veuillez corriger les erreurs et réessayer.',
        'go_to_app' => 'Aller à l\'Application',
        'retry' => 'Réessayer l\'Installation',
        'php_version_ok' => 'Version PHP %s compatible',
        'php_version_error' => 'PHP version 7.4.0 ou supérieure requise. Version actuelle : %s',
        'pdo_ok' => 'Extension PDO MySQL installée',
        'pdo_error' => 'Extension PDO MySQL requise mais non installée',
        'openssl_ok' => 'Extension OpenSSL installée',
        'openssl_error' => 'Extension OpenSSL requise mais non installée',
        'keys_dir_created' => 'Répertoire des clés créé avec succès',
        'keys_dir_error' => 'Échec de la création du répertoire des clés',
        'keys_dir_writable' => 'Répertoire des clés accessible en écriture',
        'keys_dir_not_writable' => 'Répertoire des clés non accessible en écriture. Veuillez définir les permissions appropriées',
        'keys_exist' => 'Les clés SSL existent déjà',
        'keys_generated' => 'Clés SSL générées avec succès',
        'keys_error' => 'Échec de la génération des clés SSL : %s',
        'db_created' => 'Base de données créée avec succès',
        'db_error' => 'Échec de la création de la base de données : %s',
        'tables_created' => 'Tables créées avec succès',
        'tables_error' => 'Échec de la création des tables : %s'
    ]
];

function __($key, ...$args) {
    global $translations, $lang;
    $text = $translations[$lang][$key] ?? $key;
    return $args ? sprintf($text, ...$args) : $text;
}

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
        $this->checkPHPVersion()
             ->checkPDOExtension()
             ->checkOpenSSLExtension()
             ->createDatabase()
             ->createTables();
        if ($this->createKeysDirectory()) {$this->generateSSLKeys();}
        $this->displayResults();
    }

    private function checkPHPVersion() {
        if (version_compare(PHP_VERSION, '7.4.0', '>=')) {
            $this->messages[] = "✓ " . __('php_version_ok', PHP_VERSION);
        } else {
            $this->errors[] = "✗ " . __('php_version_error', PHP_VERSION);
        }
        return $this;
    }

    private function checkPDOExtension() {
        if (extension_loaded('pdo_mysql')) {
            $this->messages[] = "✓ " . __('pdo_ok');
        } else {
            $this->errors[] = "✗ " . __('pdo_error');
        }
        return $this;
    }

    private function checkOpenSSLExtension() {
        if (extension_loaded('openssl')) {
            $this->messages[] = "✓ " . __('openssl_ok');
        } else {
            $this->errors[] = "✗ " . __('openssl_error');
        }
        return $this;
    }

    private function createKeysDirectory() {
        $keysDir = __DIR__ . '/keys';
        
        if (!file_exists($keysDir)) {
            if (mkdir($keysDir, 0755, true)) {
                $this->messages[] = "✓ " . __('keys_dir_created');
            } else {
                $this->errors[] = "✗ " . __('keys_dir_error');
                return false;
            }
        }

        if (is_writable($keysDir)) {
            $this->messages[] = "✓ " . __('keys_dir_writable');
        } else {
            $this->errors[] = "✗ " . __('keys_dir_not_writable');
            return false;
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
                password TEXT NOT NULL,
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

    private function displayResults() {global $lang;
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
