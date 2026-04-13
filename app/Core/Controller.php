<?php
/**
 * Contrôleur de base
 */
class Controller
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    protected function view($view, $data = [])
    {
        // Extraire les données pour les rendre disponibles dans la vue
        extract($data);
        
        // Données communes à toutes les vues
        $profile = $this->db->fetch("SELECT * FROM profile LIMIT 1");
        $socialLinks = $this->db->fetchAll("SELECT * FROM social_links WHERE is_active = 1 ORDER BY order_index");
        
        // Inclure la vue
        $viewFile = BASE_PATH . '/app/Views/' . $view . '.php';
        
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            die("Vue non trouvée: $view");
        }
    }

    protected function json($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function redirect($url)
    {
        header('Location: ' . SITE_URL . '/' . ltrim($url, '/'));
        exit;
    }
}
