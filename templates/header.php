<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <meta name="description" content="Site officiel de la commune de Mégange, village mosellan d'environ 300 habitants. Informations municipales, vie locale, services et démarches.">
    <link rel="icon" type="image/svg+xml" href="assets/images/favicon.svg">
    <link rel="stylesheet" href="assets/fonts/fontawesome.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <script>document.documentElement.setAttribute('data-theme', localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme:dark)').matches?'dark':'light'));</script>
</head>
<body>
    <a href="#main" class="skip-link">Aller au contenu</a>

    <header class="site-header" role="banner">
        <div class="container header-inner">
            <a href="index.php" class="logo">
                <span class="logo-icon"><i class="fas fa-tree"></i></span>
                <div class="logo-text">
                    <span class="logo-title"><?= $site_name ?></span>
                    <span class="logo-subtitle"><?= $site_tagline ?></span>
                </div>
            </a>

            <div style="display:flex;align-items:center;gap:0.25rem;">
                <button class="theme-toggle" id="themeToggle" aria-label="Changer le thème">
                    <i class="fas fa-moon"></i>
                </button>
                <button class="menu-toggle" aria-label="Menu" aria-expanded="false">
                    <span></span><span></span><span></span>
                </button>
            </div>

            <nav class="main-nav" role="navigation" aria-label="Navigation principale">
                <ul>
                    <?php foreach ($nav as $key => $item): ?>
                    <li>
                        <a href="index.php?p=<?= $key ?>" class="<?= $page === $key ? 'active' : '' ?>">
                            <i class="fas <?= $item['icon'] ?>"></i>
                            <span><?= $item['label'] ?></span>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </nav>
        </div>
    </header>

    <main id="main" role="main">
