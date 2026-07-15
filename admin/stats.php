<?php
session_start();
require_once __DIR__ . '/../config.php';

if (empty($_SESSION['admin'])) { header('Location: index.php'); exit; }

$stats = getCounterStats();
$trend = $stats['trend'];
$maxTrend = count($trend) ? max(array_column($trend, 'pageviews')) : 0;
if ($maxTrend < 1) $maxTrend = 1;
$monthPages = $stats['pages'];
$monthReferrers = $stats['referrers'];
$monthBrowsers = $stats['browsers'];
$monthOses = $stats['oses'];
$monthDevices = $stats['devices'];
$maxPages = count($monthPages) ? max($monthPages) : 1;
$maxRef = count($monthReferrers) ? max($monthReferrers) : 1;
$maxBrowser = count($monthBrowsers) ? max($monthBrowsers) : 1;
$maxOS = count($monthOses) ? max($monthOses) : 1;
$maxDevice = count($monthDevices) ? max($monthDevices) : 1;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques | Administration</title>
    <link rel="stylesheet" href="../assets/fonts/fontawesome.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-bar { background: var(--green-900); color: white; padding: 0.75rem 0; }
        .admin-bar a { color: var(--gold); }
        .stat-grid { display: grid; gap: 1.5rem; }
        .stat-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius); padding: 1.25rem; }
        .stat-card h3 { margin-bottom: 0.75rem; font-size: 1rem; }
        .stat-card .big { font-size: 2.5rem; font-weight: 700; color: var(--green-600); }
        .bar-row { display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.35rem; font-size: 0.9rem; }
        .bar-row .label { min-width: 120px; text-align: right; color: var(--gray-500); }
        .bar-row .bar { height: 1.25rem; background: var(--green-100); border-radius: var(--radius-sm); transition: width 0.3s; min-width: 2px; }
        .bar-row .count { min-width: 2.5rem; font-weight: 600; }
        .trend-chart { display: flex; align-items: flex-end; gap: 2px; height: 120px; }
        .trend-chart .col { flex: 1; display: flex; flex-direction: column; align-items: center; }
        .trend-chart .bar-col { width: 100%; display: flex; flex-direction: column-reverse; flex: 1; gap: 1px; }
        .trend-chart .bar-visitors { background: var(--green-200); border-radius: 2px 2px 0 0; }
        .trend-chart .bar-pageviews { background: var(--green-500); border-radius: 2px 2px 0 0; }
        .trend-chart .label-date { font-size: 0.6rem; color: var(--gray-400); margin-top: 2px; white-space: nowrap; }
        .legend { display: flex; gap: 1.5rem; margin-bottom: 1rem; font-size: 0.85rem; }
        .legend span { display: flex; align-items: center; gap: 0.35rem; }
        .legend .dot { width: 10px; height: 10px; border-radius: 2px; display: inline-block; }
        @media (max-width: 768px) {
            .bar-row .label { min-width: 80px; font-size: 0.8rem; }
            .trend-chart { height: 80px; }
        }
    </style>
</head>
<body>
    <div class="admin-bar">
        <div class="container" style="display:flex;justify-content:space-between;align-items:center;">
            <span><i class="fas fa-shield-alt"></i> Statistiques</span>
            <a href="index.php" style="color:var(--gold);"><i class="fas fa-arrow-left"></i> Retour</a>
        </div>
    </div>
    <main style="padding: 2rem 0;">
        <div class="container">
            <div class="stat-grid">
                <!-- Chiffres clés -->
                <div class="stat-card">
                    <h3><i class="fas fa-eye"></i> Vue d'ensemble — 30 derniers jours</h3>
                    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(120px,1fr));gap:1rem;text-align:center;">
                        <div><div class="big"><?= $stats['month']['visitors'] ?></div><div style="color:var(--gray-400);font-size:0.85rem;">Visiteurs</div></div>
                        <div><div class="big"><?= $stats['month']['pageviews'] ?></div><div style="color:var(--gray-400);font-size:0.85rem;">Pages vues</div></div>
                        <div><div class="big"><?= $stats['pageviews'] ?></div><div style="color:var(--gray-400);font-size:0.85rem;">Total pages vues</div></div>
                        <div><div class="big"><?= $stats['total'] ?></div><div style="color:var(--gray-400);font-size:0.85rem;">Visiteurs uniques</div></div>
                    </div>
                </div>

                <!-- Tendance -->
                <div class="stat-card">
                    <h3><i class="fas fa-chart-line"></i> Tendance (30 jours)</h3>
                    <div class="legend">
                        <span><span class="dot" style="background:var(--green-200);"></span> Visiteurs</span>
                        <span><span class="dot" style="background:var(--green-500);"></span> Pages vues</span>
                    </div>
                    <div class="trend-chart">
                        <?php foreach ($trend as $t): ?>
                        <div class="col">
                            <div class="bar-col">
                                <div class="bar-pageviews" style="flex:<?= $t['pageviews'] / $maxTrend * 100 ?>"></div>
                                <div class="bar-visitors" style="flex:<?= $t['visitors'] / $maxTrend * 100 ?>"></div>
                            </div>
                            <span class="label-date"><?= date('d/m', strtotime($t['date'])) ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Pages -->
                <div class="stat-card">
                    <h3><i class="fas fa-file"></i> Pages les plus visitées</h3>
                    <?php foreach ($monthPages as $name => $count): ?>
                    <div class="bar-row">
                        <span class="label"><?= htmlspecialchars($name) ?></span>
                        <div class="bar" style="width:<?= $count / $maxPages * 100 ?>%"></div>
                        <span class="count"><?= $count ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Provenance -->
                <div class="stat-card">
                    <h3><i class="fas fa-external-link-alt"></i> Provenance</h3>
                    <?php foreach ($monthReferrers as $name => $count): ?>
                    <div class="bar-row">
                        <span class="label"><?= htmlspecialchars($name) ?></span>
                        <div class="bar" style="width:<?= $count / $maxRef * 100 ?>%"></div>
                        <span class="count"><?= $count ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Navigateur -->
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">
                    <div class="stat-card">
                        <h3><i class="fas fa-globe"></i> Navigateur</h3>
                        <?php foreach ($monthBrowsers as $name => $count): ?>
                        <div class="bar-row">
                            <span class="label"><?= htmlspecialchars($name) ?></span>
                            <div class="bar" style="width:<?= $count / $maxBrowser * 100 ?>%"></div>
                            <span class="count"><?= $count ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="stat-card">
                        <h3><i class="fas fa-laptop"></i> OS</h3>
                        <?php foreach ($monthOses as $name => $count): ?>
                        <div class="bar-row">
                            <span class="label"><?= htmlspecialchars($name) ?></span>
                            <div class="bar" style="width:<?= $count / $maxOS * 100 ?>%"></div>
                            <span class="count"><?= $count ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Appareil -->
                <div class="stat-card">
                    <h3><i class="fas fa-mobile-alt"></i> Appareil</h3>
                    <?php foreach ($monthDevices as $name => $count): ?>
                    <div class="bar-row">
                        <span class="label"><?= htmlspecialchars($name) ?></span>
                        <div class="bar" style="width:<?= $count / $maxDevice * 100 ?>%"></div>
                        <span class="count"><?= $count ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
