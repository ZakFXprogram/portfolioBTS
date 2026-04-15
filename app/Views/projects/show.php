<?php require_once BASE_PATH . '/app/Views/partials/header.php'; ?>

<section class="page-header">
    <div class="container">
        <a href="<?= SITE_URL ?>/projects" class="back-link">
            <i class="fas fa-arrow-left"></i> Retour aux projets
        </a>
    </div>
</section>

<section class="section project-detail">
    <div class="container">
        <div class="project-detail-header">
            <h1 class="project-detail-title"><?= htmlspecialchars($project['title']) ?></h1>
            
            <div class="project-detail-links">
                <?php if ($project['url']): ?>
                <a href="<?= htmlspecialchars($project['url']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-primary">
                    <i class="fas fa-external-link-alt"></i> Visiter le site
                </a>
                <?php endif; ?>
                
                <?php if ($project['github_url']): ?>
                <a href="<?= htmlspecialchars($project['github_url']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-secondary">
                    <i class="fab fa-github"></i> Voir sur GitHub
                </a>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="project-detail-image">
            <img src="<?= ASSETS_PATH ?>/images/projects/<?= htmlspecialchars($project['image']) ?>" 
                 alt="<?= htmlspecialchars($project['title']) ?>"
                 onerror="this.src='https://via.placeholder.com/1200x600/1a1a2e/eee?text=<?= urlencode($project['title']) ?>'">
        </div>
        
        <div class="project-detail-content">
            <div class="project-detail-description">
                <?= nl2br(htmlspecialchars($project['long_description'] ?? $project['description'])) ?>
            </div>
            
            <?php if ($project['technologies']): ?>
            <div class="project-detail-technologies">
                <h3>Technologies utilisées</h3>
                <div class="tags-list">
                    <?php foreach (explode(',', $project['technologies']) as $tech): ?>
                    <span class="tag tag-large"><?= htmlspecialchars(trim($tech)) ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($project['competence_blocks'])): ?>
            <div class="project-detail-sub-competences">
                <h3>Compétences mobilisées</h3>
                <?php 
                // Grouper les sous-compétences par block_id
                $groupedSc = [];
                foreach ($project['sub_competences'] as $sc) {
                    $groupedSc[$sc['block_id']][] = $sc;
                }
                // Afficher par bloc avec justification par sous-compétence
                foreach ($project['competence_blocks'] as $block): 
                    $blockScs = $groupedSc[$block['id']] ?? [];
                    if (empty($blockScs)) continue;
                ?>
                <div class="sc-group">
                    <h4 class="sc-group-title"><?= htmlspecialchars($block['name']) ?></h4>
                    <ul class="sc-items-list">
                        <?php foreach ($blockScs as $sc): ?>
                        <li class="sc-item-entry">
                            <span class="sc-item-name"><?= htmlspecialchars($sc['sc_name']) ?></span>
                            <?php if (!empty($sc['sc_justification'])): ?>
                            <p class="sc-item-justification"><?= nl2br(htmlspecialchars($sc['sc_justification'])) ?></p>
                            <?php endif; ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once BASE_PATH . '/app/Views/partials/footer.php'; ?>
