<div class="page-header">
    <div class="container">
        <h1>Alertes</h1>
        <p>Informations en temps réel et alertes de la commune</p>
    </div>
</div>

<div class="content-page">
    <div class="container">
        <div class="pp-layout">
            <div class="pp-main">
                <h2 class="pp-section-title">Dernières informations</h2>

                <?php if (!empty($panneaupocket_widget_url)): ?>
                <div class="pp-embed-wrap">
                    <iframe src="<?= htmlspecialchars($panneaupocket_widget_url) ?>" height="500" frameborder="0" style="width:100%;max-width:360px;border-radius:var(--radius);margin:0 auto;display:block;" loading="lazy"></iframe>
                </div>

                <div class="pp-footer-link">
                    <a href="<?= $panneaupocket_public_url ?>" target="_blank" rel="noopener" class="btn btn-outline">
                        <i class="fas fa-external-link-alt"></i> Voir toutes les alertes sur PanneauPocket
                    </a>
                </div>
                <?php else: ?>
                <?php
                require_once __DIR__ . '/../includes/panneaupocket.php';
                $alerts = get_panneaupocket_alerts();
                ?>
                <?php if ($alerts && !empty($alerts)): ?>
                <div class="pp-grid">
                    <?php $count = 0; ?>
                    <?php foreach ($alerts as $a): ?>
                    <?php if ($count >= 10) break; $count++; ?>
                    <article class="pp-card">
                        <?php if (!empty($a['image'])): ?>
                        <div class="pp-card-img">
                            <img src="<?= htmlspecialchars($a['image']) ?>" alt="" loading="lazy">
                        </div>
                        <?php endif; ?>
                        <div class="pp-card-body">
                            <div class="pp-card-meta">
                                <?php if (!empty($a['date'])): ?>
                                <span class="pp-date"><i class="far fa-clock"></i> <?= htmlspecialchars($a['date']) ?></span>
                                <?php endif; ?>
                                <?php if (!empty($a['author'])): ?>
                                <span class="pp-author"><i class="fas fa-user"></i> <?= htmlspecialchars($a['author']) ?></span>
                                <?php endif; ?>
                            </div>
                            <h3 class="pp-card-title"><?= htmlspecialchars($a['title']) ?></h3>
                            <?php if (!empty($a['body'])): ?>
                            <div class="pp-card-text"><?= $a['body'] ?></div>
                            <?php endif; ?>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
                <div class="pp-footer-link">
                    <a href="<?= $panneaupocket_public_url ?>" target="_blank" rel="noopener" class="btn btn-outline"><i class="fas fa-external-link-alt"></i> Voir toutes les alertes sur PanneauPocket</a>
                </div>
                <?php else: ?>
                <div class="card" style="text-align: center; padding: 3rem;">
                    <div class="card-icon" style="margin: 0 auto 1.5rem;">
                        <i class="fas fa-bell" style="font-size: 2.5rem; color: var(--terracotta);"></i>
                    </div>
                    <h3>Restez informés</h3>
                    <p style="margin-bottom: 1.5rem;">Téléchargez l'application <strong>PanneauPocket</strong> pour recevoir les alertes et informations de la commune directement sur votre téléphone.</p>
                    <div style="display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap; margin-bottom: 2rem;">
                        <a href="https://apps.apple.com/fr/app/panneaupocket/id1143507069" target="_blank" rel="noopener" class="btn btn-primary"><i class="fab fa-apple"></i> App Store</a>
                        <a href="https://play.google.com/store/apps/details?id=panopoche.panopoche&hl=fr" target="_blank" rel="noopener" class="btn btn-primary"><i class="fab fa-google-play"></i> Google Play</a>
                        <a href="<?= $panneaupocket_public_url ?>" target="_blank" rel="noopener" class="btn btn-outline"><i class="fas fa-globe"></i> Version web</a>
                    </div>
                    <p style="font-size: 0.9rem; color: var(--gray-400);"><i class="fas fa-check-circle" style="color: var(--green-500);"></i> Gratuit • anonyme • sans publicité</p>
                </div>
                <?php endif; ?>
                <?php endif; ?>
            </div>

            <aside class="pp-sidebar">
                <div class="card" style="text-align: center; padding: 2rem;">
                    <div style="margin: 0 auto 1rem; width: 64px; height: 64px; background: var(--green-100); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-bell" style="font-size: 1.5rem; color: var(--green-500);"></i>
                    </div>
                    <h3 style="margin-bottom: 0.75rem;">PanneauPocket</h3>
                    <p style="font-size: 0.9rem; color: var(--gray-400); margin-bottom: 1.25rem;">
                        Recevez les alertes de la commune directement sur votre téléphone.
                    </p>
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <a href="https://apps.apple.com/fr/app/panneaupocket/id1143507069" target="_blank" rel="noopener" class="btn btn-primary btn-sm"><i class="fab fa-apple"></i> App Store</a>
                        <a href="https://play.google.com/store/apps/details?id=panopoche.panopoche&hl=fr" target="_blank" rel="noopener" class="btn btn-primary btn-sm"><i class="fab fa-google-play"></i> Google Play</a>
                        <a href="<?= $panneaupocket_public_url ?>" target="_blank" rel="noopener" class="btn btn-outline btn-sm"><i class="fas fa-globe"></i> Version web</a>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</div>
