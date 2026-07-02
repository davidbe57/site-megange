<section class="hero" id="hero">
    <div class="carousel-slide active" style="background-image: url('assets/images/hero.jpg');"></div>
    <div class="carousel-slide" style="background-image: url('assets/images/hero-2.jpg');"></div>
    <div class="carousel-slide" style="background-image: url('assets/images/hero-3.jpg');"></div>

    <div class="carousel-dots">
        <button class="carousel-dot active" data-index="0"></button>
        <button class="carousel-dot" data-index="1"></button>
        <button class="carousel-dot" data-index="2"></button>
    </div>

    <div class="container hero-content">
        <h1>Bienvenue à <?= $site_name ?></h1>
        <p>Situé au c&#339;ur du Pays Messin, Mégange est un charmant village mosellan d'environ 300 habitants. Entre traditions et modernité, notre commune offre un cadre de vie paisible et convivial à ses résidents.</p>
        <div class="hero-actions">
            <a href="index.php?p=la-commune" class="btn btn-primary"><i class="fas fa-tree"></i> Découvrir le village</a>
            <a href="index.php?p=contact" class="btn btn-secondary"><i class="fas fa-envelope"></i> Nous contacter</a>
        </div>
    </div>
</section>

<div class="content-page" style="padding-top: 0;">
    <div class="container">
        <div class="home-layout">
            <div class="home-main">
                <article class="home-article home-article--featured">
                    <h2 class="home-article-title">Bienvenue à Mégange</h2>
                    <p>Niché au c&#339;ur du Pays Messin, notre village de 300 habitants allie le charme de la campagne mosellane à la proximité des commodités urbaines. Entre ses sentiers verdoyants, son église Saint-Martin et la convivialité de ses habitants, Mégange est un lieu de vie où il fait bon s'installer.</p>
                    <p>La commune s'engage au quotidien pour améliorer le cadre de vie de ses résidents, préserver son patrimoine et dynamiser la vie locale. Consultez cette page pour suivre l'actualité de votre village.</p>
                </article>

                <?php
                $articles_file = __DIR__ . '/../data/articles.json';
                $articles = [];
                if (file_exists($articles_file)) {
                    $articles = json_decode(file_get_contents($articles_file), true) ?: [];
                }
                ?>

                <?php $recent = array_slice($articles, 0, 3); ?>
                <?php if (!empty($recent)): ?>
                <section class="home-section">
                    <h2 class="home-section-title">Actualités</h2>
                    <?php foreach ($recent as $a): ?>
                    <article class="home-actu">
                        <div class="home-actu-meta">
                            <span class="home-actu-date"><?= date('d/m/Y', strtotime($a['date'])) ?></span>
                            <?php if (!empty($a['author'])): ?>
                            <span class="home-actu-author"><?= htmlspecialchars($a['author']) ?></span>
                            <?php endif; ?>
                        </div>
                        <h3 class="home-actu-title"><?= htmlspecialchars($a['title']) ?></h3>
                        <?php if (!empty($a['excerpt'])): ?>
                        <p class="home-actu-excerpt"><?= htmlspecialchars($a['excerpt']) ?></p>
                        <?php endif; ?>
                        <a href="index.php?p=vie-locale" class="home-actu-link">Lire la suite &rarr;</a>
                    </article>
                    <?php endforeach; ?>
                    <div class="home-section-footer">
                        <a href="index.php?p=vie-locale" class="btn btn-outline">Toutes les actualités</a>
                    </div>
                </section>
                <?php endif; ?>

                <section class="home-section">
                    <h2 class="home-section-title">Vivre à Mégange</h2>
                    <div class="home-links-grid">
                        <a href="index.php?p=services" class="home-link-card">
                            <i class="fas fa-hand-holding-heart"></i>
                            <span>Services</span>
                        </a>
                        <a href="index.php?p=vie-municipale" class="home-link-card">
                            <i class="fas fa-landmark"></i>
                            <span>Vie municipale</span>
                        </a>
                        <a href="index.php?p=galerie" class="home-link-card">
                            <i class="fas fa-images"></i>
                            <span>Galerie photos</span>
                        </a>
                        <a href="index.php?p=contact" class="home-link-card">
                            <i class="fas fa-envelope"></i>
                            <span>Contact</span>
                        </a>
                    </div>
                </section>
            </div>

            <aside class="home-sidebar">
                <div class="sidebar-widget">
                    <h3><i class="fas fa-bell" style="color: var(--terracotta);"></i> Alertes PanneauPocket</h3>
                    <iframe src="<?= htmlspecialchars($panneaupocket_widget_url) ?>" height="415" width="240" frameborder="0" style="max-width:100%;border-radius:var(--radius-sm);display:block;margin:0 auto;" loading="lazy"></iframe>
                    <div style="text-align:center;margin-top:0.75rem;">
                        <a href="index.php?p=alertes" style="font-size:0.85rem;color:var(--terracotta);">Voir toutes les alertes &rarr;</a>
                    </div>
                </div>

                <div class="sidebar-widget">
                    <h3><i class="fas fa-clock"></i> Horaires d'ouverture</h3>
                    <ul class="hours-list">
                        <?php foreach ($mairie_hours as $day => $hours): ?>
                        <li><span class="day"><?= $day ?></span><span class="hours"><?= $hours ?></span></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="sidebar-widget">
                    <h3><i class="fas fa-phone"></i> Contact</h3>
                    <address style="font-style:normal;font-size:0.9rem;color:var(--text);line-height:1.8;">
                        <p><i class="fas fa-location-dot" style="width:1.2rem;color:var(--terracotta);"></i> <?= $site_address ?></p>
                        <p><i class="fas fa-phone" style="width:1.2rem;color:var(--terracotta);"></i> <a href="tel:<?= $site_phone ?>"><?= $site_phone ?></a></p>
                        <p><i class="fas fa-envelope" style="width:1.2rem;color:var(--terracotta);"></i> <a href="mailto:<?= $site_email ?>"><?= $site_email ?></a></p>
                    </address>
                </div>
            </aside>
        </div>
    </div>
</div>
