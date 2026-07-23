<?php
$conseilFile = DATA_DIR . '/conseil.json';
$conseilData = file_exists($conseilFile) ? (json_decode(file_get_contents($conseilFile), true) ?: []) : [];
$elusFile = DATA_DIR . '/elus.json';
$elusData = file_exists($elusFile) ? (json_decode(file_get_contents($elusFile), true) ?: []) : [];
if (empty($elusData)) $elusData = $municipal_team ?? [];
$months = ['','janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre'];
$ts = strtotime($conseilData['next_date'] ?? '');
$dateStr = $ts ? date('d', $ts) . ' ' . $months[(int)date('m', $ts)] . ' ' . date('Y', $ts) : 'À venir';
?>
<div class="page-header">
    <div class="container">
        <h1>Vie municipale</h1>
        <p>Le conseil municipal, les élus et la vie démocratique de la commune</p>
    </div>
</div>

<div class="content-page">
    <div class="container">
        <div class="content-grid vie-grid">

            <section class="vie-section section-conseil">
                <h2 id="conseil">Le conseil municipal</h2>
                <?php if (!empty($conseilData['photo'])): ?>
                <div style="margin-bottom:1rem;"><img src="<?= htmlspecialchars(fileUrl($conseilData['photo'])) ?>" alt="Photo du conseil municipal" style="width:100%;max-width:500px;border-radius:var(--radius);box-shadow:var(--shadow-md);"></div>
                <?php endif; ?>
                <p>Le conseil municipal de Mégange est composé d'élus dévoués au service de la commune et de ses habitants. Il se réunit régulièrement pour discuter et voter les décisions qui façonnent l'avenir du village.</p>
                <p>Les séances du conseil municipal sont publiques. Vous êtes invités à y assister pour suivre la vie démocratique de votre commune.</p>
            </section>

            <section class="vie-section section-prochain sidebar-widget">
                <h3>Prochain conseil</h3>
                <p><i class="fas fa-calendar"></i> Prochaine séance : <strong><?= $dateStr ?></strong></p>
                <p><i class="fas fa-clock"></i> <?= htmlspecialchars($conseilData['next_time'] ?? '20h00') ?> - <?= htmlspecialchars($conseilData['next_location'] ?? 'Salle du conseil') ?></p>
            </section>

            <section class="vie-section section-elus sidebar-widget">
                <h3>Vos élus</h3>
                <p>Nombre de conseillers : <strong><?= (int)($conseilData['councilors'] ?? 11) ?></strong></p>
                <p>Prochaine élection : <strong><?= (int)($conseilData['next_election'] ?? 2026) ?></strong></p>
            </section>

            <section class="vie-section section-comptes">
                <h2 id="comptes">Les comptes-rendus</h2>
                <p>Consultez les comptes-rendus des conseils municipaux.</p>
                <?php
                $crFile = DATA_DIR . '/comptes_rendus.json';
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
                            <a href="<?= htmlspecialchars(fileUrl($cr['file'])) ?>" class="cr-card<?= fileExists($cr['file']) ? '' : ' cr-missing' ?>" target="_blank" title="Ouvrir le compte-rendu (PDF)">
                                <span class="cr-thumb">
                                    <?php if (!empty($cr['thumbnail']) && fileExists($cr['thumbnail'])): ?>
                                    <img src="<?= htmlspecialchars(fileUrl($cr['thumbnail'])) ?>" alt="" loading="lazy">
                                    <?php elseif (!empty($cr['thumbnail'])): ?>
                                    <i class="fas fa-file-pdf"></i>
                                    <?php else: ?>
                                    <i class="fas fa-file-pdf"></i>
                                    <?php endif; ?>
                                </span>
                                <span class="cr-label"><?php
                    $ts2 = strtotime($cr['date']);
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
                <p style="color:var(--gray-400);">Aucun compte-rendu publié pour le moment.</p>
                <?php endif; ?>
            </section>

            <section class="vie-section section-equipe">
                <h2 id="equipe">L'équipe municipale</h2>
                <div class="team-grid">
                    <?php foreach ($elusData as $member): ?>
                    <div class="team-card">
                        <div class="team-avatar"><?php if (!empty($member['photo'])): ?><img src="<?= htmlspecialchars(fileUrl($member['photo'])) ?>" alt="<?= htmlspecialchars($member['name']) ?>"><?php else: ?><i class="fas fa-user"></i><?php endif; ?></div>
                        <h3><?= htmlspecialchars($member['name']) ?></h3>
                        <p class="role"><?= htmlspecialchars($member['role']) ?></p>
                        <?php
                        $coms = [];
                        for ($i = 1; $i <= 4; $i++) {
                            $k = 'delegation' . $i;
                            if (!empty($member[$k])) $coms[] = htmlspecialchars($member[$k]);
                        }
                        if (!empty($coms)):
                        ?>
                        <p class="delegation" style="font-weight:600;margin-top:0.75rem;font-size:0.85rem;">Commissions municipales</p>
                        <?php foreach ($coms as $c): ?>
                        <p class="delegation"><?= $c ?></p>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>

        </div>
    </div>
</div>
