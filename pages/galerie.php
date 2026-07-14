<?php
$galleryFile = DATA_DIR . '/gallery.json';
$galleryItems = file_exists($galleryFile) ? (json_decode(file_get_contents($galleryFile), true) ?: []) : [];
$defaultGallery = [
    ['image' => 'assets/images/gallery/eglise.jpg', 'label' => "L'église Saint-Martin"],
    ['image' => 'assets/images/gallery/mairie.jpg', 'label' => 'La mairie'],
    ['image' => 'assets/images/gallery/paysage.jpg', 'label' => 'Campagne mosellane'],
    ['image' => 'assets/images/gallery/sentier.jpg', 'label' => 'Sentier de randonnée'],
    ['image' => 'assets/images/gallery/fete.jpg', 'label' => 'Fête du village'],
    ['image' => 'assets/images/gallery/automne.jpg', 'label' => 'Mégange en automne'],
    ['image' => 'assets/images/gallery/salle.jpg', 'label' => 'Salle polyvalente'],
    ['image' => 'assets/images/gallery/printemps.jpg', 'label' => 'Le village au printemps'],
];
if (empty($galleryItems)) $galleryItems = $defaultGallery;
?>
<div class="page-header">
    <div class="container">
        <h1>Galerie</h1>
        <p>Découvrez Mégange en images</p>
    </div>
</div>

<div class="content-page">
    <div class="container">
        <div class="gallery-grid">
            <?php foreach ($galleryItems as $item):
                $imgUrl = fileUrl($item['image']);
                $label = htmlspecialchars($item['label'] ?? '');
                $link = !empty($item['link']) ? $item['link'] : '';
            ?>
            <?php if ($link): ?><a href="<?= htmlspecialchars($link) ?>" target="_blank" rel="noopener" style="text-decoration:none;color:inherit;"><?php endif; ?>
            <div class="gallery-item" data-src="<?= $imgUrl ?>">
                <img src="<?= $imgUrl ?>" alt="<?= $label ?>" loading="lazy">
                <div class="overlay"><?= $label ?></div>
            </div>
            <?php if ($link): ?></a><?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="lightbox">
    <button class="lightbox-close"><i class="fas fa-times"></i></button>
    <button class="lightbox-nav lightbox-prev"><i class="fas fa-chevron-left"></i></button>
    <img src="" alt="">
    <button class="lightbox-nav lightbox-next"><i class="fas fa-chevron-right"></i></button>
</div>
