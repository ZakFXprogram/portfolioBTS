<?php
/**
 * Administration du Portfolio
 * Accès: http://localhost:8000/admin.php
 * cf discord pour les identifiants de connexion
 */

if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__);
}
require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/config/admin.php';
require_once BASE_PATH . '/app/Core/Database.php';

// Démarrer la session
session_start();

// Fonction de vérification d'authentification
function isAuthenticated() {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        return false;
    }
    // Vérifier l'expiration de la session
    if (isset($_SESSION['admin_login_time']) && (time() - $_SESSION['admin_login_time']) > SESSION_DURATION) {
        session_destroy();
        return false;
    }
    return true;
}

// Traitement de la connexion
if (isset($_POST['login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Vérification avec password_verify (bcrypt)
    if ($username === ADMIN_USERNAME && password_verify($password, ADMIN_PASSWORD_HASH)) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_login_time'] = time();
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        header('Location: admin.php');
        exit;
    } else {
        $login_error = "Identifiants incorrects !";
    }
}

// Traitement de la déconnexion
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin.php');
    exit;
}

// Si non authentifié, afficher le formulaire de connexion
if (!isAuthenticated()) {
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Connexion - Administration</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <style>
            * { box-sizing: border-box; margin: 0; padding: 0; }
            body {
                font-family: 'Segoe UI', sans-serif;
                background: #0f0f1a;
                color: #fff;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .login-container {
                background: #1a1a2e;
                padding: 40px;
                border-radius: 16px;
                border: 1px solid #3a3a5a;
                width: 100%;
                max-width: 400px;
                box-shadow: 0 10px 40px rgba(0,0,0,0.5);
            }
            h1 {
                color: #f97316;
                text-align: center;
                margin-bottom: 30px;
                font-size: 1.5rem;
            }
            .error {
                background: #ef444433;
                border: 1px solid #ef4444;
                padding: 12px;
                border-radius: 8px;
                margin-bottom: 20px;
                text-align: center;
            }
            .form-group { margin-bottom: 20px; }
            label {
                display: block;
                margin-bottom: 8px;
                color: #a0a0b0;
                font-weight: 600;
            }
            input {
                width: 100%;
                padding: 14px;
                border: 1px solid #3a3a5a;
                border-radius: 8px;
                background: #252542;
                color: #fff;
                font-size: 1rem;
            }
            input:focus {
                outline: none;
                border-color: #f97316;
            }
            button {
                width: 100%;
                padding: 14px;
                background: linear-gradient(135deg, #f97316, #fb923c);
                color: white;
                border: none;
                border-radius: 8px;
                font-size: 1rem;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s;
            }
            button:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 15px rgba(249,115,22,0.4);
            }
            .back-link {
                text-align: center;
                margin-top: 20px;
            }
            .back-link a {
                color: #6c6c7c;
                text-decoration: none;
            }
            .back-link a:hover { color: #f97316; }
        </style>
    </head>
    <body>
        <div class="login-container">
            <h1><i class="fas fa-lock"></i> Administration</h1>
            
            <?php if (isset($login_error)): ?>
            <div class="error"><?= htmlspecialchars($login_error) ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="username"><i class="fas fa-user"></i> Identifiant</label>
                    <input type="text" id="username" name="username" required autocomplete="username">
                </div>
                
                <div class="form-group">
                    <label for="password"><i class="fas fa-key"></i> Mot de passe</label>
                    <input type="password" id="password" name="password" required autocomplete="current-password">
                </div>
                
                <button type="submit" name="login"><i class="fas fa-sign-in-alt"></i> Se connecter</button>
            </form>
            
            <div class="back-link">
                <a href="/"><i class="fas fa-arrow-left"></i> Retour au site</a>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Vérification CSRF pour les formulaires POST
function checkCSRF() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['login'])) {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die('Erreur de sécurité CSRF. <a href="admin.php">Retour</a>');
        }
    }
}
checkCSRF();

$db = Database::getInstance();
$message = '';
$messageType = '';

// Traitement des formulaires
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    // Ajouter un projet
    if ($action === 'add_project') {
        $title = $_POST['title'] ?? '';
        $slug = $_POST['slug'] ?? strtolower(str_replace(' ', '-', $title));
        $description = $_POST['description'] ?? '';
        $long_description = $_POST['long_description'] ?? '';
        $image = $_POST['image'] ?? '';
        $gallery_images = $_POST['gallery_images'] ?? '';
        $url = $_POST['url'] ?? '';
        $github_url = $_POST['github_url'] ?? '';
        $technologies = $_POST['technologies'] ?? '';
        $featured = isset($_POST['featured']) ? 1 : 0;
        
        try {
            $db->query("INSERT INTO projects (title, slug, description, long_description, image, gallery_images, url, github_url, technologies, featured, order_index) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", 
                       [$title, $slug, $description, $long_description, $image, $gallery_images, $url, $github_url, $technologies, $featured, 0]);
            $message = "Projet '$title' ajouté avec succès !";
            $messageType = 'success';
        } catch (Exception $e) {
            $message = "Erreur: " . $e->getMessage();
            $messageType = 'error';
        }
    }
    
    // Supprimer un projet
    if ($action === 'delete_project') {
        $id = $_POST['project_id'] ?? 0;
        $db->query("DELETE FROM projects WHERE id = ?", [$id]);
        $message = "Projet supprimé !";
        $messageType = 'success';
    }
    
    // Modifier un projet
    if ($action === 'edit_project') {
        $id = $_POST['project_id'] ?? 0;
        $title = $_POST['title'] ?? '';
        $slug = $_POST['slug'] ?? strtolower(str_replace(' ', '-', $title));
        $description = $_POST['description'] ?? '';
        $long_description = $_POST['long_description'] ?? '';
        $image = $_POST['image'] ?? '';
        $gallery_images = $_POST['gallery_images'] ?? '';
        $url = $_POST['url'] ?? '';
        $github_url = $_POST['github_url'] ?? '';
        $technologies = $_POST['technologies'] ?? '';
        $featured = isset($_POST['featured']) ? 1 : 0;
        
        try {
            $db->query("UPDATE projects SET title = ?, slug = ?, description = ?, long_description = ?, 
                        image = ?, gallery_images = ?, url = ?, github_url = ?, technologies = ?, featured = ?, updated_at = CURRENT_TIMESTAMP 
                        WHERE id = ?", 
                       [$title, $slug, $description, $long_description, $image, $gallery_images, $url, $github_url, $technologies, $featured, $id]);
            $message = "Projet '$title' modifié avec succès !";
            $messageType = 'success';
        } catch (Exception $e) {
            $message = "Erreur: " . $e->getMessage();
            $messageType = 'error';
        }
    }
    
    // Modifier le profil
    if ($action === 'update_profile') {
        $full_name = $_POST['full_name'] ?? '';
        $title = $_POST['title'] ?? '';
        $bio = $_POST['bio'] ?? '';
        $email = $_POST['email'] ?? '';
        $location = $_POST['location'] ?? '';
        
        $db->query("UPDATE profile SET full_name = ?, title = ?, bio = ?, email = ?, location = ? WHERE id = 1", 
                   [$full_name, $title, $bio, $email, $location]);
        $message = "Profil mis à jour !";
        $messageType = 'success';
    }
    
    // Ajouter un lien social
    if ($action === 'add_social') {
        $name = $_POST['name'] ?? '';
        $url = $_POST['url'] ?? '';
        $icon = $_POST['icon'] ?? '';
        
        $db->query("INSERT INTO social_links (name, url, icon, order_index) VALUES (?, ?, ?, ?)", 
                   [$name, $url, $icon, 0]);
        $message = "Lien social ajouté !";
        $messageType = 'success';
    }
    
    // Supprimer un lien social
    if ($action === 'delete_social') {
        $id = $_POST['social_id'] ?? 0;
        $db->query("DELETE FROM social_links WHERE id = ?", [$id]);
        $message = "Lien social supprimé !";
        $messageType = 'success';
    }

    // Ajouter une expérience
    if ($action === 'add_experience') {
        $company = $_POST['company'] ?? '';
        $position = $_POST['position'] ?? '';
        $type = $_POST['type'] ?? 'Full-time';
        $description = $_POST['description'] ?? '';
        $responsibilities = $_POST['responsibilities'] ?? '';
        $start_date = $_POST['start_date'] ?? '';
        $end_date = $_POST['end_date'] ?? null;
        $is_current = isset($_POST['is_current']) ? 1 : 0;
        $order_index = (int)($_POST['order_index'] ?? 0);

        try {
            $db->query("INSERT INTO experiences (company, position, type, description, responsibilities, start_date, end_date, is_current, order_index) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)", 
                       [$company, $position, $type, $description, $responsibilities, $start_date, $is_current ? null : $end_date, $is_current, $order_index]);
            $message = "Expérience '$position à $company' ajoutée avec succès !";
            $messageType = 'success';
        } catch (Exception $e) {
            $message = "Erreur: " . $e->getMessage();
            $messageType = 'error';
        }
    }

    // Modifier une expérience
    if ($action === 'edit_experience') {
        $id = $_POST['experience_id'] ?? 0;
        $company = $_POST['company'] ?? '';
        $position = $_POST['position'] ?? '';
        $type = $_POST['type'] ?? 'Full-time';
        $description = $_POST['description'] ?? '';
        $responsibilities = $_POST['responsibilities'] ?? '';
        $start_date = $_POST['start_date'] ?? '';
        $end_date = $_POST['end_date'] ?? null;
        $is_current = isset($_POST['is_current']) ? 1 : 0;
        $order_index = (int)($_POST['order_index'] ?? 0);

        try {
            $db->query("UPDATE experiences SET company = ?, position = ?, type = ?, description = ?, responsibilities = ?, start_date = ?, end_date = ?, is_current = ?, order_index = ? WHERE id = ?", 
                       [$company, $position, $type, $description, $responsibilities, $start_date, $is_current ? null : $end_date, $is_current, $order_index, $id]);
            $message = "Expérience modifiée avec succès !";
            $messageType = 'success';
        } catch (Exception $e) {
            $message = "Erreur: " . $e->getMessage();
            $messageType = 'error';
        }
    }

    // Supprimer une expérience
    if ($action === 'delete_experience') {
        $id = $_POST['experience_id'] ?? 0;
        $db->query("DELETE FROM experiences WHERE id = ?", [$id]);
        $message = "Expérience supprimée !";
        $messageType = 'success';
    }

    // Ajouter un skill
    if ($action === 'add_skill') {
        $name = $_POST['name'] ?? '';
        $category = $_POST['category'] ?? '';
        $icon = $_POST['icon'] ?? '';
        $level = (int)($_POST['level'] ?? 0);
        $order_index = (int)($_POST['order_index'] ?? 0);

        try {
            $db->query("INSERT INTO skills (name, category, icon, level, order_index) VALUES (?, ?, ?, ?, ?)", 
                       [$name, $category, $icon, $level, $order_index]);
            $message = "Skill '$name' ajouté avec succès !";
            $messageType = 'success';
        } catch (Exception $e) {
            $message = "Erreur: " . $e->getMessage();
            $messageType = 'error';
        }
    }

    // Modifier un skill
    if ($action === 'edit_skill') {
        $id = $_POST['skill_id'] ?? 0;
        $name = $_POST['name'] ?? '';
        $category = $_POST['category'] ?? '';
        $icon = $_POST['icon'] ?? '';
        $level = (int)($_POST['level'] ?? 0);
        $order_index = (int)($_POST['order_index'] ?? 0);

        try {
            $db->query("UPDATE skills SET name = ?, category = ?, icon = ?, level = ?, order_index = ? WHERE id = ?", 
                       [$name, $category, $icon, $level, $order_index, $id]);
            $message = "Skill modifié avec succès !";
            $messageType = 'success';
        } catch (Exception $e) {
            $message = "Erreur: " . $e->getMessage();
            $messageType = 'error';
        }
    }

    // Supprimer un skill
    if ($action === 'delete_skill') {
        $id = $_POST['skill_id'] ?? 0;
        $db->query("DELETE FROM skills WHERE id = ?", [$id]);
        $message = "Skill supprimé !";
        $messageType = 'success';
    }

    // Ajouter un tool
    if ($action === 'add_tool') {
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        $icon = $_POST['icon'] ?? '';
        $url = $_POST['url'] ?? '';
        $category = $_POST['category'] ?? '';
        $order_index = (int)($_POST['order_index'] ?? 0);

        try {
            $db->query("INSERT INTO tools (name, description, icon, url, category, order_index) VALUES (?, ?, ?, ?, ?, ?)", 
                       [$name, $description, $icon, $url, $category, $order_index]);
            $message = "Tool '$name' ajouté avec succès !";
            $messageType = 'success';
        } catch (Exception $e) {
            $message = "Erreur: " . $e->getMessage();
            $messageType = 'error';
        }
    }

    // Modifier un tool
    if ($action === 'edit_tool') {
        $id = $_POST['tool_id'] ?? 0;
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        $icon = $_POST['icon'] ?? '';
        $url = $_POST['url'] ?? '';
        $category = $_POST['category'] ?? '';
        $order_index = (int)($_POST['order_index'] ?? 0);

        try {
            $db->query("UPDATE tools SET name = ?, description = ?, icon = ?, url = ?, category = ?, order_index = ? WHERE id = ?", 
                       [$name, $description, $icon, $url, $category, $order_index, $id]);
            $message = "Tool modifié avec succès !";
            $messageType = 'success';
        } catch (Exception $e) {
            $message = "Erreur: " . $e->getMessage();
            $messageType = 'error';
        }
    }

    // Supprimer un tool
    if ($action === 'delete_tool') {
        $id = $_POST['tool_id'] ?? 0;
        $db->query("DELETE FROM tools WHERE id = ?", [$id]);
        $message = "Tool supprimé !";
        $messageType = 'success';
    }

    // ====== GRANDES COMPÉTENCES (BLOCS) ======
    if ($action === 'add_competence_block') {
        $name = $_POST['name'] ?? '';
        $icon = $_POST['icon'] ?? '';
        $color = $_POST['color'] ?? '#f97316';
        $order_index = (int)($_POST['order_index'] ?? 0);
        try {
            $db->query("INSERT INTO competence_blocks (name, icon, color, order_index) VALUES (?, ?, ?, ?)", [$name, $icon, $color, $order_index]);
            $message = "Bloc de compétence '$name' ajouté !";
            $messageType = 'success';
        } catch (Exception $e) {
            $message = "Erreur: " . $e->getMessage();
            $messageType = 'error';
        }
    }

    if ($action === 'edit_competence_block') {
        $id = $_POST['block_id'] ?? 0;
        $name = $_POST['name'] ?? '';
        $icon = $_POST['icon'] ?? '';
        $color = $_POST['color'] ?? '#f97316';
        $order_index = (int)($_POST['order_index'] ?? 0);
        try {
            $db->query("UPDATE competence_blocks SET name = ?, icon = ?, color = ?, order_index = ? WHERE id = ?", [$name, $icon, $color, $order_index, $id]);
            $message = "Bloc de compétence modifié !";
            $messageType = 'success';
        } catch (Exception $e) {
            $message = "Erreur: " . $e->getMessage();
            $messageType = 'error';
        }
    }

    if ($action === 'delete_competence_block') {
        $id = $_POST['block_id'] ?? 0;
        $db->query("DELETE FROM competence_blocks WHERE id = ?", [$id]);
        $message = "Bloc de compétence supprimé !";
        $messageType = 'success';
    }

    // ====== SOUS-COMPÉTENCES ======
    if ($action === 'add_sub_competence') {
        $name = $_POST['name'] ?? '';
        $competence_block_id = (int)($_POST['competence_block_id'] ?? 0);
        $order_index = (int)($_POST['order_index'] ?? 0);
        try {
            $db->query("INSERT INTO sub_competences (name, competence_block_id, order_index) VALUES (?, ?, ?)", [$name, $competence_block_id, $order_index]);
            $message = "Sous-compétence '$name' ajoutée !";
            $messageType = 'success';
        } catch (Exception $e) {
            $message = "Erreur: " . $e->getMessage();
            $messageType = 'error';
        }
    }

    if ($action === 'edit_sub_competence') {
        $id = $_POST['sub_competence_id'] ?? 0;
        $name = $_POST['name'] ?? '';
        $competence_block_id = (int)($_POST['competence_block_id'] ?? 0);
        $order_index = (int)($_POST['order_index'] ?? 0);
        try {
            $db->query("UPDATE sub_competences SET name = ?, competence_block_id = ?, order_index = ? WHERE id = ?", [$name, $competence_block_id, $order_index, $id]);
            $message = "Sous-compétence modifiée !";
            $messageType = 'success';
        } catch (Exception $e) {
            $message = "Erreur: " . $e->getMessage();
            $messageType = 'error';
        }
    }

    if ($action === 'delete_sub_competence') {
        $id = $_POST['sub_competence_id'] ?? 0;
        $db->query("DELETE FROM sub_competences WHERE id = ?", [$id]);
        $message = "Sous-compétence supprimée !";
        $messageType = 'success';
    }

    // ====== COMPÉTENCES PROJET ======
    if ($action === 'save_project_competence_blocks') {
        $project_id = (int)($_POST['project_id'] ?? 0);
        $block_ids = $_POST['competence_block_ids'] ?? [];
        $db->query("DELETE FROM project_competence_blocks WHERE project_id = ?", [$project_id]);
        foreach ($block_ids as $block_id) {
            $db->query("INSERT INTO project_competence_blocks (project_id, competence_block_id) VALUES (?, ?)", [$project_id, (int)$block_id]);
        }
        $message = "Grandes compétences du projet mises à jour !";
        $messageType = 'success';
    }

    if ($action === 'add_project_sub_competence') {
        $project_id = (int)($_POST['project_id'] ?? 0);
        $sub_competence_id = (int)($_POST['sub_competence_id'] ?? 0);
        $justification = trim($_POST['justification'] ?? '');
        if (empty($justification)) {
            $message = "La justification est obligatoire !";
            $messageType = 'error';
        } else {
            try {
                $db->query("INSERT INTO project_sub_competences (project_id, sub_competence_id, justification) VALUES (?, ?, ?)", [$project_id, $sub_competence_id, $justification]);
                $message = "Sous-compétence ajoutée au projet !";
                $messageType = 'success';
            } catch (Exception $e) {
                $message = "Erreur: " . $e->getMessage();
                $messageType = 'error';
            }
        }
    }

    if ($action === 'edit_project_sub_competence') {
        $id = (int)($_POST['psc_id'] ?? 0);
        $justification = trim($_POST['justification'] ?? '');
        if (empty($justification)) {
            $message = "La justification est obligatoire !";
            $messageType = 'error';
        } else {
            $db->query("UPDATE project_sub_competences SET justification = ? WHERE id = ?", [$justification, $id]);
            $message = "Justification modifiée !";
            $messageType = 'success';
        }
    }

    if ($action === 'delete_project_sub_competence') {
        $id = (int)($_POST['psc_id'] ?? 0);
        $db->query("DELETE FROM project_sub_competences WHERE id = ?", [$id]);
        $message = "Sous-compétence retirée du projet !";
        $messageType = 'success';
    }
}

// Récupérer les données
$profile = $db->fetch("SELECT * FROM profile LIMIT 1");
$projects = $db->fetchAll("SELECT * FROM projects ORDER BY order_index, id DESC");
$socials = $db->fetchAll("SELECT * FROM social_links ORDER BY order_index");
$experiences = $db->fetchAll("SELECT * FROM experiences ORDER BY order_index");
$skills = $db->fetchAll("SELECT * FROM skills ORDER BY category, order_index");
$tools = $db->fetchAll("SELECT * FROM tools ORDER BY category, order_index");
$competenceBlocks = $db->fetchAll("SELECT * FROM competence_blocks ORDER BY order_index, id");
$subCompetences = $db->fetchAll("SELECT sc.*, cb.name as block_name FROM sub_competences sc LEFT JOIN competence_blocks cb ON sc.competence_block_id = cb.id ORDER BY cb.order_index, sc.order_index");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - Portfolio</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #1a1a2e;
            color: #fff;
            line-height: 1.6;
            padding: 20px;
        }
        .container { max-width: 1200px; margin: 0 auto; }
        h1 { color: #f97316; margin-bottom: 30px; }
        h2 { color: #f97316; margin: 30px 0 15px; border-bottom: 2px solid #f97316; padding-bottom: 10px; }
        h3 { margin: 20px 0 10px; color: #fb923c; }
        
        .message {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .message.success { background: #22c55e33; border: 1px solid #22c55e; }
        .message.error { background: #ef444433; border: 1px solid #ef4444; }
        
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 30px; }
        
        .card {
            background: #252542;
            border-radius: 12px;
            padding: 20px;
            border: 1px solid #3a3a5a;
        }
        
        form { display: flex; flex-direction: column; gap: 15px; }
        
        label {
            font-weight: 600;
            color: #a0a0b0;
            font-size: 0.9rem;
        }
        
        input, textarea, select {
            width: 100%;
            padding: 12px;
            border: 1px solid #3a3a5a;
            border-radius: 8px;
            background: #1a1a2e;
            color: #fff;
            font-size: 1rem;
        }
        
        input:focus, textarea:focus {
            outline: none;
            border-color: #f97316;
        }
        
        textarea { min-height: 100px; resize: vertical; }
        
        button {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #f97316, #fb923c);
            color: white;
        }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(249,115,22,0.4); }
        
        .btn-danger {
            background: #ef4444;
            color: white;
            padding: 8px 16px;
            font-size: 0.85rem;
        }
        .btn-danger:hover { background: #dc2626; }
        
        .btn-edit {
            background: #3b82f6;
            color: white;
            padding: 8px 16px;
            font-size: 0.85rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }
        .btn-edit:hover { background: #2563eb; }
        
        .btn-secondary {
            background: #6b7280;
            color: white;
        }
        .btn-secondary:hover { background: #4b5563; }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .checkbox-group input { width: auto; }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #3a3a5a;
        }
        th { color: #f97316; }
        
        .project-item, .social-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            background: #1a1a2e;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        
        .project-info h4 { margin-bottom: 5px; }
        .project-info p { color: #6c6c7c; font-size: 0.9rem; }
        
        .icon-preview {
            font-size: 1.5rem;
            margin-right: 10px;
        }
        
        .help-text {
            font-size: 0.8rem;
            color: #6c6c7c;
            margin-top: 5px;
        }
        
        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        .tab {
            padding: 10px 20px;
            background: #252542;
            border-radius: 8px;
            cursor: pointer;
            border: 1px solid #3a3a5a;
        }
        .tab.active, .tab:hover {
            background: #f97316;
            border-color: #f97316;
        }
        
        .section { display: none; }
        .section.active { display: block; }

        a { color: #f97316; }
        
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.8);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .modal.active { display: flex; }
        .modal-content {
            background: #252542;
            border-radius: 16px;
            padding: 30px;
            width: 100%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            border: 1px solid #3a3a5a;
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .modal-header h3 { color: #f97316; margin: 0; }
        .modal-close {
            background: none;
            border: none;
            color: #6c6c7c;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0;
        }
        .modal-close:hover { color: #ef4444; }
        .modal-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-cog"></i> Administration du Portfolio</h1>
        <p style="margin-bottom: 20px;">
            <a href="/">← Retour au site</a> | 
            <a href="admin.php?logout=1" style="color: #ef4444;"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
        </p>
        
        <?php if ($message): ?>
        <div class="message <?= $messageType ?>"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        
        <div class="tabs">
            <div class="tab active" onclick="showSection('profile')"><i class="fas fa-user"></i> Profil</div>
            <div class="tab" onclick="showSection('experiences')"><i class="fas fa-briefcase"></i> Expériences</div>
            <div class="tab" onclick="showSection('skills')"><i class="fas fa-code"></i> Compétences</div>
            <div class="tab" onclick="showSection('projects')"><i class="fas fa-folder"></i> Projets</div>
            <div class="tab" onclick="showSection('tools')"><i class="fas fa-tools"></i> Tools</div>
            <div class="tab" onclick="showSection('competences')"><i class="fas fa-graduation-cap"></i> Compétences BTS</div>
            <div class="tab" onclick="showSection('socials')"><i class="fas fa-share-alt"></i> Réseaux Sociaux</div>
        </div>
        
        <!-- Section Profil -->
        <div id="profile" class="section active">
            <h2><i class="fas fa-user"></i> Mon Profil</h2>
            <div class="card">
                <form method="POST">
                    <input type="hidden" name="action" value="update_profile">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    
                    <label>Nom complet</label>
                    <input type="text" name="full_name" value="<?= htmlspecialchars($profile['full_name'] ?? '') ?>" required>
                    
                    <label>Titre / Poste</label>
                    <input type="text" name="title" value="<?= htmlspecialchars($profile['title'] ?? '') ?>" placeholder="Ex: Senior Full-Stack Developer">
                    
                    <label>Bio / Description</label>
                    <textarea name="bio" rows="5"><?= htmlspecialchars($profile['bio'] ?? '') ?></textarea>
                    
                    <label>Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($profile['email'] ?? '') ?>">
                    
                    <label>Localisation</label>
                    <input type="text" name="location" value="<?= htmlspecialchars($profile['location'] ?? '') ?>" placeholder="Ex: Paris, France">
                    
                    <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Enregistrer</button>
                </form>
            </div>
        </div>
        
        <!-- Section Expériences -->
        <div id="experiences" class="section">
            <h2><i class="fas fa-briefcase"></i> Expériences Professionnelles</h2>
            
            <div class="grid">
                <div class="card">
                    <h3><i class="fas fa-plus"></i> Ajouter une expérience</h3>
                    <form method="POST">
                        <input type="hidden" name="action" value="add_experience">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        
                        <label>Entreprise *</label>
                        <input type="text" name="company" required placeholder="Ex: Google, Microsoft...">
                        
                        <label>Poste *</label>
                        <input type="text" name="position" required placeholder="Ex: Développeur Full-Stack">
                        
                        <label>Type de contrat</label>
                        <select name="type">
                            <option value="Full-time">CDI (Full-time)</option>
                            <option value="Part-time">Temps partiel</option>
                            <option value="Contract">CDD/Contrat</option>
                            <option value="Freelance">Freelance</option>
                            <option value="Internship">Stage</option>
                            <option value="Apprenticeship">Alternance</option>
                        </select>
                        
                        <label>Description</label>
                        <textarea name="description" rows="2" placeholder="Description générale du poste"></textarea>
                        
                        <label>Responsabilités (une par ligne)</label>
                        <textarea name="responsibilities" rows="4" placeholder="Développement d'applications web&#10;Gestion de bases de données&#10;Collaboration avec l'équipe design"></textarea>
                        
                        <label>Date de début *</label>
                        <input type="date" name="start_date" required>
                        
                        <div class="checkbox-group">
                            <input type="checkbox" name="is_current" id="is_current" onchange="toggleEndDate()">
                            <label for="is_current">Poste actuel</label>
                        </div>
                        
                        <label>Date de fin</label>
                        <input type="date" name="end_date" id="end_date">
                        
                        <label>Ordre d'affichage</label>
                        <input type="number" name="order_index" value="0" min="0">
                        <p class="help-text">Plus le nombre est petit, plus l'expérience apparaît en premier</p>
                        
                        <button type="submit" class="btn-primary"><i class="fas fa-plus"></i> Ajouter</button>
                    </form>
                </div>
                
                <div class="card">
                    <h3><i class="fas fa-list"></i> Expériences existantes (<?= count($experiences) ?>)</h3>
                    
                    <?php if (empty($experiences)): ?>
                    <p style="color: #6c6c7c;">Aucune expérience pour le moment.</p>
                    <?php else: ?>
                    <?php foreach ($experiences as $exp): ?>
                    <div class="project-item">
                        <div class="project-info">
                            <h4>
                                <?= htmlspecialchars($exp['position']) ?>
                                <?php if ($exp['is_current']): ?>
                                <span style="color: #22c55e;">● Actuel</span>
                                <?php endif; ?>
                            </h4>
                            <p><?= htmlspecialchars($exp['company']) ?> - <?= htmlspecialchars($exp['type']) ?></p>
                            <small style="color: #6c6c7c;">
                                <?= date('M Y', strtotime($exp['start_date'])) ?> - 
                                <?= $exp['is_current'] ? 'Présent' : date('M Y', strtotime($exp['end_date'])) ?>
                            </small>
                        </div>
                        <div style="display: flex; gap: 8px;">
                            <button type="button" class="btn-edit" onclick="editExperience(<?= htmlspecialchars(json_encode($exp)) ?>)">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form method="POST" style="display: inline;" onsubmit="return confirm('Supprimer cette expérience ?')">
                                <input type="hidden" name="action" value="delete_experience">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                <input type="hidden" name="experience_id" value="<?= $exp['id'] ?>">
                                <button type="submit" class="btn-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Section Compétences -->
        <div id="skills" class="section">
            <h2><i class="fas fa-code"></i> Compétences</h2>
            
            <div class="grid">
                <div class="card">
                    <h3><i class="fas fa-plus"></i> Ajouter une compétence</h3>
                    <form method="POST">
                        <input type="hidden" name="action" value="add_skill">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        
                        <label>Nom de la compétence *</label>
                        <input type="text" name="name" required placeholder="Ex: JavaScript, PHP, Docker...">
                        
                        <label>Catégorie</label>
                        <input type="text" name="category" placeholder="Ex: Frontend, Backend, DevOps, Design...">
                        <p class="help-text">Les compétences sont regroupées par catégorie dans la page Resume</p>
                        
                        <label>Icône Font Awesome</label>
                        <input type="text" name="icon" placeholder="fab fa-js, fab fa-php, fas fa-database...">
                        <p class="help-text">
                            <a href="https://fontawesome.com/icons" target="_blank">Voir toutes les icônes →</a>
                        </p>
                        
                        <label>Niveau (0-100)</label>
                        <input type="range" name="level" min="0" max="100" value="50" oninput="this.nextElementSibling.textContent = this.value + '%'">
                        <span>50%</span>
                        <p class="help-text">Niveau de maîtrise affiché dans la barre de progression sur Home</p>
                        
                        <label>Ordre d'affichage</label>
                        <input type="number" name="order_index" value="0" min="0">
                        
                        <button type="submit" class="btn-primary"><i class="fas fa-plus"></i> Ajouter</button>
                    </form>
                </div>
                
                <div class="card">
                    <h3><i class="fas fa-list"></i> Compétences existantes (<?= count($skills) ?>)</h3>
                    
                    <?php if (empty($skills)): ?>
                    <p style="color: #6c6c7c;">Aucune compétence pour le moment.</p>
                    <?php else: ?>
                    <?php 
                    $currentCategory = null;
                    foreach ($skills as $skill): 
                        if ($skill['category'] !== $currentCategory):
                            $currentCategory = $skill['category'];
                    ?>
                    <h4 style="margin-top: 15px; color: #fb923c; border-bottom: 1px solid #3a3a5a; padding-bottom: 5px;">
                        <?= htmlspecialchars($currentCategory ?: 'Sans catégorie') ?>
                    </h4>
                    <?php endif; ?>
                    <div class="social-item" style="margin-bottom: 8px;">
                        <div style="display: flex; align-items: center;">
                            <i class="<?= htmlspecialchars($skill['icon']) ?> icon-preview"></i>
                            <div>
                                <strong><?= htmlspecialchars($skill['name']) ?></strong>
                                <div style="background: #1a1a2e; border-radius: 4px; height: 6px; width: 100px; margin-top: 4px;">
                                    <div style="background: linear-gradient(135deg, #f97316, #fb923c); height: 100%; border-radius: 4px; width: <?= (int)$skill['level'] ?>%;"></div>
                                </div>
                            </div>
                        </div>
                        <div style="display: flex; gap: 8px;">
                            <button type="button" class="btn-edit" onclick="editSkill(<?= htmlspecialchars(json_encode($skill)) ?>)">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form method="POST" style="display: inline;" onsubmit="return confirm('Supprimer cette compétence ?')">
                                <input type="hidden" name="action" value="delete_skill">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                <input type="hidden" name="skill_id" value="<?= $skill['id'] ?>">
                                <button type="submit" class="btn-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Section Projets -->
        <div id="projects" class="section">
            <h2><i class="fas fa-folder"></i> Mes Projets</h2>
            
            <div class="grid">
                <div class="card">
                    <h3><i class="fas fa-plus"></i> Ajouter un projet</h3>
                    <form method="POST">
                        <input type="hidden" name="action" value="add_project">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        
                        <label>Titre du projet *</label>
                        <input type="text" name="title" required placeholder="Ex: Mon Super Projet">
                        
                        <label>Slug (URL)</label>
                        <input type="text" name="slug" placeholder="mon-super-projet (auto-généré si vide)">
                        
                        <label>Description courte *</label>
                        <textarea name="description" rows="2" required placeholder="Description affichée dans les cards"></textarea>
                        
                        <label>Description longue</label>
                        <textarea name="long_description" rows="4" placeholder="Description détaillée pour la page du projet"></textarea>
                        
                        <label>Image principale</label>
                        <input type="text" name="image" placeholder="nom-image.jpg">
                        <p class="help-text">Placez vos images dans: assets/images/projects/</p>
                        
                        <label>Images galerie (séparées par des virgules)</label>
                        <input type="text" name="gallery_images" placeholder="img1.jpg, img2.jpg, img3.jpg">
                        <p class="help-text">Ces images s'afficheront dans la modal du projet</p>
                        
                        <label>URL du site</label>
                        <input type="url" name="url" placeholder="https://monprojet.com">
                        
                        <label>URL GitHub</label>
                        <input type="url" name="github_url" placeholder="https://github.com/user/repo">
                        
                        <label>Technologies (séparées par des virgules)</label>
                        <input type="text" name="technologies" placeholder="PHP, Laravel, Vue.js, MySQL">
                        
                        <div class="checkbox-group">
                            <input type="checkbox" name="featured" id="featured">
                            <label for="featured">Mettre en avant sur la page d'accueil</label>
                        </div>
                        
                        <button type="submit" class="btn-primary"><i class="fas fa-plus"></i> Ajouter</button>
                    </form>
                </div>
                
                <div class="card">
                    <h3><i class="fas fa-list"></i> Projets existants (<?= count($projects) ?>)</h3>
                    
                    <?php if (empty($projects)): ?>
                    <p style="color: #6c6c7c;">Aucun projet pour le moment.</p>
                    <?php else: ?>
                    <?php foreach ($projects as $project): ?>
                    <div class="project-item">
                        <div class="project-info">
                            <h4>
                                <?= htmlspecialchars($project['title']) ?>
                                <?php if ($project['featured']): ?>
                                <span style="color: #f97316;">⭐</span>
                                <?php endif; ?>
                            </h4>
                            <p><?= htmlspecialchars(substr($project['description'], 0, 60)) ?>...</p>
                        </div>
                        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                            <button type="button" class="btn-edit" onclick="editProject(<?= htmlspecialchars(json_encode($project)) ?>)">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="btn-edit" style="background:#8b5cf6;" onclick="openProjectCompetences(<?= $project['id'] ?>, <?= htmlspecialchars(json_encode($project['title'])) ?>)">
                                <i class="fas fa-graduation-cap"></i>
                            </button>
                            <form method="POST" style="display: inline;" onsubmit="return confirm('Supprimer ce projet ?')">
                                <input type="hidden" name="action" value="delete_project">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                <input type="hidden" name="project_id" value="<?= $project['id'] ?>">
                                <button type="submit" class="btn-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Section Tools -->
        <div id="tools" class="section">
            <h2><i class="fas fa-tools"></i> Tools (Outils)</h2>
            
            <div class="grid">
                <div class="card">
                    <h3><i class="fas fa-plus"></i> Ajouter un tool</h3>
                    <form method="POST">
                        <input type="hidden" name="action" value="add_tool">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        
                        <label>Nom de l'outil *</label>
                        <input type="text" name="name" required placeholder="Ex: VS Code, Docker, Figma...">
                        
                        <label>Description</label>
                        <textarea name="description" rows="2" placeholder="Description de l'outil"></textarea>
                        
                        <label>Icône Font Awesome</label>
                        <input type="text" name="icon" placeholder="fab fa-docker, fas fa-code...">
                        <p class="help-text">
                            <a href="https://fontawesome.com/icons" target="_blank">Voir toutes les icônes →</a>
                        </p>
                        
                        <label>URL (optionnel)</label>
                        <input type="url" name="url" placeholder="https://code.visualstudio.com">
                        
                        <label>Catégorie</label>
                        <input type="text" name="category" placeholder="Ex: IDE, DevOps, Design, Database...">
                        <p class="help-text">Les tools sont regroupés par catégorie dans la page Tools</p>
                        
                        <label>Ordre d'affichage</label>
                        <input type="number" name="order_index" value="0" min="0">
                        
                        <button type="submit" class="btn-primary"><i class="fas fa-plus"></i> Ajouter</button>
                    </form>
                </div>
                
                <div class="card">
                    <h3><i class="fas fa-list"></i> Tools existants (<?= count($tools) ?>)</h3>
                    
                    <?php if (empty($tools)): ?>
                    <p style="color: #6c6c7c;">Aucun tool pour le moment.</p>
                    <?php else: ?>
                    <?php 
                    $currentToolCategory = null;
                    foreach ($tools as $tool): 
                        if ($tool['category'] !== $currentToolCategory):
                            $currentToolCategory = $tool['category'];
                    ?>
                    <h4 style="margin-top: 15px; color: #fb923c; border-bottom: 1px solid #3a3a5a; padding-bottom: 5px;">
                        <?= htmlspecialchars($currentToolCategory ?: 'Sans catégorie') ?>
                    </h4>
                    <?php endif; ?>
                    <div class="social-item" style="margin-bottom: 8px;">
                        <div style="display: flex; align-items: center;">
                            <i class="<?= htmlspecialchars($tool['icon']) ?> icon-preview"></i>
                            <div>
                                <strong><?= htmlspecialchars($tool['name']) ?></strong>
                                <br><small style="color: #6c6c7c;"><?= htmlspecialchars(substr($tool['description'], 0, 50)) ?><?= strlen($tool['description']) > 50 ? '...' : '' ?></small>
                            </div>
                        </div>
                        <div style="display: flex; gap: 8px;">
                            <button type="button" class="btn-edit" onclick="editTool(<?= htmlspecialchars(json_encode($tool)) ?>)">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form method="POST" style="display: inline;" onsubmit="return confirm('Supprimer ce tool ?')">
                                <input type="hidden" name="action" value="delete_tool">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                <input type="hidden" name="tool_id" value="<?= $tool['id'] ?>">
                                <button type="submit" class="btn-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Section Compétences BTS -->
        <div id="competences" class="section">
            <h2><i class="fas fa-graduation-cap"></i> Compétences BTS (Blocs & Sous-compétences)</h2>
            
            <!-- Grandes compétences -->
            <h3 style="color:#fb923c;margin-bottom:15px;"><i class="fas fa-cubes"></i> Grandes compétences (blocs)</h3>
            <div class="grid">
                <div class="card">
                    <h3><i class="fas fa-plus"></i> Ajouter un bloc</h3>
                    <form method="POST">
                        <input type="hidden" name="action" value="add_competence_block">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        
                        <label>Nom du bloc *</label>
                        <input type="text" name="name" required placeholder="Ex: Gérer le patrimoine informatique">
                        
                        <label>Icône Font Awesome</label>
                        <input type="text" name="icon" placeholder="fas fa-server, fas fa-code...">
                        
                        <label>Couleur</label>
                        <input type="color" name="color" value="#f97316" style="height:40px;">
                        
                        <label>Ordre d'affichage</label>
                        <input type="number" name="order_index" value="0" min="0">
                        
                        <button type="submit" class="btn-primary"><i class="fas fa-plus"></i> Ajouter</button>
                    </form>
                </div>
                
                <div class="card">
                    <h3><i class="fas fa-list"></i> Blocs existants (<?= count($competenceBlocks) ?>)</h3>
                    <?php if (empty($competenceBlocks)): ?>
                    <p style="color: #6c6c7c;">Aucun bloc pour le moment.</p>
                    <?php else: ?>
                    <?php foreach ($competenceBlocks as $block): ?>
                    <div class="project-item">
                        <div class="project-info" style="display:flex;align-items:center;gap:10px;">
                            <span style="display:inline-block;width:12px;height:12px;border-radius:50%;background:<?= htmlspecialchars($block['color'] ?? '#f97316') ?>;"></span>
                            <i class="<?= htmlspecialchars($block['icon']) ?>"></i>
                            <div>
                                <h4><?= htmlspecialchars($block['name']) ?></h4>
                                <small style="color:#6c6c7c;">Ordre: <?= $block['order_index'] ?></small>
                            </div>
                        </div>
                        <div style="display: flex; gap: 8px;">
                            <button type="button" class="btn-edit" onclick='editCompetenceBlock(<?= json_encode($block) ?>)'>
                                <i class="fas fa-edit"></i>
                            </button>
                            <form method="POST" style="display:inline;" onsubmit="return confirm('Supprimer ce bloc et toutes ses sous-compétences ?')">
                                <input type="hidden" name="action" value="delete_competence_block">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                <input type="hidden" name="block_id" value="<?= $block['id'] ?>">
                                <button type="submit" class="btn-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Sous-compétences -->
            <h3 style="color:#fb923c;margin:30px 0 15px;"><i class="fas fa-puzzle-piece"></i> Sous-compétences</h3>
            <div class="grid">
                <div class="card">
                    <h3><i class="fas fa-plus"></i> Ajouter une sous-compétence</h3>
                    <form method="POST">
                        <input type="hidden" name="action" value="add_sub_competence">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        
                        <label>Bloc de rattachement *</label>
                        <select name="competence_block_id" required>
                            <option value="">-- Choisir un bloc --</option>
                            <?php foreach ($competenceBlocks as $block): ?>
                            <option value="<?= $block['id'] ?>"><?= htmlspecialchars($block['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        
                        <label>Nom de la sous-compétence *</label>
                        <input type="text" name="name" required placeholder="Ex: Gérer les tickets d'incidents">
                        
                        <label>Ordre d'affichage</label>
                        <input type="number" name="order_index" value="0" min="0">
                        
                        <button type="submit" class="btn-primary"><i class="fas fa-plus"></i> Ajouter</button>
                    </form>
                </div>
                
                <div class="card">
                    <h3><i class="fas fa-list"></i> Sous-compétences existantes (<?= count($subCompetences) ?>)</h3>
                    <?php if (empty($subCompetences)): ?>
                    <p style="color: #6c6c7c;">Aucune sous-compétence pour le moment.</p>
                    <?php else: ?>
                    <?php 
                    $currentBlock = null;
                    foreach ($subCompetences as $sc): 
                        if ($sc['block_name'] !== $currentBlock):
                            $currentBlock = $sc['block_name'];
                    ?>
                    <h4 style="margin-top:15px;color:#fb923c;border-bottom:1px solid #3a3a5a;padding-bottom:5px;">
                        <?= htmlspecialchars($currentBlock ?: 'Sans bloc') ?>
                    </h4>
                    <?php endif; ?>
                    <div class="social-item" style="margin-bottom:8px;">
                        <div>
                            <strong><?= htmlspecialchars($sc['name']) ?></strong>
                        </div>
                        <div style="display:flex;gap:8px;">
                            <button type="button" class="btn-edit" onclick='editSubCompetence(<?= json_encode($sc) ?>)'>
                                <i class="fas fa-edit"></i>
                            </button>
                            <form method="POST" style="display:inline;" onsubmit="return confirm('Supprimer cette sous-compétence ?')">
                                <input type="hidden" name="action" value="delete_sub_competence">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                <input type="hidden" name="sub_competence_id" value="<?= $sc['id'] ?>">
                                <button type="submit" class="btn-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Section Réseaux Sociaux -->
        <div id="socials" class="section">
            <h2><i class="fas fa-share-alt"></i> Réseaux Sociaux</h2>
            
            <div class="grid">
                <div class="card">
                    <h3><i class="fas fa-plus"></i> Ajouter un lien</h3>
                    <form method="POST">
                        <input type="hidden" name="action" value="add_social">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        
                        <label>Nom du réseau</label>
                        <input type="text" name="name" required placeholder="Ex: GitHub, LinkedIn, Twitter">
                        
                        <label>URL</label>
                        <input type="url" name="url" required placeholder="https://github.com/votreusername">
                        
                        <label>Icône Font Awesome</label>
                        <input type="text" name="icon" required placeholder="fab fa-github">
                        <p class="help-text">
                            Exemples: fab fa-github, fab fa-linkedin, fab fa-twitter, fab fa-instagram, fab fa-youtube
                            <br><a href="https://fontawesome.com/icons?d=gallery&s=brands" target="_blank">Voir toutes les icônes →</a>
                        </p>
                        
                        <button type="submit" class="btn-primary"><i class="fas fa-plus"></i> Ajouter</button>
                    </form>
                </div>
                
                <div class="card">
                    <h3><i class="fas fa-list"></i> Liens existants</h3>
                    
                    <?php foreach ($socials as $social): ?>
                    <div class="social-item">
                        <div style="display: flex; align-items: center;">
                            <i class="<?= htmlspecialchars($social['icon']) ?> icon-preview"></i>
                            <div>
                                <strong><?= htmlspecialchars($social['name']) ?></strong>
                                <br><small style="color: #6c6c7c;"><?= htmlspecialchars($social['url']) ?></small>
                            </div>
                        </div>
                        <form method="POST" style="display: inline;" onsubmit="return confirm('Supprimer ce lien ?')">
                            <input type="hidden" name="action" value="delete_social">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                            <input type="hidden" name="social_id" value="<?= $social['id'] ?>">
                            <button type="submit" class="btn-danger"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal Edition Projet -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-edit"></i> Modifier le projet</h3>
                <button type="button" class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <form method="POST" id="editProjectForm">
                <input type="hidden" name="action" value="edit_project">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <input type="hidden" name="project_id" id="edit_project_id">
                
                <label>Titre du projet *</label>
                <input type="text" name="title" id="edit_title" required>
                
                <label>Slug (URL)</label>
                <input type="text" name="slug" id="edit_slug">
                
                <label>Description courte *</label>
                <textarea name="description" id="edit_description" rows="2" required></textarea>
                
                <label>Description longue</label>
                <textarea name="long_description" id="edit_long_description" rows="4"></textarea>
                
                <label>Image principale</label>
                <input type="text" name="image" id="edit_image" placeholder="nom-image.jpg">
                <p class="help-text">Placez vos images dans: assets/images/projects/</p>
                
                <label>Images galerie (séparées par des virgules)</label>
                <input type="text" name="gallery_images" id="edit_gallery_images" placeholder="img1.jpg, img2.jpg, img3.jpg">
                <p class="help-text">Ces images s'afficheront dans la modal du projet</p>
                
                <label>URL du site</label>
                <input type="url" name="url" id="edit_url">
                
                <label>URL GitHub</label>
                <input type="url" name="github_url" id="edit_github_url">
                
                <label>Technologies (séparées par des virgules)</label>
                <input type="text" name="technologies" id="edit_technologies">
                
                <div class="checkbox-group">
                    <input type="checkbox" name="featured" id="edit_featured">
                    <label for="edit_featured">Mettre en avant sur la page d'accueil</label>
                </div>
                
                <div class="modal-actions">
                    <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Enregistrer</button>
                    <button type="button" class="btn-secondary" onclick="closeModal()">Annuler</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Modal Edition Expérience -->
    <div id="editExperienceModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-edit"></i> Modifier l'expérience</h3>
                <button type="button" class="modal-close" onclick="closeExperienceModal()">&times;</button>
            </div>
            <form method="POST" id="editExperienceForm">
                <input type="hidden" name="action" value="edit_experience">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <input type="hidden" name="experience_id" id="edit_experience_id">
                
                <label>Entreprise *</label>
                <input type="text" name="company" id="edit_company" required>
                
                <label>Poste *</label>
                <input type="text" name="position" id="edit_position" required>
                
                <label>Type de contrat</label>
                <select name="type" id="edit_type">
                    <option value="Full-time">CDI (Full-time)</option>
                    <option value="Part-time">Temps partiel</option>
                    <option value="Contract">CDD/Contrat</option>
                    <option value="Freelance">Freelance</option>
                    <option value="Internship">Stage</option>
                    <option value="Apprenticeship">Alternance</option>
                </select>
                
                <label>Description</label>
                <textarea name="description" id="edit_exp_description" rows="2"></textarea>
                
                <label>Responsabilités (une par ligne)</label>
                <textarea name="responsibilities" id="edit_responsibilities" rows="4"></textarea>
                
                <label>Date de début *</label>
                <input type="date" name="start_date" id="edit_start_date" required>
                
                <div class="checkbox-group">
                    <input type="checkbox" name="is_current" id="edit_is_current" onchange="toggleEditEndDate()">
                    <label for="edit_is_current">Poste actuel</label>
                </div>
                
                <label>Date de fin</label>
                <input type="date" name="end_date" id="edit_end_date">
                
                <label>Ordre d'affichage</label>
                <input type="number" name="order_index" id="edit_exp_order" value="0" min="0">
                
                <div class="modal-actions">
                    <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Enregistrer</button>
                    <button type="button" class="btn-secondary" onclick="closeExperienceModal()">Annuler</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Modal Edition Skill -->
    <div id="editSkillModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-edit"></i> Modifier la compétence</h3>
                <button type="button" class="modal-close" onclick="closeSkillModal()">&times;</button>
            </div>
            <form method="POST" id="editSkillForm">
                <input type="hidden" name="action" value="edit_skill">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <input type="hidden" name="skill_id" id="edit_skill_id">
                
                <label>Nom de la compétence *</label>
                <input type="text" name="name" id="edit_skill_name" required>
                
                <label>Catégorie</label>
                <input type="text" name="category" id="edit_skill_category">
                
                <label>Icône Font Awesome</label>
                <input type="text" name="icon" id="edit_skill_icon">
                
                <label>Niveau (0-100)</label>
                <input type="range" name="level" id="edit_skill_level" min="0" max="100" value="50" oninput="document.getElementById('edit_skill_level_display').textContent = this.value + '%'">
                <span id="edit_skill_level_display">50%</span>
                
                <label>Ordre d'affichage</label>
                <input type="number" name="order_index" id="edit_skill_order" value="0" min="0">
                
                <div class="modal-actions">
                    <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Enregistrer</button>
                    <button type="button" class="btn-secondary" onclick="closeSkillModal()">Annuler</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Modal Edition Tool -->
    <div id="editToolModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-edit"></i> Modifier le tool</h3>
                <button type="button" class="modal-close" onclick="closeToolModal()">&times;</button>
            </div>
            <form method="POST" id="editToolForm">
                <input type="hidden" name="action" value="edit_tool">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <input type="hidden" name="tool_id" id="edit_tool_id">
                
                <label>Nom de l'outil *</label>
                <input type="text" name="name" id="edit_tool_name" required>
                
                <label>Description</label>
                <textarea name="description" id="edit_tool_description" rows="2"></textarea>
                
                <label>Icône Font Awesome</label>
                <input type="text" name="icon" id="edit_tool_icon">
                
                <label>URL (optionnel)</label>
                <input type="url" name="url" id="edit_tool_url">
                
                <label>Catégorie</label>
                <input type="text" name="category" id="edit_tool_category">
                
                <label>Ordre d'affichage</label>
                <input type="number" name="order_index" id="edit_tool_order" value="0" min="0">
                
                <div class="modal-actions">
                    <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Enregistrer</button>
                    <button type="button" class="btn-secondary" onclick="closeToolModal()">Annuler</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Modal Edition Bloc Compétence -->
    <div id="editCompetenceBlockModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-edit"></i> Modifier le bloc</h3>
                <button type="button" class="modal-close" onclick="closeCompetenceBlockModal()">&times;</button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="edit_competence_block">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <input type="hidden" name="block_id" id="edit_block_id">
                
                <label>Nom du bloc *</label>
                <input type="text" name="name" id="edit_block_name" required>
                
                <label>Icône Font Awesome</label>
                <input type="text" name="icon" id="edit_block_icon">
                
                <label>Couleur</label>
                <input type="color" name="color" id="edit_block_color" style="height:40px;">
                
                <label>Ordre d'affichage</label>
                <input type="number" name="order_index" id="edit_block_order" value="0" min="0">
                
                <div class="modal-actions">
                    <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Enregistrer</button>
                    <button type="button" class="btn-secondary" onclick="closeCompetenceBlockModal()">Annuler</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Modal Edition Sous-compétence -->
    <div id="editSubCompetenceModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-edit"></i> Modifier la sous-compétence</h3>
                <button type="button" class="modal-close" onclick="closeSubCompetenceModal()">&times;</button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="edit_sub_competence">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <input type="hidden" name="sub_competence_id" id="edit_sc_id">
                
                <label>Bloc de rattachement *</label>
                <select name="competence_block_id" id="edit_sc_block_id" required>
                    <?php foreach ($competenceBlocks as $block): ?>
                    <option value="<?= $block['id'] ?>"><?= htmlspecialchars($block['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                
                <label>Nom *</label>
                <input type="text" name="name" id="edit_sc_name" required>
                
                <label>Ordre d'affichage</label>
                <input type="number" name="order_index" id="edit_sc_order" value="0" min="0">
                
                <div class="modal-actions">
                    <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Enregistrer</button>
                    <button type="button" class="btn-secondary" onclick="closeSubCompetenceModal()">Annuler</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Modal Compétences Projet -->
    <div id="projectCompetencesModal" class="modal">
        <div class="modal-content" style="max-width:800px;">
            <div class="modal-header">
                <h3><i class="fas fa-graduation-cap"></i> Compétences — <span id="pc_project_title"></span></h3>
                <button type="button" class="modal-close" onclick="closeProjectCompetencesModal()">&times;</button>
            </div>
            
            <!-- Partie 1 : Grandes compétences -->
            <h4 style="color:#fb923c;margin-bottom:10px;">1. Grandes compétences mobilisées</h4>
            <form method="POST" style="margin-bottom:25px;">
                <input type="hidden" name="action" value="save_project_competence_blocks">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <input type="hidden" name="project_id" id="pc_blocks_project_id">
                
                <div id="pc_blocks_checkboxes" style="display:flex;flex-direction:column;gap:10px;margin-bottom:15px;">
                    <?php foreach ($competenceBlocks as $block): ?>
                    <div class="checkbox-group">
                        <input type="checkbox" name="competence_block_ids[]" value="<?= $block['id'] ?>" id="pc_block_<?= $block['id'] ?>" class="pc-block-checkbox">
                        <label for="pc_block_<?= $block['id'] ?>" style="color:#fff;">
                            <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:<?= htmlspecialchars($block['color'] ?? '#f97316') ?>;margin-right:5px;"></span>
                            <?= htmlspecialchars($block['name']) ?>
                        </label>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Enregistrer les blocs</button>
            </form>
            
            <!-- Partie 2 : Sous-compétences avec justification -->
            <h4 style="color:#fb923c;margin-bottom:10px;">2. Sous-compétences avec justification</h4>
            <form method="POST" style="margin-bottom:20px;" onsubmit="return validateJustification(this)">
                <input type="hidden" name="action" value="add_project_sub_competence">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <input type="hidden" name="project_id" id="pc_sub_project_id">
                
                <label>Sous-compétence</label>
                <select name="sub_competence_id" required style="margin-bottom:10px;">
                    <option value="">-- Choisir --</option>
                    <?php 
                    $currentBlockName = null;
                    foreach ($subCompetences as $sc): 
                        if ($sc['block_name'] !== $currentBlockName):
                            if ($currentBlockName !== null) echo '</optgroup>';
                            $currentBlockName = $sc['block_name'];
                            echo '<optgroup label="' . htmlspecialchars($currentBlockName ?? 'Sans bloc') . '">';
                        endif;
                    ?>
                    <option value="<?= $sc['id'] ?>"><?= htmlspecialchars($sc['name']) ?></option>
                    <?php endforeach; ?>
                    <?php if ($currentBlockName !== null) echo '</optgroup>'; ?>
                </select>
                
                <label>Justification *</label>
                <textarea name="justification" rows="3" required placeholder="Décrivez en quoi cette sous-compétence est mobilisée dans ce projet..."></textarea>
                
                <button type="submit" class="btn-primary" style="margin-top:10px;"><i class="fas fa-plus"></i> Ajouter</button>
            </form>
            
            <div id="pc_sub_list">
                <!-- Rempli dynamiquement par JS -->
            </div>
        </div>
    </div>
    
    <!-- Modal Edition Justification -->
    <div id="editJustificationModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-edit"></i> Modifier la justification</h3>
                <button type="button" class="modal-close" onclick="closeJustificationModal()">&times;</button>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="edit_project_sub_competence">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <input type="hidden" name="psc_id" id="edit_psc_id">
                
                <label>Justification *</label>
                <textarea name="justification" id="edit_psc_justification" rows="4" required></textarea>
                
                <div class="modal-actions">
                    <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Enregistrer</button>
                    <button type="button" class="btn-secondary" onclick="closeJustificationModal()">Annuler</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        // Données des compétences par projet (pour charger dynamiquement)
        var projectCompetenceData = <?php
            $pcData = [];
            foreach ($projects as $p) {
                $pid = $p['id'];
                $blocks = $db->fetchAll("SELECT competence_block_id FROM project_competence_blocks WHERE project_id = ?", [$pid]);
                $subs = $db->fetchAll("SELECT psc.id as psc_id, psc.justification, sc.name as sc_name, cb.name as block_name 
                    FROM project_sub_competences psc 
                    JOIN sub_competences sc ON psc.sub_competence_id = sc.id 
                    LEFT JOIN competence_blocks cb ON sc.competence_block_id = cb.id 
                    WHERE psc.project_id = ? 
                    ORDER BY cb.order_index, sc.order_index", [$pid]);
                $pcData[$pid] = [
                    'blocks' => array_column($blocks, 'competence_block_id'),
                    'subs' => $subs
                ];
            }
            echo json_encode($pcData);
        ?>;

        function showSection(id) {
            // Cacher toutes les sections
            document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
            document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            
            // Afficher la section sélectionnée
            document.getElementById(id).classList.add('active');
            event.target.classList.add('active');
        }
        
        // ====== PROJETS ======
        function editProject(project) {
            document.getElementById('edit_project_id').value = project.id;
            document.getElementById('edit_title').value = project.title || '';
            document.getElementById('edit_slug').value = project.slug || '';
            document.getElementById('edit_description').value = project.description || '';
            document.getElementById('edit_long_description').value = project.long_description || '';
            document.getElementById('edit_image').value = project.image || '';
            document.getElementById('edit_gallery_images').value = project.gallery_images || '';
            document.getElementById('edit_url').value = project.url || '';
            document.getElementById('edit_github_url').value = project.github_url || '';
            document.getElementById('edit_technologies').value = project.technologies || '';
            document.getElementById('edit_featured').checked = project.featured == 1;
            
            document.getElementById('editModal').classList.add('active');
        }
        
        function closeModal() {
            document.getElementById('editModal').classList.remove('active');
        }
        
        // ====== EXPÉRIENCES ======
        function toggleEndDate() {
            var isCurrent = document.getElementById('is_current').checked;
            document.getElementById('end_date').disabled = isCurrent;
            if (isCurrent) {
                document.getElementById('end_date').value = '';
            }
        }
        
        function toggleEditEndDate() {
            var isCurrent = document.getElementById('edit_is_current').checked;
            document.getElementById('edit_end_date').disabled = isCurrent;
            if (isCurrent) {
                document.getElementById('edit_end_date').value = '';
            }
        }
        
        function editExperience(exp) {
            document.getElementById('edit_experience_id').value = exp.id;
            document.getElementById('edit_company').value = exp.company || '';
            document.getElementById('edit_position').value = exp.position || '';
            document.getElementById('edit_type').value = exp.type || 'Full-time';
            document.getElementById('edit_exp_description').value = exp.description || '';
            document.getElementById('edit_responsibilities').value = exp.responsibilities || '';
            document.getElementById('edit_start_date').value = exp.start_date || '';
            document.getElementById('edit_end_date').value = exp.end_date || '';
            document.getElementById('edit_is_current').checked = exp.is_current == 1;
            document.getElementById('edit_exp_order').value = exp.order_index || 0;
            
            toggleEditEndDate();
            document.getElementById('editExperienceModal').classList.add('active');
        }
        
        function closeExperienceModal() {
            document.getElementById('editExperienceModal').classList.remove('active');
        }
        
        // ====== SKILLS ======
        function editSkill(skill) {
            document.getElementById('edit_skill_id').value = skill.id;
            document.getElementById('edit_skill_name').value = skill.name || '';
            document.getElementById('edit_skill_category').value = skill.category || '';
            document.getElementById('edit_skill_icon').value = skill.icon || '';
            document.getElementById('edit_skill_level').value = skill.level || 50;
            document.getElementById('edit_skill_level_display').textContent = (skill.level || 50) + '%';
            document.getElementById('edit_skill_order').value = skill.order_index || 0;
            
            document.getElementById('editSkillModal').classList.add('active');
        }
        
        function closeSkillModal() {
            document.getElementById('editSkillModal').classList.remove('active');
        }
        
        // ====== TOOLS ======
        function editTool(tool) {
            document.getElementById('edit_tool_id').value = tool.id;
            document.getElementById('edit_tool_name').value = tool.name || '';
            document.getElementById('edit_tool_description').value = tool.description || '';
            document.getElementById('edit_tool_icon').value = tool.icon || '';
            document.getElementById('edit_tool_url').value = tool.url || '';
            document.getElementById('edit_tool_category').value = tool.category || '';
            document.getElementById('edit_tool_order').value = tool.order_index || 0;
            
            document.getElementById('editToolModal').classList.add('active');
        }
        
        function closeToolModal() {
            document.getElementById('editToolModal').classList.remove('active');
        }
        
        // ====== BLOCS COMPÉTENCES ======
        function editCompetenceBlock(block) {
            document.getElementById('edit_block_id').value = block.id;
            document.getElementById('edit_block_name').value = block.name || '';
            document.getElementById('edit_block_icon').value = block.icon || '';
            document.getElementById('edit_block_color').value = block.color || '#f97316';
            document.getElementById('edit_block_order').value = block.order_index || 0;
            document.getElementById('editCompetenceBlockModal').classList.add('active');
        }
        function closeCompetenceBlockModal() {
            document.getElementById('editCompetenceBlockModal').classList.remove('active');
        }
        
        // ====== SOUS-COMPÉTENCES ======
        function editSubCompetence(sc) {
            document.getElementById('edit_sc_id').value = sc.id;
            document.getElementById('edit_sc_name').value = sc.name || '';
            document.getElementById('edit_sc_block_id').value = sc.competence_block_id || '';
            document.getElementById('edit_sc_order').value = sc.order_index || 0;
            document.getElementById('editSubCompetenceModal').classList.add('active');
        }
        function closeSubCompetenceModal() {
            document.getElementById('editSubCompetenceModal').classList.remove('active');
        }
        
        // ====== COMPÉTENCES PROJET ======
        function openProjectCompetences(projectId, projectTitle) {
            document.getElementById('pc_project_title').textContent = projectTitle;
            document.getElementById('pc_blocks_project_id').value = projectId;
            document.getElementById('pc_sub_project_id').value = projectId;
            
            // Charger les blocs cochés
            var data = projectCompetenceData[projectId] || {blocks: [], subs: []};
            document.querySelectorAll('.pc-block-checkbox').forEach(function(cb) {
                cb.checked = data.blocks.indexOf(parseInt(cb.value)) !== -1;
            });
            
            // Charger la liste des sous-compétences
            var listHtml = '';
            if (data.subs.length === 0) {
                listHtml = '<p style="color:#6c6c7c;">Aucune sous-compétence ajoutée.</p>';
            } else {
                data.subs.forEach(function(s) {
                    var excerpt = s.justification.length > 80 ? s.justification.substring(0, 80) + '...' : s.justification;
                    listHtml += '<div class="project-item" style="flex-direction:column;align-items:flex-start;gap:8px;">' +
                        '<div style="width:100%;display:flex;justify-content:space-between;align-items:center;">' +
                        '<div><strong>' + escapeHtml(s.sc_name) + '</strong> <small style="color:#6c6c7c;">(' + escapeHtml(s.block_name || '') + ')</small></div>' +
                        '<div style="display:flex;gap:8px;">' +
                        '<button type="button" class="btn-edit" onclick="editJustification(' + s.psc_id + ', ' + JSON.stringify(JSON.stringify(s.justification)) + ')"><i class="fas fa-edit"></i></button>' +
                        '<form method="POST" style="display:inline;" onsubmit="return confirm(\'Retirer cette sous-compétence ?\');">' +
                        '<input type="hidden" name="action" value="delete_project_sub_competence">' +
                        '<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">' +
                        '<input type="hidden" name="psc_id" value="' + s.psc_id + '">' +
                        '<button type="submit" class="btn-danger"><i class="fas fa-trash"></i></button></form></div></div>' +
                        '<p style="color:#a0a0b0;font-size:0.85rem;margin:0;">' + escapeHtml(excerpt) + '</p></div>';
                });
            }
            document.getElementById('pc_sub_list').innerHTML = listHtml;
            
            document.getElementById('projectCompetencesModal').classList.add('active');
        }
        function closeProjectCompetencesModal() {
            document.getElementById('projectCompetencesModal').classList.remove('active');
        }
        
        function editJustification(pscId, justification) {
            document.getElementById('edit_psc_id').value = pscId;
            document.getElementById('edit_psc_justification').value = justification;
            document.getElementById('editJustificationModal').classList.add('active');
        }
        function closeJustificationModal() {
            document.getElementById('editJustificationModal').classList.remove('active');
        }
        
        function validateJustification(form) {
            var j = form.querySelector('textarea[name="justification"]');
            if (!j.value.trim()) {
                alert('La justification est obligatoire !');
                j.focus();
                return false;
            }
            return true;
        }
        
        function escapeHtml(text) {
            var div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        // ====== FERMETURE DES MODALES ======
        // Fermer la modal en cliquant à l'extérieur
        document.querySelectorAll('.modal').forEach(function(modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.remove('active');
                }
            });
        });
        
        // Fermer avec Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal').forEach(function(modal) {
                    modal.classList.remove('active');
                });
            }
        });
    </script>
</body>
</html>
