<?php
class EnvironmentChecker {
    private $messages = [];
    private $errors = [];

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