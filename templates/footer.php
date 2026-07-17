    </main>

    <footer class="site-footer" role="contentinfo">
        <div class="container footer-grid">
            <div class="footer-col hide-mobile">
                <h3><i class="fas fa-tree"></i> Mégange</h3>
                <p>Bienvenue dans notre charmant village mosellan, où la convivialité et la qualité de vie sont au cœur de notre identité.</p>
            </div>

            <div class="footer-col">
                <h3>Contact</h3>
                <address>
                    <p><i class="fas fa-location-dot"></i> <?= $site_address ?></p>
                    <p><i class="fas fa-phone"></i> <a href="tel:<?= $site_phone ?>"><?= $site_phone ?></a></p>
                    <p><i class="fas fa-envelope"></i> <a href="mailto:<?= $site_email ?>"><?= $site_email ?></a></p>
                </address>
                <div class="social-links">
                    <?php if (!empty($social['facebook']) && $social['facebook'] !== '#'): ?>
                    <a href="<?= $social['facebook'] ?>" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($social['youtube']) && $social['youtube'] !== '#'): ?>
                    <a href="<?= $social['youtube'] ?>" target="_blank" rel="noopener" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="container">
                <p>&copy; <?= date('Y') ?> Commune de <?= $site_name ?> | 
                <a href="index.php?p=mentions-legales">Mentions légales</a> | 
                <a href="index.php?p=contact">Contact</a> | 
                <a href="index.php?p=webmaster">Contacter le webmaster</a></p>
            </div>
        </div>
    </footer>

    <button class="back-to-top" aria-label="Retour en haut"><i class="fas fa-arrow-up"></i></button>

    <div class="side-actions">
        <button class="side-btn" id="mapBtn" aria-label="Voir la carte" title="Carte">
            <i class="fas fa-map-marked-alt"></i>
            <span class="side-btn-label">Carte</span>
        </button>
        <button class="side-btn" id="weatherBtn" aria-label="Voir la météo" title="Météo">
            <i class="fas fa-cloud-sun"></i>
            <span class="side-btn-label">Météo</span>
        </button>
    </div>

    <!-- Map Modal -->
    <div class="modal" id="mapModal" role="dialog" aria-modal="true" aria-label="Carte de Mégange">
        <div class="modal-overlay"></div>
        <div class="modal-content modal-content--large">
            <button class="modal-close" aria-label="Fermer">&times;</button>
            <h3><i class="fas fa-map-marked-alt"></i> Mairie de Mégange</h3>
            <p style="font-size:0.9rem;color:var(--gray-400);margin-bottom:1rem;">25 rue Principale, 57220 Mégange</p>
            <div id="map" style="height:400px;border-radius:var(--radius-sm);"></div>
        </div>
    </div>

    <!-- Weather Modal -->
    <div class="modal" id="weatherModal" role="dialog" aria-modal="true" aria-label="Météo à Mégange">
        <div class="modal-overlay"></div>
        <div class="modal-content">
            <button class="modal-close" aria-label="Fermer">&times;</button>
            <h3><i class="fas fa-cloud-sun"></i> Météo à Mégange</h3>
            <div id="weatherContent" style="text-align:center;padding:1.5rem 0;">
                <p style="color:var(--gray-400);"><i class="fas fa-spinner fa-spin"></i> Chargement…</p>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" id="leaflet-css">
    <script src="assets/js/main.js"></script>
</body>
</html>
