# Portfolio PHP
*php -S localhost:8000 router.php*
Un portfolio moderne et responsive inspiré du design de Kyle Ross, construit en PHP pur avec une architecture MVC.

## 🚀 Fonctionnalités

- **Architecture MVC** : Code bien organisé et maintenable
- **Base de données SQLite** : Pas besoin de MySQL, tout est auto-configuré
- **Design Dark Mode** : Interface moderne et élégante
- **Responsive** : S'adapte à tous les écrans
- **Horloge en temps réel** : Affiche l'heure de votre timezone
- **Gestion des projets** : Cards cliquables avec détails
- **CV téléchargeable** : Export PDF disponible
- **Réseaux sociaux** : Icônes et liens configurables
- **Blog** : Système d'articles intégré
- **API REST** : Endpoints JSON disponibles

## 📁 Structure du projet

```
test/
├── index.php                 # Point d'entrée
├── .htaccess                 # Configuration Apache
├── config/
│   └── config.php            # Configuration globale
├── app/
│   ├── Core/
│   │   ├── App.php           # Routeur principal
│   │   ├── Controller.php    # Contrôleur de base
│   │   └── Database.php      # Gestionnaire BDD
│   ├── Controllers/
│   │   ├── HomeController.php
│   │   ├── ProjectController.php
│   │   ├── ResumeController.php
│   │   ├── BlogController.php
│   │   ├── ToolsController.php
│   │   ├── UsesController.php
│   │   ├── ApiController.php
│   │   └── ErrorController.php
│   └── Views/
│       ├── partials/
│       │   ├── header.php
│       │   └── footer.php
│       ├── home.php
│       ├── resume.php
│       ├── tools.php
│       ├── uses.php
│       ├── projects/
│       │   ├── index.php
│       │   └── show.php
│       ├── blog/
│       │   └── index.php
│       └── errors/
│           └── 404.php
├── assets/
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   └── app.js
│   └── images/
│       ├── avatar.png
│       ├── projects/
│       └── clients/
├── database/
│   └── portfolio.db          # BDD SQLite (auto-créée)
└── uploads/
    └── cv/
        └── cv.pdf            # Votre CV
```

## 🛠️ Installation

### Prérequis
- PHP 7.4+ avec extension SQLite (pdo_sqlite)
- Serveur web (Apache, Nginx) ou PHP built-in server

### Étapes

1. **Clonez ou copiez les fichiers** dans votre dossier web

2. **Lancez le serveur PHP intégré** :
   ```bash
   cd test
   php -S localhost:8000
   ```

3. **Ouvrez votre navigateur** : http://localhost:8000

C'est tout ! La base de données est automatiquement créée avec des données de démo.

## ⚙️ Configuration

Modifiez `config/config.php` pour personnaliser :

```php
define('SITE_NAME', 'Mon Portfolio');
define('SITE_URL', 'http://localhost:8000');
define('SITE_AUTHOR', 'Votre Nom');
```

## 📝 Personnalisation

### Modifier le profil
La base de données SQLite contient toutes les informations. Au premier lancement, elle est peuplée avec des données de démo.

Pour modifier manuellement, utilisez un client SQLite (DB Browser for SQLite) ou créez un script PHP admin.

### Ajouter des projets
Insérez dans la table `projects` :
- `title` : Nom du projet
- `slug` : URL-friendly (ex: mon-projet)
- `description` : Description courte
- `image` : Nom du fichier image
- `url` : Lien du projet
- `technologies` : Liste séparée par virgules

### Ajouter votre CV
Placez votre fichier PDF dans `uploads/cv/cv.pdf`

### Modifier les réseaux sociaux
Éditez la table `social_links` avec vos URLs.

## 🎨 Thème

Les couleurs et styles sont dans `assets/css/style.css`. Variables CSS principales :

```css
:root {
    --bg-primary: #0f0f1a;
    --accent-primary: #f97316;
    --text-primary: #ffffff;
}
```

## 📱 Routes disponibles

| Route | Description |
|-------|-------------|
| `/` | Page d'accueil |
| `/projects` | Liste des projets |
| `/project/{slug}` | Détail d'un projet |
| `/resume` | CV |
| `/resume/download` | Télécharger le CV |
| `/blog` | Articles |
| `/tools` | Outils utilisés |
| `/uses` | Setup/équipement |
| `/api/projects` | API JSON projets |
| `/api/socials` | API JSON réseaux sociaux |

## 🔒 Sécurité

- Protection XSS via `htmlspecialchars()`
- Requêtes préparées PDO
- Protection du fichier `.db`
- Headers de sécurité dans `.htaccess`

## 📄 Licence

MIT License - Utilisez librement pour vos projets personnels ou commerciaux.
