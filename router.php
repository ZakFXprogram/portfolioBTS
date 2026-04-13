<?php
/**
 * Router pour le serveur PHP intégré
 * Utilisation: php -S localhost:8000 router.php
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Si c'est un fichier existant (CSS, JS, images), le servir directement
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    // Déterminer le type MIME
    $extension = pathinfo($uri, PATHINFO_EXTENSION);
    $mimeTypes = [
        'css' => 'text/css',
        'js' => 'application/javascript',
        'json' => 'application/json',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'svg' => 'image/svg+xml',
        'webp' => 'image/webp',
        'ico' => 'image/x-icon',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf' => 'font/ttf',
        'pdf' => 'application/pdf',
    ];
    
    if (isset($mimeTypes[$extension])) {
        header('Content-Type: ' . $mimeTypes[$extension]);
    }
    
    return false; // Laisser PHP servir le fichier statique
}

// Route spéciale pour /admin (sans .php)
if ($uri === '/admin' || strpos($uri, '/admin?') === 0 || $uri === '/admin/') {
    require_once __DIR__ . '/admin.php';
    return;
}

// Sinon, router vers index.php
$_GET['url'] = ltrim($uri, '/');
require_once __DIR__ . '/index.php';
