    </main>

    <footer class="site-footer" role="contentinfo">
        <div class="container footer-grid">
            <div class="footer-col">
                <h3><i class="fas fa-tree"></i> Mégange</h3>
                <p>Bienvenue dans notre charmant village mosellan, où la convivialité et la qualité de vie sont au cœur de notre identité.</p>
            </div>

            <div class="footer-col">
                <h3>Horaires d'ouverture</h3>
                <ul class="hours-list">
                    <?php foreach ($mairie_hours as $day => $hours): ?>
                    <li><span class="day"><?= $day ?></span><span class="hours"><?= $hours ?></span></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="footer-col">
                <h3>Contact</h3>
                <address>
                    <p><i class="fas fa-location-dot"></i> <?= $site_address ?></p>
                    <p><i class="fas fa-phone"></i> <a href="tel:<?= $site_phone ?>"><?= $site_phone ?></a></p>
                    <p><i class="fas fa-envelope"></i> <a href="mailto:<?= $site_email ?>"><?= $site_email ?></a></p>
                </address>
                <div class="social-links">
                    <?php if (!empty($social['facebook'])): ?>
                    <a href="<?= $social['facebook'] ?>" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($social['youtube'])): ?>
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
                <a href="admin/index.php">Admin</a></p>
            </div>
        </div>
    </footer>

    <button class="back-to-top" aria-label="Retour en haut"><i class="fas fa-arrow-up"></i></button>

    <script src="assets/js/main.js"></script>
</body>
</html>
