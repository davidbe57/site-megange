<?php
$carouselFile = DATA_DIR . '/carousel.json';
$carouselItems = file_exists($carouselFile) ? (json_decode(file_get_contents($carouselFile), true) ?: []) : [];
$defaultHeroes = ['assets/images/hero.jpg', 'assets/images/hero-2.jpg', 'assets/images/hero-3.jpg'];
if (empty($carouselItems)) {
    foreach ($defaultHeroes as $i => $src) $carouselItems[] = ['image' => $src];
}
?>
<section class="hero" id="hero" aria-roledescription="carousel" aria-label="Galerie photos de Mégange">
    <?php foreach ($carouselItems as $i => $item): ?>
    <div class="carousel-slide<?= $i === 0 ? ' active' : '' ?>" style="background-image: url('<?= htmlspecialchars(fileUrl($item['image'])) ?>');" role="group" aria-roledescription="slide" aria-label="Photo <?= $i + 1 ?> sur <?= count($carouselItems) ?>"></div>
    <?php endforeach; ?>

    <div class="carousel-dots" aria-label="Choisir une photo">
        <?php foreach ($carouselItems as $i => $item): ?>
        <button class="carousel-dot<?= $i === 0 ? ' active' : '' ?>" data-index="<?= $i ?>" aria-label="Photo <?= $i + 1 ?>"></button>
        <?php endforeach; ?>
    </div>

    <div class="container hero-content">
        <h1>Bienvenue à <?= $site_name ?><br><span style="font-size:0.65em;font-weight:400;">et Rurange-lès-Mégange</span></h1>
        <p>Mégange est un charmant village mosellan, où convivialité et qualité de vie sont au cœur de notre identité. La commune réunit deux villages : <strong>Mégange</strong> et <strong>Rurange-lès-Mégange</strong>. Entre traditions et modernité, notre commune offre un cadre de vie paisible à ses résidents.</p>
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
                <?php
                $articles_file = DATA_DIR . '/articles.json';
                $articles = [];
                if (file_exists($articles_file)) {
                    $articles = json_decode(file_get_contents($articles_file), true) ?: [];
                }
                ?>

                <?php usort($articles, function($a, $b) { return strcmp($b['date'] ?? '', $a['date'] ?? ''); }); $recent = array_slice($articles, 0, 3); ?>
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
                        <p class="home-actu-excerpt"><?= htmlspecialchars(strip_tags($a['excerpt'])) ?></p>
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
                        <a href="index.php?p=la-commune#dechetterie" class="home-link-card">
                            <i class="fas fa-hand-holding-heart"></i>
                            <span>Déchetterie</span>
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
                        <a href="index.php?p=vie-locale" class="home-link-card">
                            <i class="fas fa-newspaper"></i>
                            <span>Actualités</span>
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
                        <?php foreach ($mairie_hours as $day => $slots): if (empty($slots)) continue; ?>
                        <li><span class="day"><?= $day ?></span><span class="hours"><?= implode('<br>', $slots) ?></span></li>
                        <?php endforeach; ?>
                    </ul>
                </div>


            </aside>
        </div>
    </div>
</div>
