<?php
session_start();
require_once __DIR__ . '/../config.php';
if (empty($_SESSION['admin'])) { header('Location: index.php'); exit; }

$file = DATA_DIR . '/elus.json';
$items = file_exists($file) ? (json_decode(file_get_contents($file), true) ?: []) : [];

// Handle delete
if (isset($_GET['del'])) {
    $delId = (int)$_GET['del'];
    $items = array_values(array_filter($items, fn($e) => $e['id'] !== $delId));
    file_put_contents($file, json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    header('Location: elus.php?deleted=1');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add' || $action === 'edit') {
        $name = trim($_POST['name'] ?? '');
        $role = trim($_POST['role'] ?? '');
        $delegation = trim($_POST['delegation'] ?? '');

        if ($name === '' || $role === '') {
            $error = 'Le nom et le rôle sont obligatoires.';
        } else {
            if ($action === 'add') {
                $maxId = 0;
                foreach ($items as $e) { if ($e['id'] > $maxId) $maxId = $e['id']; }
                $items[] = ['id' => $maxId + 1, 'name' => $name, 'role' => $role, 'delegation' => $delegation];
            } else {
                $editId = (int)($_POST['id'] ?? 0);
                foreach ($items as &$e) {
                    if ($e['id'] === $editId) { $e['name'] = $name; $e['role'] = $role; $e['delegation'] = $delegation; break; }
                }
                unset($e);
            }
            file_put_contents($file, json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            $success = $action === 'add' ? 'Membre ajouté.' : 'Membre modifié.';
        }
    }
}

$editItem = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    foreach ($items as $e) { if ($e['id'] === $editId) { $editItem = $e; break; } }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Équipe municipale | Administration</title>
    <link rel="stylesheet" href="../assets/fonts/fontawesome.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="admin-bar">
        <div class="container" style="display:flex;justify-content:space-between;align-items:center;">
            <span><i class="fas fa-shield-alt"></i> Équipe municipale</span>
            <a href="index.php" style="color:var(--gold);"><i class="fas fa-arrow-left"></i> Retour</a>
        </div>
    </div>
    <main style="padding:2rem 0;">
        <div class="container" style="max-width:700px;">
            <h2 style="margin-bottom:1.5rem;"><?= $editItem ? 'Modifier' : 'Ajouter' ?> un élu</h2>
            <?php if ($error): ?><div style="background:#fef2f2;color:#b91c1c;padding:0.75rem 1rem;border-radius:var(--radius-sm);margin-bottom:1rem;"><?= $error ?></div><?php endif; ?>
            <?php if ($success): ?><div style="background:#ecfdf5;color:#065f46;padding:0.75rem 1rem;border-radius:var(--radius-sm);margin-bottom:1rem;"><?= $success ?></div><?php endif; ?>
            <form method="POST" style="margin-bottom:2rem;padding:1.5rem;background:var(--bg-card);border-radius:var(--radius);border:1px solid var(--border);">
                <input type="hidden" name="action" value="<?= $editItem ? 'edit' : 'add' ?>">
                <?php if ($editItem): ?><input type="hidden" name="id" value="<?= $editItem['id'] ?>"><?php endif; ?>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    <div class="form-group">
                        <label for="name">Nom</label>
                        <input type="text" id="name" name="name" class="form-control" value="<?= $editItem ? htmlspecialchars($editItem['name']) : '' ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="role">Rôle</label>
                        <input type="text" id="role" name="role" class="form-control" value="<?= $editItem ? htmlspecialchars($editItem['role']) : '' ?>" required placeholder="Maire, Adjoint, Conseiller...">
                    </div>
                </div>
                <div class="form-group">
                    <label for="delegation">Délégation (optionnel)</label>
                    <input type="text" id="delegation" name="delegation" class="form-control" value="<?= $editItem ? htmlspecialchars($editItem['delegation']) : '' ?>" placeholder="Ex: Travaux et urbanisme">
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> <?= $editItem ? 'Modifier' : 'Ajouter' ?></button>
                <?php if ($editItem): ?><a href="elus.php" class="btn" style="margin-left:.5rem;">Annuler</a><?php endif; ?>
            </form>

            <h3 style="margin-bottom:1rem;">Membres actuels</h3>
            <?php if (empty($items)): ?>
                <p style="color:var(--gray-400);">Aucun membre.</p>
            <?php else: ?>
                <table class="admin-table">
                    <thead><tr><th>Nom</th><th>Rôle</th><th>Délégation</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php foreach ($items as $e): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($e['name']) ?></strong></td>
                            <td><?= htmlspecialchars($e['role']) ?></td>
                            <td><?= htmlspecialchars($e['delegation']) ?></td>
                            <td>
                                <div class="admin-actions">
                                    <a href="elus.php?edit=<?= $e['id'] ?>" style="background:var(--green-100);color:var(--green-700);"><i class="fas fa-edit"></i></a>
                                    <a href="elus.php?del=<?= $e['id'] ?>" style="background:#fef2f2;color:#b91c1c;" onclick="return confirm('Supprimer <?= htmlspecialchars($e['name']) ?> ?')"><i class="fas fa-trash"></i></a>
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
