<?php require_once BASE_PATH . '/app/Views/partials/header.php'; ?>

<section class="page-header">
    <div class="container">
        <a href="<?= SITE_URL ?>/blog" class="back-link"><i class="fas fa-arrow-left"></i> Retour à la veille</a>
        <h1 class="page-title"><?= htmlspecialchars($post['title']) ?></h1>
        <p class="page-subtitle">
            <?= !empty($post['published_at']) ? date('d/m/Y H:i', strtotime($post['published_at'])) : 'Non publié' ?>
        </p>
    </div>
</section>

<section class="section">
    <div class="container" style="max-width: 900px;">
        <?php if (!empty($post['image'])): ?>
        <div style="margin-bottom: 24px; border-radius: 12px; overflow: hidden; border: 1px solid var(--border-color);">
            <img src="<?= ASSETS_PATH ?>/images/posts/<?= htmlspecialchars($post['image']) ?>"
                 alt="<?= htmlspecialchars($post['title']) ?>"
                 style="width: 100%; display: block;"
                 onerror="this.style.display='none'">
        </div>
        <?php endif; ?>

        <?php if (!empty($post['excerpt'])): ?>
        <p style="color: var(--text-secondary); font-size: 1.05rem; margin-bottom: 24px;"><?= htmlspecialchars($post['excerpt']) ?></p>
        <?php endif; ?>

        <article style="line-height: 1.8; color: var(--text-primary);">
            <?= nl2br(htmlspecialchars($post['content'] ?? '')) ?>
        </article>
    </div>
</section>

<?php require_once BASE_PATH . '/app/Views/partials/footer.php'; ?>
