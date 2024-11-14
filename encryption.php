<?php
class Encryption {
    private $public_key;
    private $private_key;

    public function __construct() {
        $this->public_key = file_get_contents(__DIR__ . '/keys/public.key');
        $this->private_key = file_get_contents(__DIR__ . '/keys/private.key');
    }

    public function encrypt($data) {
        $encrypted = '';
        openssl_public_encrypt($data, $encrypted, $this->public_key);
        return base64_encode($encrypted);
    }

    public function decrypt($encrypted_data) {
        $decrypted = '';
        openssl_private_decrypt(base64_decode($encrypted_data), $decrypted, $this->private_key);
        return $decrypted;
    }
}