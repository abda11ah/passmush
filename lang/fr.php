<?php
return [
    'page_title' => 'Partage Sécurisé de Mot de Passe',
    'password_to_share' => 'Mot de passe à partager',
    'text_to_share' => 'Texte secret à partager',
    'share_password' => 'Partager un Mot de Passe',
    'share_text' => 'Partager un Texte',
    'expires_after' => 'Expire après',
    'view_limit' => 'Limite de visualisation',
    'generate_link' => 'Générer un Lien Sécurisé',
    'shared_password' => 'Mot de Passe Partagé',
    'password' => 'Mot de passe:',
    'copy_clipboard' => 'Copier dans le Presse-papiers',
    'copied' => 'Copié!',
    'expires' => 'Expire le:',
    'views_remaining' => 'Vues restantes:',
    'of' => 'sur',
    'share_another' => 'Partager un Autre Mot de Passe',
    'link_expired' => 'Ce lien a expiré ou n\'existe pas.',
    'max_views_reached' => 'Ce mot de passe a atteint sa limite maximale de vues.',
    'share_success' => 'Mot de Passe Partagé avec Succès!',
    'share_link' => 'Partagez ce lien sécurisé:',
    'generate' => 'Générer',
    'destroy_password' => 'Détruire le Mot de Passe',
    'confirm_destroy' => 'Êtes-vous sûr de vouloir détruire ce mot de passe ? Cette action est irréversible.',
    'password_destroyed' => 'Le mot de passe a été détruit avec succès.',
    'destroy_error' => 'Une erreur est survenue lors de la destruction du mot de passe.',
    'errors' => 'Erreurs Système',
    'go_to_install' => 'Aller à l\'Installation',
    'go_to_app' => 'Ouvrir l\'application',
    'installation' => 'Installation du Partage de Mot de Passe',
    'progress' => 'Progression de l\'installation :',
    'success' => 'Installation terminée avec succès !',
    'failure' => 'L\'installation a échoué. Veuillez corriger les erreurs et réessayer.',
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
    'tables_error' => 'Échec de la création des tables : %s',
    'date_format' => 'd-m-Y à H:i:s',
    'no_expiration' => 'Pas d\'expiration',
    'time_options' => [
        '1' => '1 heure',
        '2' => '2 heures',
        '6' => '6 heures',
        '24' => '24 heures',
        '72' => '3 jours',
        '168' => '1 semaine',
        '720' => '1 mois',
        '-1' => 'Illimité (non recommandé)'
    ],
    'view_options' => [
        '1' => '1 fois',
        '3' => '3 fois',
        '5' => '5 fois',
        '10' => '10 fois',
        '0' => 'Illimité'
    ],
    // Database configuration translations
    'test_connection' => 'Tester la Connexion',
    'db_configuration' => 'Configuration de la Base de Données',
    'db_host' => 'Hôte de la Base de Données',
    'db_user' => 'Nom d\'Utilisateur',
    'db_pass' => 'Mot de Passe',
    'db_name' => 'Nom de la Base de Données',
    'db_create_type' => 'Création de la Base de Données',
    'db_create_new' => 'Créer une nouvelle base de données',
    'db_use_existing' => 'Utiliser une base de données existante',
    'table_name' => 'Nom de la Table',
    'table_prefix' => 'Préfixe de Table',
    'optional' => 'Optionnel',
    'install' => 'Installer',
    'db_connection_success' => 'Connexion à la base de données réussie',
    'db_connection_error' => 'Échec de la connexion à la base de données : %s',
    'config_write_success' => 'Fichier de configuration écrit avec succès',
    'config_write_error' => 'Échec de l\'écriture du fichier de configuration',
    'config_not_writable' => 'Le fichier de configuration n\'est pas accessible en écriture. Veuillez définir les permissions appropriées pour config.inc.php',
        // Installation related translations
    'install_warning' => 'Le fichier install.php existe toujours. Ceci représente un risque de sécurité et devrait être supprimé.',
    'delete_install' => 'Supprimer install.php',
    'confirm_delete_install' => 'Êtes-vous sûr de vouloir supprimer le fichier d\'installation ? Cette action est irréversible.',
    'install_deleted' => 'Fichier d\'installation supprimé avec succès.',
    'install_delete_error' => 'Erreur lors de la suppression du fichier d\'installation.',
        // ... existing translations ...
    'existing_db_name' => 'Nom de la Base de Données Existante',
    'enter_existing_db' => 'Entrez le nom de votre base de données existante',
    'db_exists' => 'La base de données existe et est accessible',
    'db_not_exists' => 'La base de données n\'existe pas',
    'db_connected' => 'Connexion réussie à la base de données existante',
    'company_info' => 'Informations de l\'entreprise',
    'company_logo' => 'Logo de l\'entreprise',
    'logo_requirements' => 'Formats acceptés : JPG, PNG, GIF. Taille maximum : 5Mo',
    'logo_type_error' => 'Format de logo invalide. Veuillez utiliser JPG, PNG ou GIF',
    'logo_size_error' => 'Le fichier logo est trop volumineux. Taille maximum 5Mo',
    'logo_upload_error' => 'Erreur lors du téléchargement du logo',
    'logo_uploaded' => 'Logo de l\'entreprise téléchargé avec succès',
    'uploads_dir_created' => 'Répertoire des téléchargements créé avec succès',
    'uploads_dir_error' => 'Erreur lors de la création du répertoire des téléchargements',
        // Exception page translations
    'error_occurred' => 'Une Erreur est Survenue : ',
    'return_home' => 'Retour à l\'Accueil',
];