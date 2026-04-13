<?php require_once BASE_PATH . '/app/Views/partials/header.php'; ?>

<section class="page-header resume-header">
    <div class="container">
        <div class="resume-header-content">
            <h1 class="page-title"><?= htmlspecialchars($profile['title'] ?? 'Full-Stack Developer') ?></h1>
            <a href="<?= SITE_URL ?>/resume/download" class="btn btn-primary btn-download">
                <i class="fas fa-download"></i> Download PDF
            </a>
        </div>
    </div>
</section>

<section class="section resume-section">
    <div class="container">
        <!-- Summary -->
        <div class="resume-block">
            <h2 class="resume-block-title">Résumé</h2>
            <p class="resume-summary">
                <?= nl2br(htmlspecialchars($profile['bio'] ?? '')) ?>
            </p>
        </div>

        <!-- Experience -->
        <div class="resume-block">
            <h2 class="resume-block-title">Experience</h2>
            
            <div class="experiences-list">
                <?php foreach ($experiences as $exp): ?>
                <article class="experience-item">
                    <div class="experience-header">
                        <div class="experience-info">
                            <h3 class="experience-company"><?= htmlspecialchars($exp['company']) ?></h3>
                            <p class="experience-position"><?= htmlspecialchars($exp['position']) ?></p>
                        </div>
                        <div class="experience-meta">
                            <span class="experience-type"><?= htmlspecialchars($exp['type']) ?></span>
                            <span class="experience-dates">
                                <?= date('M Y', strtotime($exp['start_date'])) ?> - 
                                <?= $exp['is_current'] ? 'Present' : date('M Y', strtotime($exp['end_date'])) ?>
                            </span>
                        </div>
                    </div>
                    
                    <?php if ($exp['description'] || $exp['responsibilities']): ?>
                    <div class="experience-content">
                        <?php if ($exp['description']): ?>
                        <p class="experience-description"><?= htmlspecialchars($exp['description']) ?></p>
                        <?php endif; ?>
                        
                        <?php if ($exp['responsibilities']): ?>
                        <ul class="experience-responsibilities">
                            <?php foreach (explode("\n", $exp['responsibilities']) as $resp): ?>
                            <?php if (trim($resp)): ?>
                            <li><?= htmlspecialchars(trim($resp)) ?></li>
                            <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </article>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Skills -->
        <?php if (!empty($skillsByCategory)): ?>
        <div class="resume-block">
            <h2 class="resume-block-title">Skills</h2>
            
            <div class="skills-categories">
                <?php foreach ($skillsByCategory as $category => $skills): ?>
                <div class="skill-category">
                    <h3 class="skill-category-title"><?= htmlspecialchars($category) ?></h3>
                    <div class="skill-category-items">
                        <?php foreach ($skills as $skill): ?>
                        <div class="skill-badge">
                            <i class="<?= htmlspecialchars($skill['icon']) ?>"></i>
                            <span><?= htmlspecialchars($skill['name']) ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once BASE_PATH . '/app/Views/partials/footer.php'; ?>
