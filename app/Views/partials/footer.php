    </main>

    <!-- Image Lightbox -->
    <div id="imageLightbox" class="image-lightbox">
        <button class="lightbox-close" onclick="closeLightbox()">
            <i class="fas fa-times"></i>
        </button>
        <button class="lightbox-nav lightbox-prev" onclick="lightboxPrev()">
            <i class="fas fa-chevron-left"></i>
        </button>
        <div class="lightbox-content">
            <img id="lightboxImage" src="" alt="Image agrandie">
        </div>
        <button class="lightbox-nav lightbox-next" onclick="lightboxNext()">
            <i class="fas fa-chevron-right"></i>
        </button>
        <div class="lightbox-counter">
            <span id="lightboxCounter">1 / 1</span>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <p class="footer-copyright">© <?= date('Y') ?> <?= htmlspecialchars($profile['full_name'] ?? SITE_NAME) ?></p>
                
                <ul class="footer-links">
                    <li><a href="<?= SITE_URL ?>">Accueil</a></li>
                    <li><a href="<?= SITE_URL ?>/blog">Veille</a></li>
                    <li><a href="<?= SITE_URL ?>/projects">Projets</a></li>
                    <li><a href="<?= SITE_URL ?>/resume">Parcours</a></li>
                    <li><a href="<?= SITE_URL ?>/tools">Outils</a></li>
                </ul>

                <div class="footer-social">
                    <?php foreach ($socialLinks as $social): ?>
                    <a href="<?= htmlspecialchars($social['url']) ?>" target="_blank" rel="noopener noreferrer" title="<?= htmlspecialchars($social['name']) ?>">
                        <i class="<?= htmlspecialchars($social['icon']) ?>"></i>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="<?= ASSETS_PATH ?>/js/app.js"></script>
</body>
</html>
