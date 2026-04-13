<?php
/**
 * Contrôleur API pour les requêtes AJAX
 */
class ApiController extends Controller
{
    public function projects()
    {
        $projects = $this->db->fetchAll("SELECT * FROM projects ORDER BY order_index");
        $this->json(['success' => true, 'data' => $projects]);
    }

    public function socials()
    {
        $socials = $this->db->fetchAll("SELECT * FROM social_links WHERE is_active = 1 ORDER BY order_index");
        $this->json(['success' => true, 'data' => $socials]);
    }
}
