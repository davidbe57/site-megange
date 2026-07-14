<?php
session_start();
require_once __DIR__ . '/../config.php';

if (empty($_SESSION['admin'])) { header('Location: index.php'); exit; }

$file = DATA_DIR . '/abonnes.json';
$abonnes = file_exists($file) ? (json_decode(file_get_contents($file), true) ?: []) : [];

// Suppression
if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    $abonnes = array_values(array_filter($abonnes, fn($a) => $a['id'] !== $delId));
    file_put_contents($file, json_encode($abonnes, JSON_PRETTY_PRINT));
    header('Location: abonnes.php?deleted=1');
    exit;
}

// Ajout / Modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $entry = [
        'id' => $id ?: (count($abonnes) ? max(array_column($abonnes, 'id')) + 1 : 1),
        'nom' => trim($_POST['nom'] ?? ''),
        'prenom' => trim($_POST['prenom'] ?? ''),
        'adresse' => trim($_POST['adresse'] ?? ''),
        'telephone' => trim($_POST['telephone'] ?? ''),
        'accept_bulletin' => isset($_POST['accept_bulletin']) ? true : false,
    ];
    if ($entry['nom'] === '' || $entry['prenom'] === '') {
        $error = 'Le nom et le prénom sont obligatoires.';
    } else {
        if ($id) {
            foreach ($abonnes as &$a) { if ($a['id'] === $id) { $a = $entry; break; } unset($a); }
        } else {
            $abonnes[] = $entry;
        }
        file_put_contents($file, json_encode($abonnes, JSON_PRETTY_PRINT));
        header('Location: abonnes.php?' . ($id ? 'updated=1' : 'created=1'));
        exit;
    }
}

$editAbonne = null;
if (isset($_GET['edit'])) {
    $eid = (int)$_GET['edit'];
    foreach ($abonnes as $a) { if ($a['id'] === $eid) { $editAbonne = $a; break; } }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Abonnés bulletin | Administration</title>
    <link rel="stylesheet" href="../assets/fonts/fontawesome.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-bar { background: var(--green-900); color: white; padding: 0.75rem 0; font-size: 0.9rem; }
        .admin-bar a { color: var(--gold); }
        .admin-table { width: 100%; border-collapse: collapse; background: var(--bg-card); border-radius: var(--radius); overflow: hidden; }
        .admin-table th, .admin-table td { padding: 0.75rem 1rem; text-align: left; border-bottom: 1px solid var(--border); }
        .admin-table th { background: var(--green-100); font-weight: 600; }
        .admin-table tr:hover { background: var(--gray-100); }
        .admin-actions { display: flex; gap: 0.5rem; }
        .admin-actions a, .admin-actions button { padding: 0.4rem 0.75rem; border-radius: var(--radius-sm); font-size: 0.85rem; text-decoration: none; border: none; cursor: pointer; }
        .toast { display: none; background: var(--green-100); color: var(--green-700); padding: 1rem 1.5rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem; font-weight: 600; }
        .toast.show { display: block; }
        .form-card { max-width: 500px; background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius); padding: 1.5rem; margin-bottom: 2rem; }
        .form-card label { display: block; margin-bottom: 0.25rem; font-weight: 600; font-size: 0.9rem; }
        .form-card .required::after { content: ' *'; color: var(--terracotta); }
        .form-card input[type="text"], .form-card input[type="tel"] { width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.95rem; }
        .form-card .form-group { margin-bottom: 1rem; }
        .checkbox-group { display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem; }
        .checkbox-group input[type="checkbox"] { width: 1.1rem; height: 1.1rem; }
    </style>
</head>
<body>
    <div class="admin-bar">
        <div class="container" style="display:flex;justify-content:space-between;align-items:center;">
            <span><i class="fas fa-shield-alt"></i> Abonnés au bulletin communal</span>
            <a href="index.php" style="color:var(--gold);"><i class="fas fa-arrow-left"></i> Retour admin</a>
        </div>
    </div>
    <main style="padding: 2rem 0;">
        <div class="container">
            <?php if (isset($_GET['created'])): ?><div class="toast show">Abonné ajouté</div><?php endif; ?>
            <?php if (isset($_GET['updated'])): ?><div class="toast show">Abonné modifié</div><?php endif; ?>
            <?php if (isset($_GET['deleted'])): ?><div class="toast show">Abonné supprimé</div><?php endif; ?>
            <?php if (!empty($error)): ?><div style="background:#fef2f2;color:#b91c1c;padding:0.75rem 1rem;border-radius:var(--radius-sm);margin-bottom:1rem;"><?= $error ?></div><?php endif; ?>

            <div class="form-card">
                <h3 style="margin-bottom:1rem;"><?= $editAbonne ? 'Modifier' : 'Ajouter' ?> un abonné</h3>
                <form method="POST">
                    <?php if ($editAbonne): ?><input type="hidden" name="id" value="<?= $editAbonne['id'] ?>"><?php endif; ?>
                    <div class="form-group">
                        <label class="required">Nom</label>
                        <input type="text" name="nom" value="<?= htmlspecialchars($editAbonne['nom'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="required">Prénom</label>
                        <input type="text" name="prenom" value="<?= htmlspecialchars($editAbonne['prenom'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Adresse</label>
                        <input type="text" name="adresse" value="<?= htmlspecialchars($editAbonne['adresse'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Téléphone</label>
                        <input type="tel" name="telephone" value="<?= htmlspecialchars($editAbonne['telephone'] ?? '') ?>">
                    </div>
                    <div class="checkbox-group">
                        <input type="checkbox" name="accept_bulletin" id="accept_bulletin"<?= ($editAbonne['accept_bulletin'] ?? false) ? ' checked' : '' ?>>
                        <label for="accept_bulletin" style="margin:0;font-weight:400;">Accepte de recevoir le bulletin communal par email</label>
                    </div>
                    <button type="submit" class="btn btn-primary"><?= $editAbonne ? 'Enregistrer' : 'Ajouter' ?></button>
                    <?php if ($editAbonne): ?><a href="abonnes.php" class="btn" style="margin-left:0.5rem;">Annuler</a><?php endif; ?>
                </form>
            </div>

            <?php if (empty($abonnes)): ?>
                <div style="text-align:center;padding:3rem;color:var(--gray-400);">
                    <i class="fas fa-users" style="font-size:3rem;margin-bottom:1rem;"></i>
                    <p>Aucun abonné pour le moment.</p>
                </div>
            <?php else: ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Adresse</th>
                            <th>Téléphone</th>
                            <th>Bulletin</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($abonnes as $a): ?>
                        <tr>
                            <td><?= htmlspecialchars($a['nom']) ?></td>
                            <td><?= htmlspecialchars($a['prenom']) ?></td>
                            <td><?= htmlspecialchars($a['adresse'] ?? '') ?></td>
                            <td><?= htmlspecialchars($a['telephone'] ?? '') ?></td>
                            <td><?= ($a['accept_bulletin'] ?? false) ? '<i class="fas fa-check" style="color:var(--green-600);"></i>' : '<i class="fas fa-times" style="color:var(--gray-300);"></i>' ?></td>
                            <td>
                                <div class="admin-actions">
                                    <a href="abonnes.php?edit=<?= $a['id'] ?>" style="background:var(--green-100);color:var(--green-700);"><i class="fas fa-edit"></i></a>
                                    <a href="abonnes.php?delete=<?= $a['id'] ?>" style="background:#fef2f2;color:#b91c1c;" onclick="return confirm('Supprimer <?= htmlspecialchars($a['prenom'] . ' ' . $a['nom']) ?> ?')"><i class="fas fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
