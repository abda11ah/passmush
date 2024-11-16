<?php
return [
    'page_title' => 'Secure Password Share',
    'password_to_share' => 'Password to share',
    'text_to_share' => 'Secret text to share',
    'share_password' => 'Share Password',
    'share_text' => 'Share Text',
    'expires_after' => 'Expires after',
    'view_limit' => 'View/Copy limit',
    'generate_link' => 'Generate Secure Link',
    'shared_password' => 'Shared Password',
    'password' => 'Password:',
    'copy_clipboard' => 'Copy to Clipboard',
    'copied' => 'Copied!',
    'expires' => 'Expires:',
    'views_remaining' => 'Views remaining:',
    'of' => 'of',
    'share_another' => 'Share Another Password',
    'link_expired' => 'This link has expired or doesn\'t exist.',
    'max_views_reached' => 'This password has reached its maximum view limit.',
    'share_success' => 'Password Shared Successfully!',
    'share_link' => 'Share this secure link:',
    'generate' => 'Generate',
    'destroy_password' => 'Destroy Password',
    'confirm_destroy' => 'Are you sure you want to destroy this password? This action cannot be undone.',
    'password_destroyed' => 'Password has been successfully destroyed.',
    'destroy_error' => 'An error occurred while destroying the password.',
    'errors' => 'System Errors',
    'go_to_install' => 'Go to Installation',
    'go_to_app' => 'Go to app',
    'installation' => 'Password Share Installation',
    'progress' => 'Installation Progress:',
    'success' => 'Installation completed successfully!',
    'failure' => 'Installation failed. Please fix the errors and try again.',
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
    'tables_error' => 'Table creation failed: %s',
    'date_format' => 'Y-m-d @ H:i:s',
    'time_options' => [
        '1' => '1 hour',
        '2' => '2 hours',
        '6' => '6 hours',
        '24' => '24 hours',
        '72' => '3 days',
        '168' => '1 week',
        '720' => '1 month'
    ],
    'view_options' => [
        '1' => '1 time',
        '3' => '3 times',
        '5' => '5 times',
        '10' => '10 times',
        '0' => 'Unlimited'
    ],
    // Database configuration translations
    'test_connection' => 'Test Database Connection',
    'db_configuration' => 'Database Configuration',
    'db_host' => 'Database Host',
    'db_user' => 'Database Username',
    'db_pass' => 'Database Password',
    'db_name' => 'Database Name',
    'db_create_type' => 'Database Creation',
    'db_create_new' => 'Create new database',
    'db_use_existing' => 'Use existing database',
    'table_name' => 'Table Name',
    'table_prefix' => 'Table Prefix',
    'optional' => 'Optional',
    'install' => 'Install',
    'db_connection_success' => 'Database connection successful',
    'db_connection_error' => 'Database connection failed: %s',
    'config_write_success' => 'Configuration file written successfully',
    'config_write_error' => 'Failed to write configuration file',
    'config_not_writable' => 'Configuration file is not writable. Please set proper write permissions for config.inc.php',
        // Installation related translations
    'install_warning' => 'The install.php file still exists. This is a security risk and should be deleted.',
    'delete_install' => 'Delete install.php',
    'confirm_delete_install' => 'Are you sure you want to delete the installation file? This action cannot be undone.',
    'install_deleted' => 'Installation file successfully deleted.',
    'install_delete_error' => 'Error deleting installation file.',
        // ... existing translations ...
    'existing_db_name' => 'Existing Database Name',
    'enter_existing_db' => 'Enter the name of your existing database',
    'db_exists' => 'Database exists and is accessible',
    'db_not_exists' => 'Database does not exist',
    'db_connected' => 'Successfully connected to existing database',
    'company_info' => 'Company Information',
    'company_logo' => 'Company Logo',
    'logo_requirements' => 'Accepted formats: JPG, PNG, GIF. Maximum size: 5MB',
    'logo_type_error' => 'Invalid logo format. Please use JPG, PNG, or GIF',
    'logo_size_error' => 'Logo file is too large. Maximum size is 5MB',
    'logo_upload_error' => 'Error uploading logo',
    'logo_uploaded' => 'Company logo uploaded successfully',
    'uploads_dir_created' => 'Uploads directory created successfully',
    'uploads_dir_error' => 'Error creating uploads directory',
];