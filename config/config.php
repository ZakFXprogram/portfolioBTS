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
define('SITE_URL', 'http://localhost:8000');
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
