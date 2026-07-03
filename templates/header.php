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
                <span class="logo-icon"><img src="assets/images/blason.svg" alt="Blason de Mégange" class="logo-blason"></span>
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
                    <?php foreach ($nav as $key => $item):
                        $hasChildren = isset($item['children']) && count($item['children']) > 0;
                    ?>
                    <li class="nav-item<?= $hasChildren ? ' has-dropdown' : '' ?>">
                        <a href="index.php?p=<?= $key ?>" class="<?= $page === $key ? 'active' : '' ?>">
                            <i class="fas <?= $item['icon'] ?>"></i>
                            <span><?= $item['label'] ?></span>
                            <?php if ($hasChildren): ?><i class="fas fa-chevron-down dropdown-arrow"></i><?php endif; ?>
                        </a>
                        <?php if ($hasChildren): ?>
                        <ul class="dropdown">
                            <?php foreach ($item['children'] as $child): ?>
                            <li><a href="<?= $child['href'] ?>"><?= $child['label'] ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                        <?php endif; ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </nav>
        </div>
    </header>

    <main id="main" role="main">
