<?php
session_start();
require_once __DIR__ . '/../config.php';

if (empty($_SESSION['admin'])) { header('Location: index.php'); exit; }

$file = DATA_DIR . '/abonnes.json';
$users = file_exists($file) ? (json_decode(file_get_contents($file), true) ?: []) : [];

// Export CSV des abonnés au bulletin
if (isset($_GET['export'])) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="abonnes-bulletin.csv"');
    echo "\xEF\xBB\xBF"; // BOM UTF-8
    $out = fopen('php://output', 'w');
    fputcsv($out, ['Nom', 'Prénom', 'Email']);
    foreach ($users as $u) {
        if (!empty($u['accept_bulletin'])) {
            fputcsv($out, [$u['nom'], $u['prenom'], $u['email'] ?? '']);
        }
    }
    fclose($out);
    exit;
}

// Suppression
if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    $users = array_values(array_filter($users, fn($a) => $a['id'] !== $delId));
    file_put_contents($file, json_encode($users, JSON_PRETTY_PRINT));
    header('Location: abonnes.php?deleted=1');
    exit;
}

// Ajout / Modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $adresse = trim($_POST['adresse'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $accept_bulletin = isset($_POST['accept_bulletin']);

    if ($nom === '' || $prenom === '' || $email === '') {
        $error = 'Le nom, le prénom et l\'email sont obligatoires.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email invalide.';
    } else {
        // Vérifie email unique
        foreach ($users as $u) {
            if ($u['id'] !== $id && strtolower($u['email'] ?? '') === strtolower($email)) {
                $error = 'Cet email est déjà utilisé.';
                break;
            }
        }
    }

    if (empty($error)) {
        if ($id) {
            foreach ($users as &$u) {
                if ($u['id'] === $id) {
                    $u['nom'] = $nom;
                    $u['prenom'] = $prenom;
                    $u['email'] = $email;
                    $u['adresse'] = $adresse;
                    $u['telephone'] = $telephone;
                    $u['accept_bulletin'] = $accept_bulletin;
                    if ($password !== '') {
                        $u['password'] = password_hash($password, PASSWORD_DEFAULT);
                    }
                    break;
                }
            }
            unset($u);
        } else {
            $users[] = [
                'id' => count($users) ? max(array_column($users, 'id')) + 1 : 1,
                'nom' => $nom,
                'prenom' => $prenom,
                'email' => $email,
                'password' => $password !== '' ? password_hash($password, PASSWORD_DEFAULT) : '',
                'adresse' => $adresse,
                'telephone' => $telephone,
                'accept_bulletin' => $accept_bulletin,
            ];
        }
        file_put_contents($file, json_encode($users, JSON_PRETTY_PRINT));
        header('Location: abonnes.php?' . ($id ? 'updated=1' : 'created=1'));
        exit;
    }
}

$editUser = null;
if (isset($_GET['edit'])) {
    $eid = (int)$_GET['edit'];
    foreach ($users as $u) { if ($u['id'] === $eid) { $editUser = $u; break; } }
}

$nbBulletin = count(array_filter($users, fn($u) => !empty($u['accept_bulletin'])));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Utilisateurs | Administration</title>
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
        .form-card input[type="text"], .form-card input[type="tel"], .form-card input[type="email"], .form-card input[type="password"] { width: 100%; padding: 0.5rem; border: 1px solid var(--border); border-radius: var(--radius-sm); font-size: 0.95rem; box-sizing:border-box; }
        .form-card .form-group { margin-bottom: 1rem; }
        .checkbox-group { display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem; }
        .checkbox-group input[type="checkbox"] { width: 1.1rem; height: 1.1rem; }
        .stats-bar { display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:1rem;margin-bottom:2rem; }
        .stat-card { background:var(--bg-card);border-radius:var(--radius);padding:1rem;text-align:center;border:1px solid var(--border); }
        .stat-card .num { font-size:2rem;font-weight:700;color:var(--green-600); }
        .stat-card .lbl { font-size:0.85rem;color:var(--gray-500); }
    </style>
</head>
<body>
    <div class="admin-bar">
        <div class="container" style="display:flex;justify-content:space-between;align-items:center;">
            <span><i class="fas fa-shield-alt"></i> Gestion des utilisateurs</span>
            <div>
                <a href="abonnes.php?export=1" style="color:var(--gold);margin-right:1rem;"><i class="fas fa-download"></i> Export bulletin</a>
                <a href="index.php" style="color:var(--gold);"><i class="fas fa-arrow-left"></i> Retour</a>
            </div>
        </div>
    </div>
    <main style="padding: 2rem 0;">
        <div class="container">
            <?php if (isset($_GET['created'])): ?><div class="toast show">Utilisateur ajouté</div><?php endif; ?>
            <?php if (isset($_GET['updated'])): ?><div class="toast show">Utilisateur modifié</div><?php endif; ?>
            <?php if (isset($_GET['deleted'])): ?><div class="toast show">Utilisateur supprimé</div><?php endif; ?>
            <?php if (!empty($error)): ?><div style="background:#fef2f2;color:#b91c1c;padding:0.75rem 1rem;border-radius:var(--radius-sm);margin-bottom:1rem;"><?= htmlspecialchars($error) ?></div><?php endif; ?>

            <div class="stats-bar">
                <div class="stat-card"><div class="num"><?= count($users) ?></div><div class="lbl">Inscrits</div></div>
                <div class="stat-card"><div class="num"><?= $nbBulletin ?></div><div class="lbl">Acceptent le bulletin</div></div>
            </div>

            <?php if (!empty($editUser)): ?>
            <div class="form-card">
                <h3 style="margin-bottom:1rem;">Modifier l'utilisateur</h3>
                <form method="POST">
                    <input type="hidden" name="id" value="<?= $editUser['id'] ?>">
                    <div class="form-group">
                        <label class="required">Nom</label>
                        <input type="text" name="nom" value="<?= htmlspecialchars($editUser['nom']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="required">Prénom</label>
                        <input type="text" name="prenom" value="<?= htmlspecialchars($editUser['prenom']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="required">Email</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($editUser['email']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Nouveau mot de passe (laisser vide pour conserver)</label>
                        <input type="password" name="password">
                    </div>
                    <div class="form-group">
                        <label>Adresse</label>
                        <input type="text" name="adresse" value="<?= htmlspecialchars($editUser['adresse'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Téléphone</label>
                        <input type="tel" name="telephone" value="<?= htmlspecialchars($editUser['telephone'] ?? '') ?>">
                    </div>
                    <div class="checkbox-group">
                        <input type="checkbox" name="accept_bulletin" id="accept_bulletin"<?= ($editUser['accept_bulletin'] ?? false) ? ' checked' : '' ?>>
                        <label for="accept_bulletin" style="margin:0;font-weight:400;">Accepte le bulletin communal</label>
                    </div>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                    <a href="abonnes.php" class="btn" style="margin-left:0.5rem;">Annuler</a>
                </form>
            </div>
            <?php endif; ?>

            <?php if (empty($users)): ?>
                <div style="text-align:center;padding:3rem;color:var(--gray-400);">
                    <i class="fas fa-users" style="font-size:3rem;margin-bottom:1rem;"></i>
                    <p>Aucun utilisateur inscrit.</p>
                </div>
            <?php else: ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Email</th>
                            <th>Adresse</th>
                            <th>Téléphone</th>
                            <th>Bulletin</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $u): ?>
                        <tr>
                            <td><?= htmlspecialchars($u['nom']) ?></td>
                            <td><?= htmlspecialchars($u['prenom']) ?></td>
                            <td><?= htmlspecialchars($u['email'] ?? '') ?></td>
                            <td><?= htmlspecialchars($u['adresse'] ?? '') ?></td>
                            <td><?= htmlspecialchars($u['telephone'] ?? '') ?></td>
                            <td><?= (!empty($u['accept_bulletin'])) ? '<i class="fas fa-check" style="color:var(--green-600);"></i>' : '<i class="fas fa-times" style="color:var(--gray-300);"></i>' ?></td>
                            <td>
                                <div class="admin-actions">
                                    <a href="abonnes.php?edit=<?= $u['id'] ?>" style="background:var(--green-100);color:var(--green-700);"><i class="fas fa-edit"></i></a>
                                    <a href="abonnes.php?delete=<?= $u['id'] ?>" style="background:#fef2f2;color:#b91c1c;" onclick="return confirm('Supprimer <?= htmlspecialchars($u['prenom'] . ' ' . $u['nom']) ?> ?')"><i class="fas fa-trash"></i></a>
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
