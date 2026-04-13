<?php
/**
 * Contrôleur du CV/Resume
 */
class ResumeController extends Controller
{
    public function index()
    {
        $profile = $this->db->fetch("SELECT * FROM profile LIMIT 1");
        $experiences = $this->db->fetchAll("SELECT * FROM experiences ORDER BY order_index");
        $skills = $this->db->fetchAll("SELECT * FROM skills ORDER BY category, order_index");

        // Grouper les compétences par catégorie
        $skillsByCategory = [];
        foreach ($skills as $skill) {
            $skillsByCategory[$skill['category']][] = $skill;
        }

        $this->view('resume', [
            'pageTitle' => 'CV',
            'profile' => $profile,
            'experiences' => $experiences,
            'skillsByCategory' => $skillsByCategory
        ]);
    }

    public function download()
    {
        $profile = $this->db->fetch("SELECT resume_file FROM profile LIMIT 1");
        
        $resumePath = BASE_PATH . '/uploads/cv/' . ($profile['resume_file'] ?? 'cv.pdf');
        
        if (file_exists($resumePath)) {
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="CV_' . date('Y') . '.pdf"');
            header('Content-Length: ' . filesize($resumePath));
            readfile($resumePath);
            exit;
        } else {
            // Si pas de CV, générer un simple texte
            header('Content-Type: text/plain');
            header('Content-Disposition: attachment; filename="cv.txt"');
            echo "CV non disponible pour le moment.\nContactez-moi à: contact@example.com";
            exit;
        }
    }
}
