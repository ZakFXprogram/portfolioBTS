<?php
/**
 * Portfolio - Point d'entrée principal
 * Architecture MVC simple et efficace
 */

// Définir le chemin de base
define('BASE_PATH', __DIR__);

// Autoloader simple
spl_autoload_register(function ($class) {
    $paths = [
        BASE_PATH . '/app/Core/',
        BASE_PATH . '/app/Controllers/',
        BASE_PATH . '/app/Models/',
    ];
    
    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Charger la configuration
require_once BASE_PATH . '/config/config.php';

// Initialiser l'application
$app = new App();
$app->run();
