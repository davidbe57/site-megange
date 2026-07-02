<?php
$art_file = __DIR__ . '/../data/articles.json';
$articles = file_exists($art_file) ? (json_decode(file_get_contents($art_file), true) ?: []) : [];
$articles = array_reverse($articles);

$associations = [
    ['icon' => 'fa-palette', 'name' => 'Comité des Fêtes', 'desc' => 'Organise les animations tout au long de l\'année : fête patronale, vide-greniers, marchés de Noël et autres événements conviviaux.'],
    ['icon' => 'fa-dice', 'name' => 'Club de l\'Amitié', 'desc' => 'Le club rassemble les aînés du village autour de jeux de société, sorties et goûters partagés. Réunions tous les jeudis après-midi.'],
    ['icon' => 'fa-futbol', 'name' => 'Association Sportive', 'desc' => 'Activités sportives pour tous les âges : football, randonnée, yoga et gymnastique douce.'],
    ['icon' => 'fa-seedling', 'name' => 'Jardins Partagés', 'desc' => 'Un espace de jardinage collectif ouvert à tous les habitants. Parcelles disponibles à la location à l\'année.'],
    ['icon' => 'fa-music', 'name' => 'Société de Musique', 'desc' => 'Notre harmonie municipale anime les cérémonies et fêtes du village. Répétitions le mercredi soir.'],
    ['icon' => 'fa-book', 'name' => 'Bibliothèque', 'desc' => 'La bibliothèque municipale propose un choix de livres pour petits et grands. Ouverte le mercredi et le samedi matin.'],
];
?>
<div class="page-header">
    <div class="container">
        <h1>Vie locale</h1>
        <p>Actualités, associations et événements qui font vivre notre village</p>
    </div>
</div>

<div class="content-page">
    <div class="container">
        <?php if (!empty($articles)): ?>
        <div class="section-header">
            <h2>Le blog de Mégange</h2>
            <p>Toutes les actualités du village</p>
        </div>

        <div class="blog-list">
            <?php foreach ($articles as $art): ?>
            <article class="blog-card">
                <?php if (!empty($art['image'])): ?>
                <div class="blog-card-image">
                    <img src="<?= htmlspecialchars($art['image']) ?>" alt="<?= htmlspecialchars($art['title']) ?>" loading="lazy">
                </div>
                <?php endif; ?>
                <div class="blog-card-body">
                    <div class="blog-meta">
                        <span><i class="fas fa-calendar"></i> <?= date('d/m/Y', strtotime($art['date'])) ?></span>
                        <span><i class="fas fa-user"></i> <?= htmlspecialchars($art['author']) ?></span>
                    </div>
                    <h3><?= htmlspecialchars($art['title']) ?></h3>
                    <p><?= nl2br(htmlspecialchars($art['content'])) ?></p>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="section-header" style="margin-top: <?= empty($articles) ? '0' : '4rem' ?>;">
            <h2>Associations</h2>
            <p>Les associations qui animent notre village</p>
        </div>

        <div class="card-grid">
            <?php foreach ($associations as $asso): ?>
            <div class="card">
                <div class="card-icon"><i class="fas <?= $asso['icon'] ?>"></i></div>
                <h3><?= $asso['name'] ?></h3>
                <p><?= $asso['desc'] ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
