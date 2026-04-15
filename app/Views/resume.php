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
                    
                    <?php if (!empty($exp['competence_blocks'])): ?>
                    <button type="button" class="btn btn-competences-exp" onclick="openExpCompetences(<?= $exp['id'] ?>)">
                        <i class="fas fa-graduation-cap"></i> Voir les compétences
                    </button>
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

<!-- Modal Compétences Expérience -->
<div id="expCompetencesModal" class="exp-comp-modal">
    <div class="exp-comp-modal-content">
        <button class="exp-comp-modal-close" onclick="closeExpCompetences()">&times;</button>
        <h3 id="expCompTitle" class="exp-comp-modal-title"></h3>
        <div id="expCompBody" class="exp-comp-modal-body"></div>
    </div>
</div>

<script>
var expCompetencesData = <?php
    $expData = [];
    foreach ($experiences as $exp) {
        if (empty($exp['competence_blocks'])) continue;
        $grouped = [];
        foreach ($exp['sub_competences'] as $sc) {
            $grouped[$sc['block_id']][] = $sc;
        }
        $expData[$exp['id']] = [
            'title' => $exp['company'] . ' — ' . $exp['position'],
            'blocks' => $exp['competence_blocks'],
            'grouped' => $grouped
        ];
    }
    echo json_encode($expData);
?>;

function openExpCompetences(expId) {
    var data = expCompetencesData[expId];
    if (!data) return;
    
    document.getElementById('expCompTitle').textContent = data.title;
    var body = document.getElementById('expCompBody');
    var html = '';
    
    data.blocks.forEach(function(block) {
        html += '<div class="sc-group">';
        html += '<h4 class="sc-group-title">' + escapeHtml(block.name) + '</h4>';
        var scs = data.grouped[block.id] || [];
        if (scs.length > 0) {
            html += '<ul class="sc-items-list">';
            scs.forEach(function(sc) {
                html += '<li class="sc-item-entry"><span class="sc-item-name">' + escapeHtml(sc.sc_name) + '</span>';
                if (sc.sc_justification) {
                    html += '<p class="sc-item-justification">' + escapeHtml(sc.sc_justification).replace(/\n/g, '<br>') + '</p>';
                }
                html += '</li>';
            });
            html += '</ul>';
        }
        html += '</div>';
    });
    
    body.innerHTML = html;
    document.getElementById('expCompetencesModal').classList.add('active');
}

function closeExpCompetences() {
    document.getElementById('expCompetencesModal').classList.remove('active');
}

function escapeHtml(text) {
    var div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

document.getElementById('expCompetencesModal').addEventListener('click', function(e) {
    if (e.target === this) closeExpCompetences();
});
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeExpCompetences();
});
</script>

<?php require_once BASE_PATH . '/app/Views/partials/footer.php'; ?>
