<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <meta name="description" content="Site officiel de la commune de Mégange, village mosellan d'environ 300 habitants. Informations municipales, vie locale, services et démarches.">
    <link rel="canonical" href="<?= $site_url ?>/<?= $page === 'accueil' ? '' : 'index.php?p=' . $page ?>">
    <meta property="og:title" content="<?= $page_title ?>">
    <meta property="og:description" content="Site officiel de la commune de Mégange, village mosellan d'environ 300 habitants. Informations municipales, vie locale, services et démarches.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= $site_url ?>/<?= $page === 'accueil' ? '' : 'index.php?p=' . $page ?>">
    <meta property="og:image" content="<?= $site_url ?>/assets/images/hero.jpg">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="google-site-verification" content="AJOUTE_ICI_TON_CODE_VERIFICATION">
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "GovernmentOrganization",
        "name": "Mairie de Mégange",
        "description": "Site officiel de la commune de Mégange, village mosellan d'environ 200 habitants.",
        "url": "<?= $site_url ?>",
        "telephone": "<?= $site_phone ?>",
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "25 rue Principale",
            "postalCode": "57220",
            "addressLocality": "Mégange",
            "addressCountry": "FR"
        }
    }
    </script>
    <script>document.documentElement.setAttribute('data-theme', localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme:dark)').matches?'dark':'light'));</script>
    <link rel="icon" type="image/svg+xml" href="assets/images/favicon.svg">
    <link rel="stylesheet" href="assets/fonts/fontawesome.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
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
                <?php if ($user_logged_in): ?>
                <a href="index.php?p=mon-compte&logout=1" class="theme-toggle" style="text-decoration:none;font-size:0.85rem;" aria-label="Déconnexion" title="Déconnexion">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
                <?php endif; ?>
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
                        <a href="index.php?p=<?= $key ?>" class="<?= $page === $key ? 'active' : '' ?>"<?= $page === $key ? ' aria-current="page"' : '' ?>>
                            <i class="fas <?= $item['icon'] ?>" aria-hidden="true"></i>
                            <span><?= $item['label'] ?></span>
                            <?php if ($hasChildren): ?><i class="fas fa-chevron-down dropdown-arrow" aria-hidden="true"></i><?php endif; ?>
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
