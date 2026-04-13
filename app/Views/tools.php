<?php require_once BASE_PATH . '/app/Views/partials/header.php'; ?>

<section class="page-header">
    <div class="container">
        <h1 class="page-title">Outils</h1>
        <p class="page-subtitle">Les outils et technologies que j'utilise au quotidien</p>
    </div>
</section>

<section class="section tools-section">
    <div class="container">
        <?php if (empty($toolsByCategory)): ?>
        <p class="no-content">Aucun outil répertorié pour le moment.</p>
        <?php else: ?>
        
        <?php foreach ($toolsByCategory as $category => $tools): ?>
        <div class="tools-category">
            <h2 class="tools-category-title"><?= htmlspecialchars($category) ?></h2>
            
            <div class="tools-grid">
                <?php foreach ($tools as $tool): ?>
                <div class="tool-card">
                    <div class="tool-card-icon">
                        <i class="<?= htmlspecialchars($tool['icon']) ?>"></i>
                    </div>
                    <div class="tool-card-content">
                        <h3 class="tool-card-name"><?= htmlspecialchars($tool['name']) ?></h3>
                        <p class="tool-card-description"><?= htmlspecialchars($tool['description']) ?></p>
                        <?php if ($tool['url']): ?>
                        <a href="<?= htmlspecialchars($tool['url']) ?>" target="_blank" rel="noopener noreferrer" class="tool-card-link">
                            Visiter <i class="fas fa-external-link-alt"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
        
        <?php endif; ?>
    </div>
</section>

<?php require_once BASE_PATH . '/app/Views/partials/footer.php'; ?>
