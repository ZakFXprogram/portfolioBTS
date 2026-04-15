<?php
/**
 * Gestionnaire de base de données SQLite
 */
class Database
{
    private static $instance = null;
    private $pdo;

    private function __construct()
    {
        try {
            $dbDir = dirname(DB_PATH);
            if (!is_dir($dbDir)) {
                mkdir($dbDir, 0777, true);
            }

            $this->pdo = new PDO('sqlite:' . DB_PATH);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die('Erreur de connexion à la base de données: ' . $e->getMessage());
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function init()
    {
        $db = self::getInstance();
        $db->createTables();
        $db->runMigrations();
        $db->seedIfEmpty();
    }

    public function getConnection()
    {
        return $this->pdo;
    }

    private function runMigrations()
    {
        // Ajouter la colonne gallery_images si elle n'existe pas
        try {
            $this->pdo->exec("ALTER TABLE projects ADD COLUMN gallery_images TEXT");
        } catch (PDOException $e) {
            // La colonne existe déjà, ignorer l'erreur
        }
    }

    public function query($sql, $params = [])
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function fetchAll($sql, $params = [])
    {
        return $this->query($sql, $params)->fetchAll();
    }

    public function fetch($sql, $params = [])
    {
        return $this->query($sql, $params)->fetch();
    }

    private function createTables()
    {
        // Table des projets
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS projects (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title VARCHAR(255) NOT NULL,
                slug VARCHAR(255) UNIQUE NOT NULL,
                description TEXT,
                long_description TEXT,
                image VARCHAR(255),
                gallery_images TEXT,
                url VARCHAR(255),
                github_url VARCHAR(255),
                technologies TEXT,
                featured INTEGER DEFAULT 0,
                order_index INTEGER DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");

        // Table des expériences professionnelles
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS experiences (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                company VARCHAR(255) NOT NULL,
                position VARCHAR(255) NOT NULL,
                location VARCHAR(255),
                type VARCHAR(50) DEFAULT 'Remote',
                start_date DATE,
                end_date DATE,
                is_current INTEGER DEFAULT 0,
                description TEXT,
                responsibilities TEXT,
                order_index INTEGER DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");

        // Table des compétences
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS skills (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(100) NOT NULL,
                category VARCHAR(100),
                level INTEGER DEFAULT 0,
                icon VARCHAR(50),
                order_index INTEGER DEFAULT 0
            )
        ");

        // Table des liens sociaux
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS social_links (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(100) NOT NULL,
                url VARCHAR(255) NOT NULL,
                icon VARCHAR(50) NOT NULL,
                order_index INTEGER DEFAULT 0,
                is_active INTEGER DEFAULT 1
            )
        ");

        // Table des informations du profil
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS profile (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                full_name VARCHAR(255) NOT NULL,
                title VARCHAR(255),
                bio TEXT,
                email VARCHAR(255),
                phone VARCHAR(50),
                location VARCHAR(255),
                avatar VARCHAR(255),
                resume_file VARCHAR(255),
                timezone VARCHAR(100) DEFAULT 'Europe/Paris'
            )
        ");

        // Table des articles de blog
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS posts (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title VARCHAR(255) NOT NULL,
                slug VARCHAR(255) UNIQUE NOT NULL,
                excerpt TEXT,
                content TEXT,
                image VARCHAR(255),
                published INTEGER DEFAULT 0,
                published_at DATETIME,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");

        // Table des outils/uses
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS tools (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(255) NOT NULL,
                description TEXT,
                category VARCHAR(100),
                url VARCHAR(255),
                icon VARCHAR(50),
                order_index INTEGER DEFAULT 0
            )
        ");

        // Table des clients
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS clients (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(255) NOT NULL,
                logo VARCHAR(255),
                url VARCHAR(255),
                order_index INTEGER DEFAULT 0,
                is_active INTEGER DEFAULT 1
            )
        ");

        // Table des images de projets (galerie)
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS project_images (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                project_id INTEGER NOT NULL,
                image VARCHAR(255) NOT NULL,
                caption VARCHAR(255),
                order_index INTEGER DEFAULT 0,
                FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
            )
        ");

        // Table des grandes compétences (blocs)
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS competence_blocks (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(255) NOT NULL,
                icon VARCHAR(50),
                color VARCHAR(20),
                order_index INTEGER DEFAULT 0
            )
        ");

        // Table des sous-compétences
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS sub_competences (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                competence_block_id INTEGER NOT NULL,
                name VARCHAR(255) NOT NULL,
                order_index INTEGER DEFAULT 0,
                FOREIGN KEY (competence_block_id) REFERENCES competence_blocks(id) ON DELETE CASCADE
            )
        ");

        // Table pivot : projet <-> grande compétence + justification par bloc
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS project_competence_blocks (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                project_id INTEGER NOT NULL,
                competence_block_id INTEGER NOT NULL,
                justification TEXT DEFAULT '',
                FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
                FOREIGN KEY (competence_block_id) REFERENCES competence_blocks(id) ON DELETE CASCADE,
                UNIQUE(project_id, competence_block_id)
            )
        ");

        // Table pivot : projet <-> sous-compétence
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS project_sub_competences (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                project_id INTEGER NOT NULL,
                sub_competence_id INTEGER NOT NULL,
                FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
                FOREIGN KEY (sub_competence_id) REFERENCES sub_competences(id) ON DELETE CASCADE,
                UNIQUE(project_id, sub_competence_id)
            )
        ");
    }

    private function seedIfEmpty()
    {
        // Vérifier si les données existent déjà
        $count = $this->fetch("SELECT COUNT(*) as count FROM profile")['count'];
        if ($count > 0) {
            return;
        }

        // Seed le profil
        $this->query("
            INSERT INTO profile (full_name, title, bio, email, location, timezone) 
            VALUES (?, ?, ?, ?, ?, ?)
        ", [
            'Votre Nom',
            'Senior Full-Stack Developer',
            'Je suis un développeur passionné avec plus de 10 ans d\'expérience dans la création de sites web et d\'applications. J\'ai commencé à construire des sites web comme hobby au début des années 2000, et j\'ai tellement aimé ça que j\'ai décidé d\'en faire une carrière. PHP/Laravel a été mon langage principal au fil des ans, mais dernièrement je me suis plongé dans Python et je travaille avec le big data, l\'IA/ML et la recherche vectorielle.',
            'contact@example.com',
            'Paris, France',
            'Europe/Paris'
        ]);

        // Seed les liens sociaux
        $socials = [
            ['GitHub', 'https://github.com/', 'fab fa-github', 1],
            ['npm', 'https://npmjs.com/', 'fab fa-npm', 2],
            ['Stack Overflow', 'https://stackoverflow.com/', 'fab fa-stack-overflow', 3],
            ['X (Twitter)', 'https://twitter.com/', 'fab fa-x-twitter', 4],
            ['LinkedIn', 'https://linkedin.com/', 'fab fa-linkedin', 5],
        ];

        foreach ($socials as $social) {
            $this->query("INSERT INTO social_links (name, url, icon, order_index) VALUES (?, ?, ?, ?)", $social);
        }

        // Seed les projets
        $projects = [
            [
                'Youngstown Phantoms',
                'youngstown-phantoms',
                'Un thème WordPress personnalisé avec intégrations Spotify, Ticketmaster et réseaux sociaux.',
                'Un des nombreux thèmes WordPress personnalisés développés qui inclut des intégrations pour des plugins et des extensions tierces comme Spotify, Ticketmaster, des plateformes de calendrier/planification et des plateformes de marketing sur les réseaux sociaux.',
                'project1.jpg',
                'https://youngstownphantoms.com',
                '',
                'WordPress,PHP,JavaScript,CSS,Spotify API',
                1, 1
            ],
            [
                'TWI Ladder',
                'twi-ladder',
                'Site de tournois et classements gaming pour Tripwire Interactive.',
                'TWI Ladder était un site web de tournois et de classements gaming compétitif que j\'ai conçu et développé pour Tripwire Interactive. L\'objectif principal de ce projet était Red Orchestra 2 et Killing Floor 2.',
                'project2.jpg',
                'https://twiladder.com',
                '',
                'PHP,Laravel,MySQL,Vue.js,Gaming',
                1, 2
            ],
            [
                'AQtion Game',
                'aqtion-game',
                'Site officiel pour AQtion, un remaster du mod Action Quake 2.',
                'Le site officiel d\'AQtion, un remaster standalone cross-platform du mod Action Quake 2 disponible sur Steam. Ce site a été construit avec Next.js et déployé sur Vercel.',
                'project3.jpg',
                'https://aqtiongame.com',
                'https://github.com/actionquake/web',
                'Next.js,React,Vercel,GitHub API',
                1, 3
            ],
        ];

        foreach ($projects as $project) {
            $this->query("
                INSERT INTO projects (title, slug, description, long_description, image, url, github_url, technologies, featured, order_index) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ", $project);
        }

        // Seed les expériences
        $experiences = [
            [
                'Affiliate.com',
                'Senior Full-Stack Software Engineer',
                'Remote',
                'Remote',
                '2025-02-01',
                null,
                1,
                'Développement full-stack sur des applications web scalables.',
                'Développement d\'applications web avec PHP, Laravel et Vue.js',
                1
            ],
            [
                'Arrowhead Engineered Products',
                'Senior Full-Stack Software Engineer',
                'Remote',
                'Remote',
                '2024-10-01',
                '2025-02-01',
                0,
                'Développement et maintenance de systèmes e-commerce.',
                'Architecture de solutions e-commerce complexes',
                2
            ],
            [
                'The Healthy Back Institute',
                'Senior Full-Stack Software Engineer, Team Lead',
                'Remote',
                'Remote',
                '2023-01-01',
                '2024-10-01',
                0,
                'Direction d\'équipe et développement full-stack.',
                'Collaboration avec les chefs de projet, revues de code, pair programming, gestion de projet avec Wrike',
                3
            ],
        ];

        foreach ($experiences as $exp) {
            $this->query("
                INSERT INTO experiences (company, position, location, type, start_date, end_date, is_current, description, responsibilities, order_index) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ", $exp);
        }

        // Seed les clients
        $clients = [
            ['Google', 'google.png', 'https://google.com', 1],
            ['A2 Hosting', 'a2hosting.png', 'https://a2hosting.com', 2],
            ['Tripwire', 'tripwire.png', 'https://tripwireinteractive.com', 3],
            ['Zynex Medical', 'zynex.png', 'https://zynex.com', 4],
            ['Slide', 'slide.png', 'https://slide.com', 5],
        ];

        foreach ($clients as $client) {
            $this->query("INSERT INTO clients (name, logo, url, order_index) VALUES (?, ?, ?, ?)", $client);
        }

        // Seed les outils
        $tools = [
            ['VS Code', 'Mon éditeur de code principal pour tout le développement.', 'Éditeur', 'https://code.visualstudio.com', 'fas fa-code', 1],
            ['PhpStorm', 'IDE puissant pour le développement PHP/Laravel.', 'IDE', 'https://jetbrains.com/phpstorm', 'fas fa-terminal', 2],
            ['Docker', 'Conteneurisation pour des environnements de développement cohérents.', 'DevOps', 'https://docker.com', 'fab fa-docker', 3],
            ['Git', 'Contrôle de version pour tous mes projets.', 'Versionning', 'https://git-scm.com', 'fab fa-git-alt', 4],
            ['Figma', 'Design et prototypage d\'interfaces.', 'Design', 'https://figma.com', 'fab fa-figma', 5],
        ];

        foreach ($tools as $tool) {
            $this->query("INSERT INTO tools (name, description, category, url, icon, order_index) VALUES (?, ?, ?, ?, ?, ?)", $tool);
        }

        // Seed les compétences
        $skills = [
            ['PHP', 'Backend', 95, 'fab fa-php', 1],
            ['Laravel', 'Backend', 95, 'fab fa-laravel', 2],
            ['JavaScript', 'Frontend', 90, 'fab fa-js', 3],
            ['Vue.js', 'Frontend', 85, 'fab fa-vuejs', 4],
            ['Python', 'Backend', 75, 'fab fa-python', 5],
            ['MySQL', 'Database', 90, 'fas fa-database', 6],
            ['Docker', 'DevOps', 80, 'fab fa-docker', 7],
            ['AWS', 'Cloud', 75, 'fab fa-aws', 8],
        ];

        foreach ($skills as $skill) {
            $this->query("INSERT INTO skills (name, category, level, icon, order_index) VALUES (?, ?, ?, ?, ?)", $skill);
        }
    }
}
