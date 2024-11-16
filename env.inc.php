<?php
// Prevent direct access to this file
defined('SECURE_ACCESS') or die('Direct access to this file is not allowed');

class EnvironmentChecker {
    private $messages = [];
    private $errors = [];
    private $configWritable = false;

    public function checkPHPVersion() {
        if (version_compare(PHP_VERSION, '7.4.0', '>=')) {
            $this->messages[] = "✓ " . __('php_version_ok', PHP_VERSION);
        } else {
            $this->errors[] = "✗ " . __('php_version_error', PHP_VERSION);
        }
        return $this;
    }

    public function checkPDOExtension() {
        if (extension_loaded('pdo_mysql')) {
            $this->messages[] = "✓ " . __('pdo_ok');
        } else {
            $this->errors[] = "✗ " . __('pdo_error');
        }
        return $this;
    }

    public function checkOpenSSLExtension() {
        if (extension_loaded('openssl')) {
            $this->messages[] = "✓ " . __('openssl_ok');
        } else {
            $this->errors[] = "✗ " . __('openssl_error');
        }
        return $this;
    }

    public function checkKeysDirectory() {
        $keysDir = __DIR__ . '/keys';
        
        if (!file_exists($keysDir)) {
            if (mkdir($keysDir, 0755, true)) {
                $this->messages[] = "✓ " . __('keys_dir_created');
            } else {
                $this->errors[] = "✗ " . __('keys_dir_error');
                return $this;
            }
        }

        if (is_writable($keysDir)) {
            $this->messages[] = "✓ " . __('keys_dir_writable');
        } else {
            $this->errors[] = "✗ " . __('keys_dir_not_writable');
        }

        return $this;
    }

    public function checkConfigWritable() {
        $configFile = 'config.inc.php';
        if (file_exists($configFile)) {
            $this->configWritable = is_writable($configFile);
        } else {
            $this->configWritable = is_writable(dirname($configFile));
        }

        if (!$this->configWritable) {
            $this->errors[] = "✗ " . __('config_not_writable');
        }
        return $this;
    }

    public function isConfigWritable() {
        return $this->configWritable;
    }

    public function getMessages() {
        return $this->messages;
    }

    public function getErrors() {
        return $this->errors;
    }

    public function hasErrors() {
        return !empty($this->errors);
    }
}