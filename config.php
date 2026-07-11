<?php
// Répertoire de stockage des données persistantes (CR, articles, élus, etc.)
// En local : ./data/ — Sur AlwaysData : définir un chemin hors du dépôt git
$dataDir = __DIR__ . '/data';
$externalDataDir = dirname(__DIR__) . '/megange-data';
if (is_dir($externalDataDir) && is_writable($externalDataDir)) {
    $dataDir = $externalDataDir;
}
define('DATA_DIR', $dataDir);
if (!is_dir(DATA_DIR)) { @mkdir(DATA_DIR, 0755, true); }

// Configuration du site
$site_name = "Mégange";
$site_tagline = "Un village mosellan où il fait bon vivre";
$site_url = "https://megange.fr";
$site_email = "mairie@megange.fr";
$contact_email = "david.better@gmail.com";
$site_address = "1 Place de la Mairie, 57590 Mégange";
$site_phone = "+33 3 87 00 00 00";

// Réseaux sociaux
$social = [
    'facebook' => '#',
    'youtube' => '#',
];

// Navigation principale
$nav = [
    'accueil'     => ['label' => 'Accueil',     'icon' => 'fa-house'],
    'la-commune'  => ['label' => 'La commune',  'icon' => 'fa-tree'],
    'vie-municipale' => [
        'label'    => 'Vie municipale',
        'icon'     => 'fa-landmark',
        'children' => [
            ['label' => 'Conseil municipal',    'href' => 'index.php?p=vie-municipale#conseil'],
            ['label' => 'Équipe municipale',    'href' => 'index.php?p=vie-municipale#equipe'],
            ['label' => 'Comptes-rendus',       'href' => 'index.php?p=vie-municipale#comptes'],
        ]
    ],
    'services'    => ['label' => 'Services',    'icon' => 'fa-hand-holding-heart'],
    'vie-locale'  => ['label' => 'News',  'icon' => 'fa-newspaper'],
    'galerie'     => ['label' => 'Galerie',     'icon' => 'fa-images'],
];

// Informations mairie
$mairie_hours = [
    'Lundi'     => '14h00 - 17h00',
    'Mardi'     => '9h00 - 12h00',
    'Mercredi'  => 'Fermé',
    'Jeudi'     => '14h00 - 17h00',
    'Vendredi'  => '9h00 - 12h00',
    'Samedi'    => '9h00 - 12h00 (1er du mois)',
    'Dimanche'  => 'Fermé',
];

// Admin
$admin_password = 'megange2026'; // Changez ce mot de passe !

// PanneauPocket
$panneaupocket_enabled = true;
$panneaupocket_widget_url = 'https://app.panneaupocket.com/embeded/250252113'; // URL du widget iframe
$panneaupocket_commune_id = '250252113'; // ID de la commune sur PanneauPocket
$panneaupocket_public_url = 'https://app.panneaupocket.com/ville/250252113-megange-57220';

// Équipe municipale (fallback si data/elus.json est vide)
$municipal_team = [
    ['name' => 'Nom du Maire', 'role' => 'Maire', 'delegation' => ''],
    ['name' => 'Nom Adjoint 1', 'role' => '1er Adjoint', 'delegation' => 'Travaux et urbanisme'],
    ['name' => 'Nom Adjoint 2', 'role' => '2ème Adjoint', 'delegation' => 'Vie associative et culture'],
];
