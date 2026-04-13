<?php require_once BASE_PATH . '/app/Views/partials/header.php'; ?>

<section class="error-section">
    <div class="container">
        <div class="error-content">
            <h1 class="error-code">404</h1>
            <h2 class="error-title">Page non trouvée</h2>
            <p class="error-description">La page que vous recherchez n'existe pas ou a été déplacée.</p>
            <a href="<?= SITE_URL ?>" class="btn btn-primary">Retour à l'accueil</a>
        </div>
    </div>
</section>

<?php require_once BASE_PATH . '/app/Views/partials/footer.php'; ?>
