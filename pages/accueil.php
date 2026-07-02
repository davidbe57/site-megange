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
        <p>Situé au cœur du Pays Messin, Mégange est un charmant village mosellan d'environ 300 habitants. Entre traditions et modernité, notre commune offre un cadre de vie paisible et convivial à ses résidents.</p>
        <div class="hero-actions">
            <a href="index.php?p=la-commune" class="btn btn-primary"><i class="fas fa-tree"></i> Découvrir le village</a>
            <a href="index.php?p=contact" class="btn btn-secondary"><i class="fas fa-envelope"></i> Nous contacter</a>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-header">
            <h2>À la une</h2>
            <p>Les dernières informations et actualités de la commune</p>
        </div>

        <div class="card-grid">
            <div class="card">
                <div class="card-icon"><i class="fas fa-calendar-check"></i></div>
                <h3>Vœux du Maire</h3>
                <p>La cérémonie des vœux aura lieu le 15 janvier à la salle polyvalente. Tous les habitants sont invités à partager ce moment de convivialité.</p>
                <span class="news-date">Janvier 2026</span>
            </div>

            <div class="card">
                <div class="card-icon"><i class="fas fa-leaf"></i></div>
                <h3>Atelier jardinage</h3>
                <p>Un atelier jardinage est organisé le 22 mars pour préparer les jardins partagés du printemps. Inscriptions en mairie.</p>
                <span class="news-date">Mars 2026</span>
            </div>

            <div class="card">
                <div class="card-icon"><i class="fas fa-wrench"></i></div>
                <h3>Travaux de voirie</h3>
                <p>Des travaux de réfection de la rue Principale sont prévus en avril. La circulation sera adaptée pendant la durée du chantier.</p>
                <span class="news-date">Avril 2026</span>
            </div>
        </div>
    </div>
</section>

<section class="section section-dark">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-item">
                <span class="stat-number">~300</span>
                <span class="stat-label">Habitants</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">100%</span>
                <span class="stat-label">Village nature</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">15 min</span>
                <span class="stat-label">De Metz</span>
            </div>
            <div class="stat-item">
                <span class="stat-number">+ 5%</span>
                <span class="stat-label">Croissance douce</span>
            </div>
        </div>
    </div>
</section>

<section class="section section-alt">
    <div class="container">
        <div class="section-header">
            <h2>Vivre à Mégange</h2>
            <p>Un village où il fait bon vivre, entre nature et proximité</p>
        </div>

        <div class="card-grid">
            <div class="card">
                <div class="card-icon"><i class="fas fa-hiking"></i></div>
                <h3>Randonnées</h3>
                <p>Partez à la découverte des sentiers qui traversent notre village et ses environs verdoyants.</p>
            </div>

            <div class="card">
                <div class="card-icon"><i class="fas fa-people-group"></i></div>
                <h3>Associations</h3>
                <p>Une vie associative dynamique qui rassemble les habitants autour d'activités variées.</p>
            </div>

            <div class="card">
                <div class="card-icon"><i class="fas fa-church"></i></div>
                <h3>Patrimoine</h3>
                <p>Notre église Saint-Martin et nos maisons traditionnelles témoignent de notre riche histoire.</p>
            </div>
        </div>
    </div>
</section>
