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
                <p>Citée dès 1131 comme dépendance de la seigneurie de Vry, la commune se compose en réalité de <strong>deux villages</strong> : <strong>Mégange</strong>, le village principal où se situe la mairie, et <strong>Rurange-lès-Mégange</strong>, réunis par décision royale en 1833. La chapelle de l'Immaculée-Conception, construite en 1860, est un élément marquant du patrimoine local.</p>
                <p>Située dans le département de la Moselle, à proximité de Boulay-Moselle, Mégange bénéficie d'un cadre de vie verdoyant et paisible, entouré de champs, de bois et de prairies. La commune fait partie de la Communauté de communes Houve-Pays boulageois. Sa superficie est d'environ 5 km², pour une population d'environ 200 habitants, à une altitude de 205 à 330 mètres.</p>

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
                            <a href="<?= htmlspecialchars(fileUrl($b['file'])) ?>" class="cr-card<?= fileExists($b['file']) ? '' : ' cr-missing' ?>" target="_blank" title="Ouvrir le bulletin (PDF)">
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

                <h2 id="dechetterie">Déchetterie</h2>
                <p>La commune de Mégange met à disposition de ses habitants un service de déchetterie. Renseignez-vous en mairie pour connaître les modalités d'accès, les horaires d'ouverture et les types de déchets acceptés.</p>
                <p>Pour plus d'informations, contactez la Communauté de communes Houve-Pays boulageois.</p>

                <h2 id="ordures">Ordures ménagères</h2>
                <p>Les jours de collecte des ordures ménagères sont communiqués par la Communauté de communes. Des bacs de tri sélectif sont également à disposition pour le verre, le papier et les emballages recyclables.</p>
                <p>Consultez le calendrier de collecte ou contactez la mairie pour toute question relative à la gestion des déchets.</p>

                <h2 id="location-salle">Location de salle</h2>
                <p>La commune dispose d'une salle polyvalente pouvant être louée pour vos événements familiaux ou associatifs (anniversaires, réunions, fêtes).</p>
                <p>Pour tout renseignement sur les disponibilités, les tarifs et les conditions de location, veuillez contacter la mairie.</p>
                <p><i class="fas fa-phone"></i> <?= $site_phone ?><br>
                <i class="fas fa-envelope"></i> <a href="mailto:<?= $site_email ?>"><?= $site_email ?></a></p>
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
                        <?php foreach ($mairie_hours as $day => $slots): if (empty($slots)) continue; ?>
                        <li><span class="day"><?= $day ?></span><span class="hours"><?= implode('<br>', $slots) ?></span></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </aside>
        </div>
    </div>
</div>
