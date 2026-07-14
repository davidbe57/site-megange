<?php
session_start();
require_once __DIR__ . '/../config.php';
if (empty($_SESSION['admin'])) { header('Location: index.php'); exit; }

$file = DATA_DIR . '/gallery.json';
$items = file_exists($file) ? (json_decode(file_get_contents($file), true) ?: []) : [];

$error = '';
$success = '';

if (isset($_GET['del'])) {
    $delId = (int)$_GET['del'];
    foreach ($items as $e) {
        if ($e['id'] === $delId && !empty($e['image'])) {
            $pp = strpos($e['image'], 'serve.php?f=') === 0 ? UPLOADS_DIR . '/' . substr($e['image'], 12) : null;
            if ($pp && file_exists($pp)) unlink($pp);
            break;
        }
    }
    $items = array_values(array_filter($items, fn($v) => $v['id'] !== $delId));
    file_put_contents($file, json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    header('Location: galerie.php?deleted=1');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $label = trim($_POST['label'] ?? '');
    $link = trim($_POST['link'] ?? '');
    if (empty($_FILES['image']['name']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        $error = 'Veuillez choisir une image.';
    } elseif ($label === '') {
        $error = 'Le libellé est obligatoire.';
    } else {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg','jpeg','png','webp'])) {
            $error = 'Format non accepté (jpg, png, webp).';
        } else {
            $fn = 'gallery_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], UPLOADS_DIR . '/gallery/' . $fn);
            $maxId = 0;
            foreach ($items as $e) { if ($e['id'] > $maxId) $maxId = $e['id']; }
            $items[] = [
                'id' => $maxId + 1,
                'image' => 'serve.php?f=gallery/' . $fn,
                'label' => $label,
                'link' => $link,
            ];
            file_put_contents($file, json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            $success = 'Photo ajoutée à la galerie.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galerie photos | Administration</title>
    <link rel="stylesheet" href="../assets/fonts/fontawesome.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>.gal-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:1rem;margin-top:1rem;}.gal-item{background:var(--bg-card);border-radius:var(--radius-sm);overflow:hidden;border:1px solid var(--border);}.gal-item img{width:100%;height:140px;object-fit:cover;display:block;}.gal-item .info{padding:.5rem;font-size:.85rem;}.gal-item .info .actions{display:flex;justify-content:space-between;align-items:center;margin-top:.3rem;}.gal-item .info .actions a{color:#b91c1c;}</style>
</head>
<body>
    <div class="admin-bar">
        <div class="container" style="display:flex;justify-content:space-between;align-items:center;">
            <span><i class="fas fa-shield-alt"></i> Galerie photos</span>
            <a href="index.php" style="color:var(--gold);"><i class="fas fa-arrow-left"></i> Retour</a>
        </div>
    </div>
    <main style="padding:2rem 0;">
        <div class="container" style="max-width:800px;">
            <h2 style="margin-bottom:1.5rem;">Ajouter une photo</h2>
            <?php if ($error): ?><div style="background:#fef2f2;color:#b91c1c;padding:0.75rem 1rem;border-radius:var(--radius-sm);margin-bottom:1rem;"><?= $error ?></div><?php endif; ?>
            <?php if ($success): ?><div style="background:#ecfdf5;color:#065f46;padding:0.75rem 1rem;border-radius:var(--radius-sm);margin-bottom:1rem;"><?= $success ?></div><?php endif; ?>
            <form method="POST" enctype="multipart/form-data" style="margin-bottom:2rem;padding:1.5rem;background:var(--bg-card);border-radius:var(--radius);border:1px solid var(--border);">
                <div class="form-group">
                    <label for="image">Image</label>
                    <input type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.webp" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="label">Libellé</label>
                    <input type="text" id="label" name="label" class="form-control" required placeholder="Ex: L'église Saint-Martin">
                </div>
                <div class="form-group">
                    <label for="link">Lien (optionnel)</label>
                    <input type="url" id="link" name="link" class="form-control" placeholder="https://...">
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Ajouter</button>
            </form>

            <h2 style="margin-bottom:1rem;">Photos existantes</h2>
            <?php if (empty($items)): ?>
            <p style="color:var(--gray-400);">Aucune photo. Les photos par défaut s'afficheront.</p>
            <?php else: ?>
            <div class="gal-grid">
                <?php foreach ($items as $e): ?>
                <div class="gal-item">
                    <img src="<?= htmlspecialchars(fileUrl($e['image'])) ?>" alt="<?= htmlspecialchars($e['label']) ?>">
                    <div class="info">
                        <strong><?= htmlspecialchars($e['label']) ?></strong>
                        <?php if (!empty($e['link'])): ?><br><small style="color:var(--gray-400);"><?= htmlspecialchars($e['link']) ?></small><?php endif; ?>
                        <div class="actions">
                            <span style="font-size:.75rem;color:var(--gray-400);">#<?= $e['id'] ?></span>
                            <a href="galerie.php?del=<?= $e['id'] ?>" onclick="return confirm('Supprimer <?= htmlspecialchars($e['label']) ?> ?')"><i class="fas fa-trash"></i></a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
