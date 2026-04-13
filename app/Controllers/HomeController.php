<?php
/**
 * Contrôleur de la page d'accueil
 */
class HomeController extends Controller
{
    public function index()
    {
        $profile = $this->db->fetch("SELECT * FROM profile LIMIT 1");
        $featuredProjects = $this->db->fetchAll("SELECT * FROM projects WHERE featured = 1 ORDER BY order_index LIMIT 4");
        $clients = $this->db->fetchAll("SELECT * FROM clients WHERE is_active = 1 ORDER BY order_index");
        $skills = $this->db->fetchAll("SELECT * FROM skills ORDER BY order_index");

        // Charger les grandes compétences par projet
        foreach ($featuredProjects as &$project) {
            $project['competence_blocks'] = $this->db->fetchAll(
                "SELECT cb.* FROM competence_blocks cb 
                 JOIN project_competence_blocks pcb ON cb.id = pcb.competence_block_id 
                 WHERE pcb.project_id = ? ORDER BY cb.order_index", 
                [$project['id']]
            );
        }
        unset($project);

        $this->view('home', [
            'pageTitle' => 'Accueil',
            'profile' => $profile,
            'featuredProjects' => $featuredProjects,
            'clients' => $clients,
            'skills' => $skills
        ]);
    }
}
