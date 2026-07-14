<?php
session_start();
require_once __DIR__ . '/../config.php';

if (empty($_SESSION['admin'])) { header('Location: index.php'); exit; }

$id = (int)($_GET['id'] ?? 0);
$articlesFile = DATA_DIR . '/articles.json';
$usersFile = DATA_DIR . '/abonnes.json';
$articles = file_exists($articlesFile) ? (json_decode(file_get_contents($articlesFile), true) ?: []) : [];
$users = file_exists($usersFile) ? (json_decode(file_get_contents($usersFile), true) ?: []) : [];

$article = null;
foreach ($articles as $a) {
    if ($a['id'] === $id) { $article = $a; break; }
}

if (!$article) { header('Location: index.php'); exit; }

$sent = 0;
$errors = 0;

foreach ($users as $u) {
    if (!empty($u['accept_actualites']) && !empty($u['email'])) {
        $to = $u['email'];
        $subject = '[' . $site_name . '] ' . $article['title'];
        $body = "Bonjour " . ($u['prenom'] ?? '') . ",\n\n"
              . strip_tags($article['content']) . "\n\n"
              . "Lire l'article en ligne : " . $site_url . "/index.php?p=vie-locale\n\n"
              . "---\n"
              . $site_name . " | " . $site_url . "\n"
              . "Pour ne plus recevoir ces emails, connectez-vous sur votre compte et décochez « Actualités ».";

        if (sendMail($to, $subject, $body)) {
            $sent++;
        } else {
            $errors++;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Envoi article | Administration</title>
    <link rel="stylesheet" href="../assets/fonts/fontawesome.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-bar { background: var(--green-900); color: white; padding: 0.75rem 0; font-size: 0.9rem; }
        .admin-bar a { color: var(--gold); }
    </style>
</head>
<body>
    <div class="admin-bar">
        <div class="container" style="display:flex;justify-content:space-between;align-items:center;">
            <span><i class="fas fa-shield-alt"></i> Envoi d'article</span>
            <a href="index.php" style="color:var(--gold);"><i class="fas fa-arrow-left"></i> Retour</a>
        </div>
    </div>
    <main style="padding: 2rem 0;">
        <div class="container" style="max-width:500px;">
            <?php if ($sent > 0): ?>
            <div style="background:var(--green-100);color:var(--green-700);padding:1.5rem;border-radius:var(--radius);text-align:center;">
                <i class="fas fa-check-circle" style="font-size:2rem;margin-bottom:0.75rem;"></i>
                <h3>Envoi terminé</h3>
                <p style="margin-top:0.5rem;"><strong><?= $sent ?></strong> email(s) envoyé(s) avec succès.</p>
                <?php if ($errors > 0): ?>
                <p style="color:#b91c1c;margin-top:0.5rem;"><strong><?= $errors ?></strong> échec(s).</p>
                <?php endif; ?>
                <a href="index.php" class="btn btn-primary" style="margin-top:1rem;">Retour</a>
            </div>
            <?php else: ?>
            <div style="text-align:center;padding:2rem;color:var(--gray-400);">
                <i class="fas fa-info-circle" style="font-size:2rem;margin-bottom:0.75rem;"></i>
                <p>Aucun abonné aux actualités.</p>
                <a href="index.php" class="btn" style="margin-top:1rem;">Retour</a>
            </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
