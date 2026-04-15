<?php require_once BASE_PATH . '/app/Views/partials/header.php'; ?>

<section class="page-header">
    <div class="container">
        <h1 class="page-title">Blog</h1>
        <p class="page-subtitle">Articles, tutoriels et veille technologique</p>
    </div>
</section>

<!-- Veille Informationnelle -->
<section class="section veille-section">
    <div class="container">
        <div class="veille-header">
            <div class="veille-title-wrapper">
                <i class="fas fa-rss veille-icon"></i>
                <div>
                    <h2 class="veille-title">Veille Informationnelle</h2>
                    <p class="veille-theme"><?= htmlspecialchars($veilleTheme ?? 'Sécurisation des API') ?></p>
                </div>
            </div>
            <div class="veille-badges">
                <span class="veille-badge"><i class="fas fa-server"></i> Mainframe</span>
                <span class="veille-badge"><i class="fas fa-code"></i> COBOL</span>
                <span class="veille-badge"><i class="fas fa-sync-alt"></i> Modernisation</span>
                <span class="veille-badge"><i class="fas fa-cloud"></i> Cloud & Z/OS</span>
            </div>
        </div>

        <?php if (!empty($veilleArticles)): ?>
        <div class="veille-grid">
            <?php foreach ($veilleArticles as $article): ?>
            <article class="veille-card">
                <div class="veille-card-header">
                    <span class="veille-source"><?= htmlspecialchars($article['source']) ?></span>
                    <span class="veille-category"><?= htmlspecialchars($article['category']) ?></span>
                </div>
                <h3 class="veille-card-title">
                    <a href="<?= htmlspecialchars($article['link']) ?>" target="_blank" rel="noopener noreferrer">
                        <?= htmlspecialchars($article['title']) ?>
                    </a>
                </h3>
                <p class="veille-card-description"><?= htmlspecialchars($article['description']) ?></p>
                <div class="veille-card-footer">
                    <time class="veille-date">
                        <i class="far fa-calendar-alt"></i>
                        <?= date('d/m/Y', strtotime($article['date'])) ?>
                    </time>
                    <a href="<?= htmlspecialchars($article['link']) ?>" target="_blank" rel="noopener noreferrer" class="veille-link">
                        Lire <i class="fas fa-external-link-alt"></i>
                    </a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="veille-loading">
            <i class="fas fa-sync-alt fa-spin"></i>
            <p>Chargement de la veille...</p>
        </div>
        <?php endif; ?>

        <div class="veille-info">
            <i class="fas fa-info-circle"></i>
            <p>Cette veille est alimentée automatiquement par des flux RSS de sources reconnues : IBM Z Blog, Planet Mainframe et IBM Developer.</p>
        </div>
    </div>
</section>

<!-- Articles du Blog -->
<section class="section blog-section">
    <div class="container">
        <h2 class="section-title">Mes Articles</h2>
        
        <?php if (empty($posts)): ?>
        <div class="no-content">
            <i class="fas fa-newspaper"></i>
            <p>Aucun article pour le moment.</p>
            <p class="no-content-sub">Revenez bientôt pour de nouveaux contenus !</p>
        </div>
        <?php else: ?>
        
        <div class="posts-grid">
            <?php foreach ($posts as $post): ?>
            <article class="post-card">
                <?php if ($post['image']): ?>
                <div class="post-card-image">
                    <a href="<?= SITE_URL ?>/blog/<?= htmlspecialchars($post['slug']) ?>">
                        <img src="<?= ASSETS_PATH ?>/images/posts/<?= htmlspecialchars($post['image']) ?>" 
                             alt="<?= htmlspecialchars($post['title']) ?>">
                    </a>
                </div>
                <?php endif; ?>
                
                <div class="post-card-content">
                    <time class="post-card-date"><?= date('d M Y', strtotime($post['published_at'])) ?></time>
                    <h2 class="post-card-title">
                        <a href="<?= SITE_URL ?>/blog/<?= htmlspecialchars($post['slug']) ?>">
                            <?= htmlspecialchars($post['title']) ?>
                        </a>
                    </h2>
                    <p class="post-card-excerpt"><?= htmlspecialchars($post['excerpt']) ?></p>
                    <a href="<?= SITE_URL ?>/blog/<?= htmlspecialchars($post['slug']) ?>" class="post-card-link">
                        Lire la suite <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        
        <?php endif; ?>
    </div>
</section>

<?php require_once BASE_PATH . '/app/Views/partials/footer.php'; ?>
