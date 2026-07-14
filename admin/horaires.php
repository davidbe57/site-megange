<?php
session_start();
require_once __DIR__ . '/../config.php';
if (empty($_SESSION['admin'])) { header('Location: index.php'); exit; }

$file = DATA_DIR . '/mairie_hours.json';
$data = $mairie_hours;
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $out = [];
    foreach ($mairieDays as $d) {
        $am = trim($_POST['h_' . $d . '_am'] ?? '');
        $pm = trim($_POST['h_' . $d . '_pm'] ?? '');
        $slots = [];
        if ($am !== '') $slots[] = $am;
        if ($pm !== '') $slots[] = $pm;
        $out[$d] = $slots;
    }
    file_put_contents($file, json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    $data = $out;
    $success = 'Horaires mis à jour.';
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
    <style>.hours-grid{display:grid;grid-template-columns:200px 1fr 1fr;gap:.75rem;align-items:center;padding:.75rem 0;border-bottom:1px solid var(--border);}.hours-grid h4{margin:0;}.hours-grid label{font-size:.85rem;color:var(--gray-400);}input.hours-input{width:100%;}</style>
</head>
<body>
    <div class="admin-bar">
        <div class="container" style="display:flex;justify-content:space-between;align-items:center;">
            <span><i class="fas fa-shield-alt"></i> Horaires d'ouverture</span>
            <a href="index.php" style="color:var(--gold);"><i class="fas fa-arrow-left"></i> Retour</a>
        </div>
    </div>
    <main style="padding:2rem 0;">
        <div class="container" style="max-width:700px;">
            <h2 style="margin-bottom:1.5rem;">Horaires de la mairie</h2>
            <p style="font-size:.9rem;color:var(--gray-400);margin-bottom:1.5rem;">Laissez vide pour les créneaux où la mairie est fermée.</p>
            <?php if ($success): ?><div style="background:#ecfdf5;color:#065f46;padding:0.75rem 1rem;border-radius:var(--radius-sm);margin-bottom:1rem;"><?= $success ?></div><?php endif; ?>
            <form method="POST">
                <div style="display:grid;grid-template-columns:200px 1fr 1fr;gap:.75rem;font-weight:600;font-size:.85rem;color:var(--gray-400);padding-bottom:.5rem;border-bottom:2px solid var(--green-500);margin-bottom:.5rem;">
                    <span>Jour</span>
                    <span>Matin</span>
                    <span>Après-midi</span>
                </div>
                <?php foreach ($mairieDays as $d):
                    $slots = $data[$d] ?? [];
                ?>
                <div class="hours-grid">
                    <h4><?= $d ?></h4>
                    <div><input type="text" class="form-control hours-input" name="h_<?= $d ?>_am" value="<?= htmlspecialchars($slots[0] ?? '') ?>" placeholder="09:00 - 12:00"></div>
                    <div><input type="text" class="form-control hours-input" name="h_<?= $d ?>_pm" value="<?= htmlspecialchars($slots[1] ?? '') ?>" placeholder="14:00 - 17:00"></div>
                </div>
                <?php endforeach; ?>
                <button type="submit" class="btn btn-primary" style="margin-top:1.5rem;"><i class="fas fa-save"></i> Enregistrer</button>
            </form>
        </div>
    </main>
</body>
</html>
