<?php
session_start();
require_once __DIR__ . '/../config.php';
if (empty($_SESSION['admin'])) { header('Location: index.php'); exit; }

$file = __DIR__ . '/../data/conseil.json';
$data = file_exists($file) ? (json_decode(file_get_contents($file), true) ?: []) : [];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $next_date = $_POST['next_date'] ?? '';
    $next_time = $_POST['next_time'] ?? '';
    $next_location = $_POST['next_location'] ?? '';
    $councilors = (int)($_POST['councilors'] ?? 11);
    $next_election = (int)($_POST['next_election'] ?? 2026);

    if ($next_date === '' || $next_time === '' || $next_location === '') {
        $error = 'Veuillez remplir tous les champs.';
    } else {
        $data = [
            'next_date' => $next_date,
            'next_time' => $next_time,
            'next_location' => $next_location,
            'councilors' => $councilors,
            'next_election' => $next_election,
        ];
        file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        $success = 'Informations mises à jour.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conseil municipal | Administration</title>
    <link rel="stylesheet" href="../assets/fonts/fontawesome.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="admin-bar">
        <div class="container" style="display:flex;justify-content:space-between;align-items:center;">
            <span><i class="fas fa-shield-alt"></i> Conseil municipal</span>
            <a href="index.php" style="color:var(--gold);"><i class="fas fa-arrow-left"></i> Retour</a>
        </div>
    </div>
    <main style="padding:2rem 0;">
        <div class="container" style="max-width:600px;">
            <h2 style="margin-bottom:1.5rem;">Prochain conseil & sidebar</h2>
            <?php if ($error): ?><div style="background:#fef2f2;color:#b91c1c;padding:0.75rem 1rem;border-radius:var(--radius-sm);margin-bottom:1rem;"><?= $error ?></div><?php endif; ?>
            <?php if ($success): ?><div style="background:#ecfdf5;color:#065f46;padding:0.75rem 1rem;border-radius:var(--radius-sm);margin-bottom:1rem;"><?= $success ?></div><?php endif; ?>
            <form method="POST">
                <div class="form-group">
                    <label for="next_date">Date du prochain conseil</label>
                    <input type="date" id="next_date" name="next_date" class="form-control" value="<?= htmlspecialchars($data['next_date'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="next_time">Horaire</label>
                    <input type="text" id="next_time" name="next_time" class="form-control" value="<?= htmlspecialchars($data['next_time'] ?? '20h00') ?>" required>
                </div>
                <div class="form-group">
                    <label for="next_location">Lieu</label>
                    <input type="text" id="next_location" name="next_location" class="form-control" value="<?= htmlspecialchars($data['next_location'] ?? 'Salle du conseil') ?>" required>
                </div>
                <hr style="margin:1.5rem 0;border-color:var(--border);">
                <h3 style="margin-bottom:1rem;">Vos élus</h3>
                <div class="form-group">
                    <label for="councilors">Nombre de conseillers</label>
                    <input type="number" id="councilors" name="councilors" class="form-control" value="<?= (int)($data['councilors'] ?? 11) ?>" required min="1" max="50">
                </div>
                <div class="form-group">
                    <label for="next_election">Prochaine élection (année)</label>
                    <input type="number" id="next_election" name="next_election" class="form-control" value="<?= (int)($data['next_election'] ?? 2026) ?>" required min="2020" max="2050">
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Enregistrer</button>
            </form>
        </div>
    </main>
</body>
</html>
