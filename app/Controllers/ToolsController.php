<?php
/**
 * Contrôleur des outils (Tools)
 */
class ToolsController extends Controller
{
    public function index()
    {
        $tools = $this->db->fetchAll("SELECT * FROM tools ORDER BY category, order_index");

        // Grouper par catégorie
        $toolsByCategory = [];
        foreach ($tools as $tool) {
            $toolsByCategory[$tool['category']][] = $tool;
        }

        $this->view('tools', [
            'pageTitle' => 'Outils',
            'toolsByCategory' => $toolsByCategory
        ]);
    }
}
