<?php
// Generate private key
$private_key = openssl_pkey_new([
    "private_key_bits" => 2048,
    "private_key_type" => OPENSSL_KEYTYPE_RSA
]);

// Extract private key to PEM format
openssl_pkey_export($private_key, $private_key_pem);
file_put_contents('private.key', $private_key_pem);

// Extract public key
$public_key_details = openssl_pkey_get_details($private_key);
file_put_contents('public.key', $public_key_details["key"]);

echo "Keys generated successfully!\n";