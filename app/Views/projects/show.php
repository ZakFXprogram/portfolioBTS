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
        
        <?php
            $galleryImages = [];
            if (!empty($project['gallery_images'])) {
                $galleryImages = array_values(array_filter(array_map('trim', explode(',', $project['gallery_images']))));
            }
            // Image principale en premier, en évitant les doublons
            $mainImage = trim((string)($project['image'] ?? ''));
            $allImages = $mainImage !== '' ? [$mainImage] : [];
            foreach ($galleryImages as $g) {
                if ($g !== '' && !in_array($g, $allImages, true)) {
                    $allImages[] = $g;
                }
            }
        ?>
        <div class="project-detail-image">
            <?php if (!empty($allImages)): ?>
            <div class="project-carousel" data-current="0">
                <?php foreach ($allImages as $i => $img): ?>
                <img class="project-carousel-img<?= $i === 0 ? ' is-active' : '' ?>"
                     src="<?= ASSETS_PATH ?>/images/projects/<?= htmlspecialchars($img) ?>"
                     alt="<?= htmlspecialchars($project['title']) ?>"
                     <?= $i === 0 ? '' : 'loading="lazy"' ?>
                     onerror="this.src='https://via.placeholder.com/1200x600/1a1a2e/eee?text=<?= urlencode($project['title']) ?>'">
                <?php endforeach; ?>
                <?php if (count($allImages) > 1): ?>
                <button type="button" class="project-carousel-arrow project-carousel-prev" aria-label="Image précédente">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button type="button" class="project-carousel-arrow project-carousel-next" aria-label="Image suivante">
                    <i class="fas fa-chevron-right"></i>
                </button>
                <div class="project-carousel-counter">
                    <span class="project-carousel-current">1</span> / <?= count($allImages) ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
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
                <p class="competences-table-help">
                    <i class="fas fa-info-circle"></i>
                    Cliquez sur un <i class="fas fa-check-circle" style="color:#22c55e;"></i> pour voir le <strong>Comment</strong> et le <strong>Pourquoi</strong> de la sous-compétence validée.
                </p>
                <?php
                // Indexer toutes les sous-compétences par bloc
                $subsByBlock = [];
                foreach ($project['all_sub_competences'] as $sc) {
                    $subsByBlock[$sc['competence_block_id']][] = $sc;
                }
                $validatedMap = $project['validated_sub_competences'];

                // Calculer le nombre maximum de sous-compétences pour les colonnes
                $maxSubs = 0;
                foreach ($project['competence_blocks'] as $block) {
                    $count = count($subsByBlock[$block['id']] ?? []);
                    if ($count > $maxSubs) { $maxSubs = $count; }
                }
                ?>
                <?php if ($maxSubs > 0): ?>
                <div class="competences-matrix-wrapper">
                    <table class="competences-matrix">
                        <thead>
                            <tr>
                                <th class="competences-matrix-corner">Compétences mises en œuvre</th>
                                <th class="competences-matrix-sc-head">Sous-compétences validées dans ce projet</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($project['competence_blocks'] as $block):
                            $blockSubs = $subsByBlock[$block['id']] ?? [];
                            if (empty($blockSubs)) continue;
                            $color = htmlspecialchars($block['color'] ?? '#f97316');
                        ?>
                            <tr>
                                <th class="competences-matrix-block" style="border-left-color: <?= $color ?>;">
                                    <span class="competences-matrix-block-name"><?= htmlspecialchars($block['name']) ?></span>
                                </th>
                                <td class="competences-matrix-cell">
                                    <ul class="competences-sc-list">
                                    <?php foreach ($blockSubs as $sc):
                                        $scId = (int)$sc['id'];
                                        $isValidated = isset($validatedMap[$scId]);
                                        $just = $validatedMap[$scId] ?? null;
                                    ?>
                                        <li class="competences-sc-item <?= $isValidated ? 'is-validated' : 'not-validated' ?>">
                                            <?php if ($isValidated): ?>
                                                <button type="button"
                                                        class="competence-check"
                                                        aria-label="Voir la justification de <?= htmlspecialchars($sc['name'], ENT_QUOTES) ?>"
                                                        data-sc-name="<?= htmlspecialchars($sc['name'], ENT_QUOTES) ?>"
                                                        data-block-name="<?= htmlspecialchars($block['name'], ENT_QUOTES) ?>"
                                                        data-comment="<?= htmlspecialchars($just['comment'] ?? '', ENT_QUOTES) ?>"
                                                        data-pourquoi="<?= htmlspecialchars($just['pourquoi'] ?? '', ENT_QUOTES) ?>">
                                                    <i class="fas fa-check-circle"></i>
                                                </button>
                                            <?php else: ?>
                                                <span class="competence-uncheck" aria-label="Non validée">
                                                    <i class="fas fa-minus-circle"></i>
                                                </span>
                                            <?php endif; ?>
                                            <span class="competences-sc-name"><?= htmlspecialchars($sc['name']) ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                    </ul>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>

            <!-- Popup de justification -->
            <div id="competencePopup" class="competence-popup" role="dialog" aria-modal="true" aria-labelledby="competencePopupTitle" hidden>
                <div class="competence-popup-backdrop" data-close-popup></div>
                <div class="competence-popup-content" role="document">
                    <button type="button" class="competence-popup-close" aria-label="Fermer" data-close-popup>&times;</button>
                    <p class="competence-popup-block" id="competencePopupBlock"></p>
                    <h4 class="competence-popup-title" id="competencePopupTitle"></h4>
                    <div class="competence-popup-section">
                        <h5><i class="fas fa-cogs"></i> Comment</h5>
                        <p id="competencePopupComment"></p>
                    </div>
                    <div class="competence-popup-section">
                        <h5><i class="fas fa-lightbulb"></i> Pourquoi</h5>
                        <p id="competencePopupPourquoi"></p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once BASE_PATH . '/app/Views/partials/footer.php'; ?>
