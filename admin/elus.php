<?php
session_start();
require_once __DIR__ . '/../config.php';
if (empty($_SESSION['admin'])) { header('Location: index.php'); exit; }

$file = DATA_DIR . '/elus.json';
$items = file_exists($file) ? (json_decode(file_get_contents($file), true) ?: []) : [];

if (isset($_GET['del'])) {
    $delId = (int)$_GET['del'];
    foreach ($items as $e) {
        if ($e['id'] === $delId && !empty($e['photo'])) {
            $pp = strpos($e['photo'], 'serve.php?f=') === 0 ? UPLOADS_DIR . '/' . substr($e['photo'], 12) : null;
            if ($pp && file_exists($pp)) unlink($pp);
            break;
        }
    }
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
        $delegation1 = trim($_POST['delegation1'] ?? '');
        $delegation2 = trim($_POST['delegation2'] ?? '');
        $delegation3 = trim($_POST['delegation3'] ?? '');
        $delegation4 = trim($_POST['delegation4'] ?? '');
        if ($name === '' || $role === '') {
            $error = 'Le nom et le rôle sont obligatoires.';
        } else {
            $photo = '';
            if (!empty($_FILES['photo']['name']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg','jpeg','png','webp'])) {
                    $fn = 'elu_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                    move_uploaded_file($_FILES['photo']['tmp_name'], UPLOADS_DIR . '/elus/' . $fn);
                    $photo = 'serve.php?f=elus/' . $fn;
                }
            }
            if ($action === 'add') {
                $maxId = 0;
                foreach ($items as $e) { if ($e['id'] > $maxId) $maxId = $e['id']; }
                $items[] = ['id' => $maxId + 1, 'name' => $name, 'role' => $role, 'delegation1' => $delegation1, 'delegation2' => $delegation2, 'delegation3' => $delegation3, 'delegation4' => $delegation4, 'photo' => $photo];
            } else {
                $editId = (int)($_POST['id'] ?? 0);
                foreach ($items as &$e) {
                    if ($e['id'] === $editId) {
                        $e['name'] = $name; $e['role'] = $role; $e['delegation1'] = $delegation1; $e['delegation2'] = $delegation2; $e['delegation3'] = $delegation3; $e['delegation4'] = $delegation4;
                        if ($photo) {
                            if (!empty($e['photo'])) {
                                $pp = strpos($e['photo'], 'serve.php?f=') === 0 ? UPLOADS_DIR . '/' . substr($e['photo'], 12) : null;
                                if ($pp && file_exists($pp)) unlink($pp);
                            }
                            $e['photo'] = $photo;
                        }
                        break;
                    }
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
            <form method="POST" enctype="multipart/form-data" style="margin-bottom:2rem;padding:1.5rem;background:var(--bg-card);border-radius:var(--radius);border:1px solid var(--border);">
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
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    <div class="form-group">
                        <label for="delegation1">Commission 1</label>
                        <input type="text" id="delegation1" name="delegation1" class="form-control" value="<?= $editItem ? htmlspecialchars($editItem['delegation1'] ?? '') : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="delegation2">Commission 2</label>
                        <input type="text" id="delegation2" name="delegation2" class="form-control" value="<?= $editItem ? htmlspecialchars($editItem['delegation2'] ?? '') : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="delegation3">Commission 3</label>
                        <input type="text" id="delegation3" name="delegation3" class="form-control" value="<?= $editItem ? htmlspecialchars($editItem['delegation3'] ?? '') : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="delegation4">Commission 4</label>
                        <input type="text" id="delegation4" name="delegation4" class="form-control" value="<?= $editItem ? htmlspecialchars($editItem['delegation4'] ?? '') : '' ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="photo">Photo (optionnelle, jpg/png/webp)</label>
                    <input type="file" id="photo" name="photo" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                    <?php if ($editItem && !empty($editItem['photo'])): ?>
                    <div style="margin-top:.5rem;"><img src="<?= htmlspecialchars(fileUrl($editItem['photo'])) ?>" style="height:80px;width:80px;object-fit:cover;border-radius:50%;"></div>
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> <?= $editItem ? 'Modifier' : 'Ajouter' ?></button>
                <?php if ($editItem): ?><a href="elus.php" class="btn" style="margin-left:.5rem;">Annuler</a><?php endif; ?>
            </form>

            <h3 style="margin-bottom:1rem;">Membres actuels</h3>
            <?php if (empty($items)): ?>
                <p style="color:var(--gray-400);">Aucun membre.</p>
            <?php else: ?>
                <table class="admin-table">
                    <thead><tr><th>Photo</th><th>Nom</th><th>Rôle</th><th>Commissions</th><th>Actions</th></tr></thead>
                    <tbody>
                        <?php foreach ($items as $e): ?>
                        <tr>
                            <td><?php if (!empty($e['photo'])): ?><img src="<?= htmlspecialchars(fileUrl($e['photo'])) ?>" style="width:40px;height:40px;object-fit:cover;border-radius:50%;"><?php else: ?><i class="fas fa-user" style="font-size:1.2rem;color:var(--gray-400);"></i><?php endif; ?></td>
                            <td><strong><?= htmlspecialchars($e['name']) ?></strong></td>
                            <td><?= htmlspecialchars($e['role']) ?></td>
                            <td style="font-size:0.85rem;"><?php
                                $coms = [];
                                for ($i = 1; $i <= 4; $i++) {
                                    $k = 'delegation' . $i;
                                    if (!empty($e[$k])) $coms[] = htmlspecialchars($e[$k]);
                                }
                                echo implode('<br>', $coms) ?: '-';
                            ?></td>
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
