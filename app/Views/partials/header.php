<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="assets-path" content="<?= ASSETS_PATH ?>">
    <title><?= htmlspecialchars($pageTitle ?? 'Portfolio') ?> - <?= SITE_NAME ?></title>
    <meta name="description" content="<?= htmlspecialchars($profile['bio'] ?? SITE_DESCRIPTION) ?>">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= ASSETS_PATH ?>/images/favicon.png">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>/css/style.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <nav class="nav">
                <a href="<?= SITE_URL ?>" class="logo">
                    <span class="logo-prefix">~</span>
                    <span class="logo-name"><?= htmlspecialchars($profile['full_name'] ?? 'Portfolio') ?></span>
                    <span class="logo-cursor">$</span>
                    <span class="cursor-blink">▌</span>
                </a>
                
                <ul class="nav-links">
                    <li><a href="<?= SITE_URL ?>" class="<?= ($pageTitle ?? '') === 'Accueil' ? 'active' : '' ?>">Accueil</a></li>
                    <li><a href="<?= SITE_URL ?>/blog" class="<?= ($pageTitle ?? '') === 'Blog' ? 'active' : '' ?>">Veille</a></li>
                    <li><a href="<?= SITE_URL ?>/projects" class="<?= ($pageTitle ?? '') === 'Projets' ? 'active' : '' ?>">Projets</a></li>
                    <li><a href="<?= SITE_URL ?>/resume" class="<?= ($pageTitle ?? '') === 'Parcours' ? 'active' : '' ?>">Parcours</a></li>
                    <li><a href="<?= SITE_URL ?>/tools" class="<?= ($pageTitle ?? '') === 'Outils' ? 'active' : '' ?>">Outils</a></li>
                </ul>

                <?php
                $githubLink = null;
                $gitlabLink = null;
                $otherSocialLinks = [];

                foreach ($socialLinks as $social) {
                    $name = strtolower((string)($social['name'] ?? ''));
                    $url = strtolower((string)($social['url'] ?? ''));

                    if (strpos($name, 'github') !== false || strpos($url, 'github.com') !== false) {
                        $githubLink = $social;
                        continue;
                    }

                    if (strpos($name, 'gitlab') !== false || strpos($url, 'gitlab.com') !== false) {
                        $gitlabLink = $social;
                        continue;
                    }

                    $otherSocialLinks[] = $social;
                }

                $gitlabUrl = trim((string)($gitlabLink['url'] ?? 'https://gitlab.com/'));
                $githubUrl = trim((string)($githubLink['url'] ?? 'https://github.com/'));
                ?>

                <div class="nav-social">
                    <?php foreach ($otherSocialLinks as $social): ?>
                    <a href="<?= htmlspecialchars($social['url']) ?>" target="_blank" rel="noopener noreferrer" title="<?= htmlspecialchars($social['name']) ?>">
                        <i class="<?= htmlspecialchars($social['icon']) ?>"></i>
                    </a>
                    <?php endforeach; ?>

                    <div class="git-switcher">
                        <button type="button" class="git-switcher-trigger" aria-label="Liens Git">
                            <i class="fa-brands fa-git-alt"></i>
                        </button>
                        <div class="git-switcher-menu" role="menu" aria-label="Plateformes Git">
                            <a href="<?= htmlspecialchars($gitlabUrl) ?>" target="_blank" rel="noopener noreferrer" class="git-switcher-item" role="menuitem">
                                <i class="fa-brands fa-gitlab"></i>
                                <span>GitLab</span>
                            </a>
                            <a href="<?= htmlspecialchars($githubUrl) ?>" target="_blank" rel="noopener noreferrer" class="git-switcher-item" role="menuitem">
                                <i class="fa-brands fa-github"></i>
                                <span>GitHub</span>
                            </a>
                        </div>
                    </div>
                </div>

                <button class="mobile-menu-btn" aria-label="Menu">
                    <i class="fas fa-bars"></i>
                </button>
            </nav>
        </div>
    </header>

    <!-- Mobile Menu -->
    <div class="mobile-menu">
        <ul class="mobile-nav-links">
            <li><a href="<?= SITE_URL ?>">Accueil</a></li>
            <li><a href="<?= SITE_URL ?>/blog">Veille</a></li>
            <li><a href="<?= SITE_URL ?>/projects">Projets</a></li>
            <li><a href="<?= SITE_URL ?>/resume">Parcours</a></li>
            <li><a href="<?= SITE_URL ?>/tools">Outils</a></li>
        </ul>
        <div class="mobile-social">
            <?php foreach ($socialLinks as $social): ?>
            <a href="<?= htmlspecialchars($social['url']) ?>" target="_blank" rel="noopener noreferrer">
                <i class="<?= htmlspecialchars($social['icon']) ?>"></i>
            </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Main Content -->
    <main class="main">
