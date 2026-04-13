<?php
/**
 * Contrôleur des projets
 */
class ProjectController extends Controller
{
    public function index()
    {
        $projects = $this->db->fetchAll("SELECT * FROM projects ORDER BY order_index");

        // Charger les grandes compétences par projet
        foreach ($projects as &$project) {
            $project['competence_blocks'] = $this->db->fetchAll(
                "SELECT cb.* FROM competence_blocks cb 
                 JOIN project_competence_blocks pcb ON cb.id = pcb.competence_block_id 
                 WHERE pcb.project_id = ? ORDER BY cb.order_index", 
                [$project['id']]
            );
        }
        unset($project);

        $this->view('projects/index', [
            'pageTitle' => 'Projets',
            'projects' => $projects
        ]);
    }

    public function show($slug = null)
    {
        if (!$slug) {
            $this->redirect('projects');
            return;
        }

        $project = $this->db->fetch("SELECT * FROM projects WHERE slug = ?", [$slug]);

        if (!$project) {
            $this->redirect('projects');
            return;
        }

        // Charger les grandes compétences du projet
        $project['competence_blocks'] = $this->db->fetchAll(
            "SELECT cb.* FROM competence_blocks cb 
             JOIN project_competence_blocks pcb ON cb.id = pcb.competence_block_id 
             WHERE pcb.project_id = ? ORDER BY cb.order_index", 
            [$project['id']]
        );

        // Charger les sous-compétences avec justification, regroupées par bloc
        $project['sub_competences'] = $this->db->fetchAll(
            "SELECT psc.justification, sc.name as sc_name, cb.id as block_id, cb.name as block_name, cb.color as block_color
             FROM project_sub_competences psc
             JOIN sub_competences sc ON psc.sub_competence_id = sc.id
             LEFT JOIN competence_blocks cb ON sc.competence_block_id = cb.id
             WHERE psc.project_id = ?
             ORDER BY cb.order_index, sc.order_index",
            [$project['id']]
        );

        $this->view('projects/show', [
            'pageTitle' => $project['title'],
            'project' => $project
        ]);
    }
}
