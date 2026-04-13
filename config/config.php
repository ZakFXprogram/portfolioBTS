<?php
/**
 * Configuration de l'application
 */

// Mode debug
define('DEBUG', true);

// Configuration de la base de données (SQLite pour simplicité)
define('DB_TYPE', 'sqlite');
define('DB_PATH', BASE_PATH . '/database/portfolio.db');

// Configuration du site
define('SITE_NAME', 'Mon Portfolio');

// Détecter SITE_URL dynamiquement (fonctionne en local et en production)
$_protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'
    || !empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https'
    || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443))
    ? 'https' : 'http';
$_host = $_SERVER['HTTP_HOST'] ?? 'localhost:8000';
define('SITE_URL', $_protocol . '://' . $_host);
unset($_protocol, $_host);

define('SITE_AUTHOR', 'Votre Nom');
define('SITE_DESCRIPTION', 'Senior Full-Stack Developer');

// Chemins
define('ASSETS_PATH', '/assets');
define('UPLOADS_PATH', '/uploads');

// Timezone
date_default_timezone_set('Europe/Paris');

// Gestion des erreurs
if (DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}
