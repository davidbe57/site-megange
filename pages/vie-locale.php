<?php
$art_file = DATA_DIR . '/articles.json';
$articles = file_exists($art_file) ? (json_decode(file_get_contents($art_file), true) ?: []) : [];
usort($articles, function($a, $b) { return strcmp($b['date'] ?? '', $a['date'] ?? ''); });
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
            <h2 id="blog">Le blog de Mégange</h2>
            <p>Toutes les actualités du village</p>
        </div>

        <div class="blog-list">
            <?php foreach ($articles as $art): ?>
            <article class="blog-card">
                <?php if (!empty($art['image']) && fileExists($art['image'])): ?>
                <div class="blog-card-image">
                    <img src="<?= htmlspecialchars(fileUrl($art['image'])) ?>" alt="<?= htmlspecialchars($art['title']) ?>" loading="lazy">
                </div>
                <?php endif; ?>
                <div class="blog-card-body">
                    <div class="blog-meta">
                        <span><i class="fas fa-calendar"></i> <?= date('d/m/Y', strtotime($art['date'])) ?></span>
                        <span><i class="fas fa-user"></i> <?= htmlspecialchars($art['author']) ?></span>
                    </div>
                    <h3><?= htmlspecialchars($art['title']) ?></h3>
                    <p><?= nl2br(strip_tags($art['content'], '<b><i><u><a><br><p><strong><em><ul><ol><li>')) ?></p>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
