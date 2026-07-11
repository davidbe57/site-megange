<div class="page-header">
    <div class="container">
        <h1>La commune</h1>
        <p>Découvrez l'histoire, la géographie et les particularités de Mégange</p>
    </div>
</div>

<div class="content-page">
    <div class="container">
        <div class="content-grid">
            <div class="content-main">
                <h2 id="histoire">Histoire</h2>
                <p>Mégange est un village au riche passé historique, typique des villages lorrains. Son nom évoque l'époque gallo-romaine, témoignant d'une occupation ancienne du territoire. Au fil des siècles, le village s'est développé autour de l'agriculture, conservant son authenticité et son caractère rural.</p>
                <p>Citée dès 1131 comme dépendance de la seigneurie de Vry, la commune se compose de deux villages : Mégange et Rurange-lès-Mégange, réunis par décision royale en 1833. La chapelle de l'Immaculée-Conception, construite en 1860, est un élément marquant du patrimoine local.</p>

                <h2 id="geographie">Géographie</h2>
                <p>Située dans le département de la Moselle, à proximité de Boulay-Moselle, Mégange bénéficie d'un cadre de vie verdoyant et paisible, entouré de champs, de bois et de prairies. La commune fait partie de la Communauté de communes Houve-Pays boulageois, offrant aux habitants l'accès à de nombreux services et infrastructures.</p>

                <h2 id="chiffres">Chiffres clés</h2>
                <ul>
                    <li><strong>Superficie :</strong> environ 5 km²</li>
                    <li><strong>Population :</strong> environ 200 habitants (Mégangeois et Mégangeoises)</li>
                    <li><strong>Altitude :</strong> 205 à 330 mètres</li>
                    <li><strong>Communes limitrophes :</strong> Piblange, Gomelange, Burtoncourt, Éblange, Guinkirchen, Roupeldange</li>
                    <li><strong>Accès :</strong> à 25 km de Metz par la D999, à 5 minutes de Boulay-Moselle</li>
                </ul>

                <h2 id="bulletins">Bulletin communal</h2>
                <p>Consultez les bulletins municipaux d'information.</p>
                <?php
                $bulletinFile = DATA_DIR . '/bulletins.json';
                $bulletins = file_exists($bulletinFile) ? (json_decode(file_get_contents($bulletinFile), true) ?: []) : [];
                usort($bulletins, function ($a, $b) { return strcmp($b['date'], $a['date']); });
                $bgroups = [];
                foreach ($bulletins as $b) {
                    $year = substr($b['date'], 0, 4);
                    $bgroups[$year][] = $b;
                }
                if (!empty($bgroups)):
                ?>
                <div class="cr-list">
                    <?php foreach ($bgroups as $year => $items): ?>
                    <div class="cr-year">
                        <h3 class="cr-year-title">Année <?= $year ?></h3>
                        <div class="cr-grid">
                            <?php foreach ($items as $b): ?>
                            <a href="<?= htmlspecialchars(fileUrl($b['file'])) ?>" class="cr-card<?= fileExists($b['file']) ? '' : ' cr-missing' ?>" target="_blank">
                                <span class="cr-thumb">
                                    <?php if (!empty($b['thumbnail']) && fileExists($b['thumbnail'])): ?>
                                    <img src="<?= htmlspecialchars(fileUrl($b['thumbnail'])) ?>" alt="" loading="lazy">
                                    <?php else: ?>
                                    <i class="fas fa-file-pdf"></i>
                                    <?php endif; ?>
                                </span>
                                <span class="cr-label"><?php
                    $ts2 = strtotime($b['date']);
                    $months2 = ['','JANVIER','FÉVRIER','MARS','AVRIL','MAI','JUIN','JUILLET','AOÛT','SEPTEMBRE','OCTOBRE','NOVEMBRE','DÉCEMBRE'];
                    echo str_pad(date('d', $ts2), 2, '0', STR_PAD_LEFT) . ' ' . $months2[(int)date('m', $ts2)] . ' ' . date('Y', $ts2);
                ?></span>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <p style="color:var(--gray-400);">Aucun bulletin publié pour le moment.</p>
                <?php endif; ?>

                <h2 id="cadre">Cadre de vie</h2>
                <p>À Mégange, la qualité de vie est notre priorité. Le village dispose d'espaces verts entretenus, d'une salle polyvalente pour les animations locales, et de nombreux chemins propices aux balades.</p>
            </div>

            <aside class="sidebar">
                <div class="sidebar-widget">
                    <h3>Mairie</h3>
                    <p><strong><?= $site_address ?></strong></p>
                    <p><i class="fas fa-phone"></i> <?= $site_phone ?></p>
                    <p><i class="fas fa-envelope"></i> <?= $site_email ?></p>
                </div>

                <div class="sidebar-widget">
                    <h3>Horaires d'ouverture</h3>
                    <ul class="hours-list">
                        <?php foreach ($mairie_hours as $day => $hours): ?>
                        <li><span class="day"><?= $day ?></span><span class="hours"><?= $hours ?></span></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </aside>
        </div>
    </div>
</div>
