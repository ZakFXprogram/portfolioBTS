<?php require_once BASE_PATH . '/app/Views/partials/header.php'; ?>

<section class="hero">
    <div class="container">
        <div class="hero-content">
            <div class="hero-text">
                <h1 class="hero-title">
                    Bienvenue ! Je suis <?= htmlspecialchars($profile['full_name'] ?? 'Developer') ?>, un 
                    <span class="highlight"><?= htmlspecialchars($profile['title'] ?? 'Full-Stack Developer') ?></span>.
                </h1>
                
                <div class="hero-description">
                    <?= nl2br(htmlspecialchars($profile['bio'] ?? '')) ?>
                </div>
            </div>
            
            <div class="hero-side">
                <div class="hero-avatar">
                    <img src="<?= ASSETS_PATH ?>/images/avatar.jpg" alt="<?= htmlspecialchars($profile['full_name'] ?? 'Avatar') ?>" onerror="this.src='https://via.placeholder.com/300x350/1a1a2e/eee?text=Avatar'">
                </div>
                
                <div class="hero-clock">
                    <p class="clock-timezone"><?= htmlspecialchars($profile['timezone'] ?? 'Europe/Paris') ?></p>
                    <p class="clock-time" id="currentTime">--:--:--</p>
                    <p class="clock-date" id="currentDate">--</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Projects -->
<?php if (!empty($featuredProjects)): ?>
<section class="section featured-projects">
    <div class="container">
        <h2 class="section-title">Projets Récents</h2>
        
        <div class="projects-grid">
            <?php foreach ($featuredProjects as $project): ?>
            <article class="project-card" data-aos="fade-up" onclick="openProjectModal(<?= $project['id'] ?>)">
                <div class="project-card-image">
                    <img src="<?= ASSETS_PATH ?>/images/projects/<?= htmlspecialchars($project['image']) ?>" 
                         alt="<?= htmlspecialchars($project['title']) ?>"
                         onerror="this.src='https://via.placeholder.com/600x400/1a1a2e/eee?text=<?= urlencode($project['title']) ?>'">
                    <div class="project-card-overlay">
                        <i class="fas fa-expand"></i>
                    </div>
                </div>
                <div class="project-card-content">
                    <h3 class="project-card-title"><?= htmlspecialchars($project['title']) ?></h3>
                    <p class="project-card-description"><?= htmlspecialchars($project['description']) ?></p>
                    
                    <?php if ($project['technologies']): ?>
                    <div class="project-card-tags">
                        <?php foreach (explode(',', $project['technologies']) as $tech): ?>
                        <span class="tag"><?= htmlspecialchars(trim($tech)) ?></span>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        
        <div class="section-cta">
            <a href="<?= SITE_URL ?>/projects" class="btn btn-primary">Voir tous les projets</a>
        </div>
    </div>
</section>

<!-- Project Modal -->
<div id="projectModal" class="project-modal">
    <div class="project-modal-content">
        <button class="modal-close" onclick="closeProjectModal()">
            <i class="fas fa-times"></i>
        </button>
        
        <div class="modal-gallery">
            <div class="gallery-main">
                <button class="gallery-nav gallery-prev" onclick="prevImage()">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <img id="modalMainImage" src="" alt="">
                <button class="gallery-nav gallery-next" onclick="nextImage()">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
            <div class="gallery-thumbnails" id="galleryThumbnails"></div>
        </div>
        
        <div class="modal-details">
            <h2 id="modalTitle"></h2>
            <p id="modalDescription"></p>
            
            <div id="modalTechnologies" class="modal-technologies"></div>
            
            <div id="modalCompetenceBlocks" class="modal-competence-blocks"></div>
            
            <div id="modalLinks" class="modal-links"></div>
        </div>
    </div>
</div>
<?php endif; ?>



<?php require_once BASE_PATH . '/app/Views/partials/footer.php'; ?>

<?php if (!empty($featuredProjects)): ?>
<!-- Project Data for JavaScript (must be after app.js is loaded) -->
<script>
window.projectsData = {
    <?php foreach ($featuredProjects as $project): ?>
    <?= $project['id'] ?>: {
        id: <?= $project['id'] ?>,
        title: <?= json_encode($project['title']) ?>,
        description: <?= json_encode($project['long_description'] ?: $project['description']) ?>,
        image: <?= json_encode($project['image']) ?>,
        images: <?= json_encode(!empty($project['gallery_images']) ? array_map('trim', explode(',', $project['gallery_images'])) : [$project['image']]) ?>,
        technologies: <?= json_encode($project['technologies'] ? array_map('trim', explode(',', $project['technologies'])) : []) ?>,
        url: <?= json_encode($project['url']) ?>,
        github_url: <?= json_encode($project['github_url']) ?>,
        slug: <?= json_encode($project['slug']) ?>,
        competence_blocks: <?= json_encode($project['competence_blocks'] ?? []) ?>
    },
    <?php endforeach; ?>
};
</script>
<?php endif; ?>
