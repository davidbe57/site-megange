<div class="page-header">
    <div class="container">
        <h1>Vie municipale</h1>
        <p>Le conseil municipal, les élus et la vie démocratique de la commune</p>
    </div>
</div>

<div class="content-page">
    <div class="container">
        <div class="content-grid">
            <div class="content-main">
                <h2 id="conseil">Le conseil municipal</h2>
                <p>Le conseil municipal de Mégange est composé d'élus dévoués au service de la commune et de ses habitants. Il se réunit régulièrement pour discuter et voter les décisions qui façonnent l'avenir du village.</p>
                <p>Les séances du conseil municipal sont publiques. Vous êtes invités à y assister pour suivre la vie démocratique de votre commune.</p>

                <h2 id="equipe">L'équipe municipale</h2>
                <div class="team-grid">
                    <?php foreach ($municipal_team as $member): ?>
                    <div class="team-card">
                        <div class="team-avatar"><i class="fas fa-user"></i></div>
                        <h3><?= $member['name'] ?></h3>
                        <p class="role"><?= $member['role'] ?></p>
                        <?php if (!empty($member['delegation'])): ?>
                        <p class="delegation"><?= $member['delegation'] ?></p>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>

                <h2 id="comptes">Les comptes-rendus</h2>
                <p>Consultez les comptes-rendus des conseils municipaux.</p>
                <?php
                $crFile = __DIR__ . '/../data/comptes_rendus.json';
                $crs = file_exists($crFile) ? (json_decode(file_get_contents($crFile), true) ?: []) : [];
                usort($crs, function ($a, $b) { return strcmp($b['date'], $a['date']); });
                $groups = [];
                foreach ($crs as $cr) {
                    $year = substr($cr['date'], 0, 4);
                    $groups[$year][] = $cr;
                }
                if (!empty($groups)):
                ?>
                <div class="cr-list">
                    <?php foreach ($groups as $year => $items): ?>
                    <div class="cr-year">
                        <h3 class="cr-year-title">Année <?= $year ?></h3>
                        <div class="cr-grid">
                            <?php foreach ($items as $cr): ?>
                            <a href="<?= htmlspecialchars($cr['file']) ?>" class="cr-card" target="_blank">
                                <div class="cr-thumb">
                                    <?php if (!empty($cr['thumbnail'])): ?>
                                    <img src="<?= htmlspecialchars($cr['thumbnail']) ?>" alt="" loading="lazy">
                                    <?php else: ?>
                                    <i class="fas fa-file-pdf"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="cr-info">
                                    <span class="cr-date"><?= date('d/m/Y', strtotime($cr['date'])) ?></span>
                                    <span class="cr-title"><?= htmlspecialchars($cr['title']) ?></span>
                                </div>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <p style="color:var(--gray-400);">Aucun compte-rendu publié pour le moment.</p>
                <?php endif; ?>
            </div>

            <aside class="sidebar">
                <div class="sidebar-widget">
                    <h3>Prochain conseil</h3>
                    <p><i class="fas fa-calendar"></i> Prochaine séance : <strong>12 juillet 2026</strong></p>
                    <p><i class="fas fa-clock"></i> 20h00 - Salle du conseil</p>
                </div>

                <div class="sidebar-widget">
                    <h3>Vos élus</h3>
                    <p>Nombre de conseillers : <strong>11</strong></p>
                    <p>Majorité : <strong>Sortie</strong></p>
                    <p>Prochaine élection : <strong>2026</strong></p>
                </div>
            </aside>
        </div>
    </div>
</div>
