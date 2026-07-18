<?php
header('Content-Type: text/html; charset=utf-8');
require_once 'config.php';

trackVisit();

// Routing simple
$page = isset($_GET['p']) ? preg_replace('/[^a-z0-9-]/', '', $_GET['p']) : 'accueil';
$page = $page ?: 'accueil';

$page_file = __DIR__ . '/pages/' . $page . '.php';
$page_404  = __DIR__ . '/pages/404.php';

if (!file_exists($page_file)) {
    $page = '404';
    $page_file = $page_404;
    http_response_code(404);
}

$page_titles = [
    'register' => 'Inscription',
    'login' => 'Connexion',
    'mon-compte' => 'Mon compte',
    'webmaster' => 'Contacter le webmaster',
    'guide-admin' => 'Guide d\'utilisation de l\'administration',
];
$page_title = isset($nav[$page]) ? $nav[$page]['label'] . ' | ' . $site_name : ($page_titles[$page] ?? '') . ($page_titles[$page] ?? '' ? ' | ' . $site_name : $site_name);
$is_home = ($page === 'accueil');

// Chargement des données
$newsletter = [];
$newsletter_file = DATA_DIR . '/newsletter.json';
if (file_exists($newsletter_file)) {
    $newsletter = json_decode(file_get_contents($newsletter_file), true) ?: [];
}

ob_start();
include $page_file;
$content = ob_get_clean();

include __DIR__ . '/templates/header.php';
echo $content;
include __DIR__ . '/templates/footer.php';
