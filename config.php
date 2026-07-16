<?php
// Session publique (utilisateurs)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Répertoire de stockage des données persistantes (CR, articles, élus, etc.)
// Sur InfinityFree, le data doit être dans htdocs/data/ (protégé par .htaccess)
$dataDir = __DIR__ . '/data';
if (!is_dir($dataDir)) {
    @mkdir($dataDir, 0755, true);
}
define('DATA_DIR', $dataDir);
define('UPLOADS_DIR', DATA_DIR . '/uploads');
foreach (['pdf', 'thumbnails', 'blog', 'elus', 'conseil', 'carousel', 'gallery'] as $dir) {
    $d = UPLOADS_DIR . '/' . $dir;
    if (!is_dir($d)) {
        @mkdir($d, 0755, true);
    }
}

function fileUrl($path)
{
    if (strpos($path, 'serve.php?f=') === 0) return $path;
    $relative = preg_replace('#^assets/(pdf|thumbnails|blog|images/(cr|newsletter|blog))/#', '$1/', $path);
    if ($relative !== $path) return 'serve.php?f=' . $relative;
    return $path;
}

function fileExists($path)
{
    $paths = [];
    if (strpos($path, 'serve.php?f=') === 0) {
        $paths[] = UPLOADS_DIR . '/' . substr($path, 12);
        $paths[] = dirname(__DIR__) . '/assets/' . substr($path, 12);
    } else {
        $paths[] = dirname(__DIR__) . '/' . $path;
        $relative = preg_replace('#^assets/(pdf|thumbnails|blog|images/(cr|newsletter|blog))/#', '', $path);
        if ($relative !== $path) $paths[] = UPLOADS_DIR . '/' . $relative;
    }
    foreach ($paths as $p) {
        if (file_exists($p)) return $p;
    }
    return false;
}

// Configuration du site
$site_name = "Mégange";
$site_tagline = "Un village mosellan où il fait bon vivre";
$site_url = "https://village-megange.fr";
$site_dev_url = "https://megange.site.je";
$site_email = "mairie@village-megange.fr";
$contact_emails = ["david.better@gmail.com", "mairie.megange@wanadoo.fr"];
$site_address = "25 rue Principale, 57220 Mégange";
$site_phone = "+33 3 87 35 70 30";

// Configuration mairie éditable (admin/config-site.php)
$mairieConfigFile = DATA_DIR . '/mairie_config.json';
if (file_exists($mairieConfigFile)) {
    $mc = json_decode(file_get_contents($mairieConfigFile), true);
    if ($mc) {
        if (!empty($mc['phone'])) $site_phone = $mc['phone'];
        if (!empty($mc['email'])) $site_email = $mc['email'];
        if (!empty($mc['address'])) $site_address = $mc['address'];
    }
}

// Réseaux sociaux
$social = [
    'facebook' => '#',
    'youtube' => '#',
];

// Utilisateur connecté (doit être avant $nav qui utilise $user_logged_in)
$user_logged_in = !empty($_SESSION['user_id']);
$current_user = null;
if ($user_logged_in) {
    $usersFile = DATA_DIR . '/abonnes.json';
    $allUsers = file_exists($usersFile) ? (json_decode(file_get_contents($usersFile), true) ?: []) : [];
    foreach ($allUsers as $u) {
        if ($u['id'] === (int)$_SESSION['user_id']) { $current_user = $u; break; }
    }
    if (!$current_user) { $user_logged_in = false; unset($_SESSION['user_id']); }
}

// Navigation principale
$nav = [
    'accueil'     => ['label' => 'Accueil',     'icon' => 'fa-house'],
    'la-commune'  => [
        'label'    => 'La commune',
        'icon'     => 'fa-tree',
        'children' => [
            ['label' => 'Histoire',             'href' => 'index.php?p=la-commune#histoire'],
            ['label' => 'Bulletin communal',    'href' => 'index.php?p=la-commune#bulletins'],
            ['label' => 'Déchetterie',          'href' => 'index.php?p=la-commune#dechetterie'],
            ['label' => 'Ordures ménagères',    'href' => 'index.php?p=la-commune#ordures'],
            ['label' => 'Location salle',       'href' => 'index.php?p=la-commune#location-salle'],
        ]
    ],
    'vie-municipale' => [
        'label'    => 'Vie municipale',
        'icon'     => 'fa-landmark',
        'children' => [
            ['label' => 'Conseil municipal',    'href' => 'index.php?p=vie-municipale#conseil'],
            ['label' => 'Équipe municipale',    'href' => 'index.php?p=vie-municipale#equipe'],
            ['label' => 'Comptes-rendus',       'href' => 'index.php?p=vie-municipale#comptes'],
        ]
    ],
    'vie-locale'  => ['label' => 'Actualités',  'icon' => 'fa-newspaper'],
    'login'       => ['label' => ($user_logged_in ? 'Mon compte' : 'Connexion'), 'icon' => ($user_logged_in ? 'fa-user' : 'fa-right-to-bracket')],
];

// Informations mairie (éditables via admin/horaires.php)
$mairieHoursFile = DATA_DIR . '/mairie_hours.json';
$mairieDays = ['Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi','Dimanche'];
$mairieHoursDefault = [];
foreach ($mairieDays as $d) $mairieHoursDefault[$d] = [];
$mairieHoursDefault['Mardi'] = ['17h30 - 20h00'];

$mairie_hours = $mairieHoursDefault;
if (file_exists($mairieHoursFile)) {
    $raw = json_decode(file_get_contents($mairieHoursFile), true);
    if ($raw) {
        foreach ($mairieDays as $d) {
            $v = $raw[$d] ?? '';
            if (is_string($v)) {
                $mairie_hours[$d] = ($v === '' || strtolower($v) === 'fermé') ? [] : [$v];
            } elseif (is_array($v)) {
                $mairie_hours[$d] = $v;
            } else {
                $mairie_hours[$d] = [];
            }
        }
    }
}

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

// Envoi email — SMTP si configuré, sinon mail() natif
// Les identifiants SMTP sont stockés dans data/smtp_config.json (hors git)
function sendMail($to, $subject, $body, $from = null)
{
    global $site_email, $site_name;
    $from = $from ?: $site_email;

    $smtpConfig = [];
    $smtpFile = DATA_DIR . '/smtp_config.json';
    if (file_exists($smtpFile)) {
        $smtpConfig = json_decode(file_get_contents($smtpFile), true) ?: [];
    }

    if (!empty($smtpConfig['password'])) {
        require_once __DIR__ . '/vendor/autoload.php';
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = $smtpConfig['host'] ?? 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = $smtpConfig['username'] ?? $from;
            $mail->Password   = $smtpConfig['password'];
            $mail->SMTPSecure = $smtpConfig['secure'] ?? PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = $smtpConfig['port'] ?? 587;
            $mail->CharSet    = 'UTF-8';
            $mail->setFrom($from, $site_name);
            $mail->addReplyTo($from);
            $mail->addAddress($to);
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log('sendMail error: ' . $e->getMessage());
            return false;
        }
    }

    // Fallback mail() si SMTP pas configuré
    $headers = 'From: ' . $site_name . ' <' . $from . '>' . "\r\n"
             . 'Reply-To: ' . $from . "\r\n"
             . 'Return-Path: ' . $from . "\r\n"
             . 'Content-Type: text/plain; charset=utf-8' . "\r\n"
             . 'X-Mailer: PHP/' . phpversion() . "\r\n"
             . 'X-Priority: 3' . "\r\n"
             . 'MIME-Version: 1.0';
    return @mail($to, $subject, $body, $headers, '-f ' . $from);
}

// Détection navigateur/OS
function detectBrowser($ua) {
    if (strpos($ua, 'Chrome') !== false && strpos($ua, 'Edg') === false) return 'Chrome';
    if (strpos($ua, 'Edg') !== false) return 'Edge';
    if (strpos($ua, 'Firefox') !== false) return 'Firefox';
    if (strpos($ua, 'Safari') !== false) return 'Safari';
    if (strpos($ua, 'OPR') !== false || strpos($ua, 'Opera') !== false) return 'Opera';
    return 'Autre';
}
function detectOS($ua) {
    if (strpos($ua, 'Windows') !== false) return 'Windows';
    if (strpos($ua, 'Mac') !== false) return 'macOS';
    if (strpos($ua, 'Linux') !== false) return 'Linux';
    if (strpos($ua, 'Android') !== false) return 'Android';
    if (strpos($ua, 'iPhone') !== false || strpos($ua, 'iPad') !== false) return 'iOS';
    return 'Autre';
}
function detectMobile($ua) {
    return strpos($ua, 'Mobile') !== false || strpos($ua, 'Android') !== false ? 'Mobile' : 'Desktop';
}
function detectBot($ua) {
    if (strpos($ua, 'Googlebot') !== false) return 'Googlebot';
    if (strpos($ua, 'Bingbot') !== false || strpos($ua, 'bingbot') !== false) return 'Bingbot';
    if (strpos($ua, 'facebookexternalhit') !== false) return 'Facebook';
    if (strpos($ua, 'Twitterbot') !== false) return 'Twitter/X';
    if (strpos($ua, 'YandexBot') !== false) return 'Yandex';
    if (strpos($ua, 'AhrefsBot') !== false) return 'Ahrefs';
    if (strpos($ua, 'SemrushBot') !== false) return 'Semrush';
    if (strpos($ua, 'MJ12bot') !== false) return 'MJ12bot';
    if (strpos($ua, 'DuckDuckGo') !== false) return 'DuckDuckGo';
    if (strpos($ua, 'GPTBot') !== false || strpos($ua, 'Claude') !== false) return 'IA';
    if (strpos($ua, 'bot') !== false || strpos($ua, 'crawl') !== false || strpos($ua, 'spider') !== false) return 'Autre bot';
    return false;
}

// Tracker détaillé
function trackVisit()
{
    $file = DATA_DIR . '/counter.json';
    $data = file_exists($file) ? (json_decode(file_get_contents($file), true) ?: []) : [];
    if (!isset($data['total'])) $data['total'] = 0;
    if (!isset($data['pageviews'])) $data['pageviews'] = 0;
    if (!isset($data['by_date'])) $data['by_date'] = [];
    if (!isset($data['visits'])) $data['visits'] = [];

    $today = date('Y-m-d');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $hash = sha1($ip . '|' . $ua . '|megange2026');
    $page = $_GET['p'] ?? 'accueil';
    $ref = $_SERVER['HTTP_REFERER'] ?? '';
    if ($ref !== '') {
        if (strpos($ref, 'google') !== false) $referrer = 'Google';
        elseif (strpos($ref, 'facebook') !== false) $referrer = 'Facebook';
        elseif (strpos($ref, 'twitter') !== false || strpos($ref, 't.co') !== false) $referrer = 'Twitter/X';
        elseif (strpos($ref, 'instagram') !== false) $referrer = 'Instagram';
        elseif (strpos($ref, 'bing') !== false) $referrer = 'Bing';
        elseif (strpos($ref, $_SERVER['HTTP_HOST'] ?? '') !== false) $referrer = 'Interne';
        else $referrer = 'Autre site';
    } else {
        $referrer = 'Direct';
    }
    $browser = detectBrowser($ua);
    $os = detectOS($ua);
    $device = detectMobile($ua);
    $bot = detectBot($ua);

    // Initialise la date si nécessaire
    if (!isset($data['by_date'])) $data['by_date'] = [];
    if (!isset($data['by_date'][$today])) {
        $data['by_date'][$today] = ['visitors' => 0, 'pageviews' => 0, 'pages' => [], 'referrers' => [], 'browsers' => [], 'os' => [], 'devices' => [], 'bots' => []];
    }

    $data['pageviews']++;
    $data['by_date'][$today]['pageviews']++;
    $data['by_date'][$today]['pages'][$page] = ($data['by_date'][$today]['pages'][$page] ?? 0) + 1;
    $data['by_date'][$today]['referrers'][$referrer] = ($data['by_date'][$today]['referrers'][$referrer] ?? 0) + 1;

    if ($bot) {
        $data['by_date'][$today]['bots'][$bot] = ($data['by_date'][$today]['bots'][$bot] ?? 0) + 1;
    } else {
        $data['by_date'][$today]['browsers'][$browser] = ($data['by_date'][$today]['browsers'][$browser] ?? 0) + 1;
        $data['by_date'][$today]['os'][$os] = ($data['by_date'][$today]['os'][$os] ?? 0) + 1;
        $data['by_date'][$today]['devices'][$device] = ($data['by_date'][$today]['devices'][$device] ?? 0) + 1;
    }

    // Visiteur unique
    $isNew = ($data['visits'][$hash] ?? '') !== $today;
    if ($isNew) {
        $data['visits'][$hash] = $today;
        $data['total']++;
        $data['by_date'][$today]['visitors']++;
    }

    // Nettoie les vieux hashs (>30 jours)
    $cutoff = date('Y-m-d', strtotime('-30 days'));
    foreach ($data['visits'] as $h => $d) {
        if ($d < $cutoff) unset($data['visits'][$h]);
    }
    // Nettoie les vieilles dates (>90 jours)
    foreach ($data['by_date'] as $d => $v) {
        if ($d < $cutoff) unset($data['by_date'][$d]);
    }

    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
}

function getCounterStats()
{
    $file = DATA_DIR . '/counter.json';
    $data = file_exists($file) ? (json_decode(file_get_contents($file), true) ?: []) : [];
    $today = date('Y-m-d');
    $weekAgo = date('Y-m-d', strtotime('-7 days'));
    $monthAgo = date('Y-m-d', strtotime('-30 days'));

    $bd = $data['by_date'] ?? [];
    $total = $data['total'] ?? 0;
    $pageviews = $data['pageviews'] ?? 0;
    $todayVisitors = $bd[$today]['visitors'] ?? 0;
    $todayPageviews = $bd[$today]['pageviews'] ?? 0;
    $weekVisitors = 0; $weekPageviews = 0;
    $monthVisitors = 0; $monthPageviews = 0;
    $pages = []; $referrers = []; $browsers = []; $oses = []; $devices = []; $bots = [];

    foreach ($bd as $d => $v) {
        if ($d >= $weekAgo) { $weekVisitors += $v['visitors']; $weekPageviews += $v['pageviews']; }
        if ($d >= $monthAgo) {
            $monthVisitors += $v['visitors']; $monthPageviews += $v['pageviews'];
            foreach ($v['pages'] ?? [] as $k => $c) $pages[$k] = ($pages[$k] ?? 0) + $c;
            foreach ($v['referrers'] ?? [] as $k => $c) $referrers[$k] = ($referrers[$k] ?? 0) + $c;
            foreach ($v['browsers'] ?? [] as $k => $c) $browsers[$k] = ($browsers[$k] ?? 0) + $c;
            foreach ($v['os'] ?? [] as $k => $c) $oses[$k] = ($oses[$k] ?? 0) + $c;
            foreach ($v['devices'] ?? [] as $k => $c) $devices[$k] = ($devices[$k] ?? 0) + $c;
            foreach ($v['bots'] ?? [] as $k => $c) $bots[$k] = ($bots[$k] ?? 0) + $c;
        }
    }
    arsort($pages); arsort($referrers); arsort($browsers); arsort($oses); arsort($devices); arsort($bots);

    // Tendance 30 jours
    $trend = [];
    for ($i = 29; $i >= 0; $i--) {
        $d = date('Y-m-d', strtotime("-$i days"));
        $trend[] = ['date' => $d, 'visitors' => $bd[$d]['visitors'] ?? 0, 'pageviews' => $bd[$d]['pageviews'] ?? 0];
    }

    return [
        'total' => $total, 'pageviews' => $pageviews,
        'today' => ['visitors' => $todayVisitors, 'pageviews' => $todayPageviews],
        'week' => ['visitors' => $weekVisitors, 'pageviews' => $weekPageviews],
        'month' => ['visitors' => $monthVisitors, 'pageviews' => $monthPageviews],
        'pages' => $pages, 'referrers' => $referrers,
        'browsers' => $browsers, 'oses' => $oses, 'devices' => $devices, 'bots' => $bots,
        'trend' => $trend,
    ];
}
