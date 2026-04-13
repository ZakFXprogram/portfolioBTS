<?php require_once BASE_PATH . '/app/Views/partials/header.php'; ?>

<section class="page-header">
    <div class="container">
        <h1 class="page-title">Uses</h1>
        <p class="page-subtitle">Mon setup de développement et les outils que j'utilise</p>
    </div>
</section>

<section class="section uses-section">
    <div class="container">
        <?php if (empty($toolsByCategory)): ?>
        <p class="no-content">Aucun outil répertorié pour le moment.</p>
        <?php else: ?>
        
        <div class="uses-list">
            <?php foreach ($toolsByCategory as $category => $tools): ?>
            <div class="uses-category">
                <h2 class="uses-category-title"><?= htmlspecialchars($category) ?></h2>
                
                <ul class="uses-items">
                    <?php foreach ($tools as $tool): ?>
                    <li class="uses-item">
                        <div class="uses-item-header">
                            <i class="<?= htmlspecialchars($tool['icon']) ?>"></i>
                            <h3 class="uses-item-name"><?= htmlspecialchars($tool['name']) ?></h3>
                        </div>
                        <p class="uses-item-description"><?= htmlspecialchars($tool['description']) ?></p>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endforeach; ?>
        </div>
        
        <?php endif; ?>
    </div>
</section>

<?php require_once BASE_PATH . '/app/Views/partials/footer.php'; ?>
