<?php
session_start();
require_once __DIR__ . '/../config.php';
if (empty($_SESSION['admin'])) { header('Location: index.php'); exit; }

$file = DATA_DIR . '/mairie_hours.json';
$default = [
    'Lundi'     => '14h00 - 17h00',
    'Mardi'     => '9h00 - 12h00',
    'Mercredi'  => 'Fermé',
    'Jeudi'     => '14h00 - 17h00',
    'Vendredi'  => '9h00 - 12h00',
    'Samedi'    => '9h00 - 12h00 (1er du mois)',
    'Dimanche'  => 'Fermé',
];
$hours = file_exists($file) ? (json_decode(file_get_contents($file), true) ?: $default) : $default;

$saved = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newHours = [];
    $days = array_keys($default);
    foreach ($days as $day) {
        $newHours[$day] = trim($_POST[$day] ?? '');
    }
    file_put_contents($file, json_encode($newHours, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    $hours = $newHours;
    $saved = true;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Horaires d'ouverture | Administration</title>
    <link rel="stylesheet" href="../assets/fonts/fontawesome.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-bar { background: var(--green-900); color: white; padding: 0.75rem 0; font-size: 0.9rem; }
        .admin-bar a { color: var(--gold); }
        .toast { display: none; background: var(--green-100); color: var(--green-700); padding: 1rem 1.5rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem; font-weight: 600; }
        .toast.show { display: block; }
        .day-row { display: flex; align-items: center; gap: 1rem; margin-bottom: 0.75rem; padding: 0.75rem; background: var(--bg-card); border-radius: var(--radius-sm); border: 1px solid var(--border); }
        .day-row label { min-width: 120px; font-weight: 600; }
        .day-row input { flex: 1; }
        .days-container { max-width: 500px; }
    </style>
</head>
<body>
    <div class="admin-bar">
        <div class="container" style="display:flex;justify-content:space-between;align-items:center;">
            <span><i class="fas fa-shield-alt"></i> Horaires d'ouverture</span>
            <div>
                <a href="index.php"><i class="fas fa-arrow-left"></i> Tableau de bord</a>
                <a href="../index.php" style="margin-left:1rem;"><i class="fas fa-arrow-left"></i> Retour au site</a>
            </div>
        </div>
    </div>

    <main style="padding: 2rem 0;">
        <div class="container">
            <?php if ($saved): ?><div class="toast show">Horaires enregistrés</div><?php endif; ?>

            <h2 style="margin-bottom:1.5rem;">Horaires d'ouverture de la mairie</h2>
            <p style="color:var(--gray-400);margin-bottom:2rem;">Ces horaires sont affichés sur la page d'accueil et dans le sidebar de La commune.</p>

            <form method="POST">
                <div class="days-container">
                <?php foreach ($hours as $day => $time): ?>
                    <div class="day-row">
                        <label for="day_<?= $day ?>"><?= $day ?></label>
                        <input type="text" id="day_<?= $day ?>" name="<?= $day ?>" class="form-control" value="<?= htmlspecialchars($time) ?>" placeholder="ex: 14h00 - 17h00 ou Fermé">
                    </div>
                <?php endforeach; ?>
                </div>
                <button type="submit" class="btn btn-primary" style="margin-top:1rem;"><i class="fas fa-save"></i> Enregistrer</button>
                <a href="index.php" class="btn btn-outline" style="margin-top:1rem;">Annuler</a>
            </form>
        </div>
    </main>
</body>
</html>
