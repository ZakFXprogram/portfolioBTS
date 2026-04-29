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

        $slug = trim((string)$slug);

        $project = $this->db->fetch(
            "SELECT * FROM projects WHERE slug = ? OR TRIM(slug) = ? LIMIT 1",
            [$slug, $slug]
        );

        if (!$project) {
            $this->redirect('projects');
            return;
        }

        // Charger les grandes compétences du projet avec justification
        $project['competence_blocks'] = $this->db->fetchAll(
            "SELECT cb.*, pcb.justification FROM competence_blocks cb 
             JOIN project_competence_blocks pcb ON cb.id = pcb.competence_block_id 
             WHERE pcb.project_id = ? ORDER BY cb.order_index", 
            [$project['id']]
        );

        // Charger toutes les sous-compétences appartenant aux blocs validés
        // (pour afficher le tableau complet : validées et non validées)
        $blockIds = array_map(function($b) { return (int)$b['id']; }, $project['competence_blocks']);
        $allSubs = [];
        if (!empty($blockIds)) {
            $placeholders = implode(',', array_fill(0, count($blockIds), '?'));
            $allSubs = $this->db->fetchAll(
                "SELECT sc.id, sc.name, sc.competence_block_id
                 FROM sub_competences sc
                 WHERE sc.competence_block_id IN ($placeholders)
                 ORDER BY sc.order_index, sc.id",
                $blockIds
            );
        }

        // Charger les sous-compétences cochées avec justification (Comment + Pourquoi)
        $validated = $this->db->fetchAll(
            "SELECT psc.sub_competence_id, psc.justification, psc.justification_pourquoi
             FROM project_sub_competences psc
             WHERE psc.project_id = ?",
            [$project['id']]
        );
        $validatedMap = [];
        foreach ($validated as $v) {
            $validatedMap[(int)$v['sub_competence_id']] = [
                'comment'  => $v['justification'] ?? '',
                'pourquoi' => $v['justification_pourquoi'] ?? ''
            ];
        }

        // Construire la matrice par bloc
        $project['all_sub_competences'] = $allSubs;
        $project['validated_sub_competences'] = $validatedMap;

        // Conserver pour compat éventuelle
        $project['sub_competences'] = $this->db->fetchAll(
            "SELECT sc.id as sc_id, sc.name as sc_name,
                    psc.justification as sc_justification,
                    psc.justification_pourquoi as sc_justification_pourquoi,
                    cb.id as block_id, cb.name as block_name, cb.color as block_color
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
