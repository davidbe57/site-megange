<?php
session_start();
require_once __DIR__ . '/../config.php';
if (empty($_SESSION['admin'])) { header('Location: index.php'); exit; }

$file = DATA_DIR . '/carousel.json';
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
    header('Location: carousel.php?deleted=1');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['images'])) {
    $names = $_FILES['images']['name'];
    $tmp = $_FILES['images']['tmp_name'];
    $errs = $_FILES['images']['error'];
    $maxId = 0;
    foreach ($items as $e) { if ($e['id'] > $maxId) $maxId = $e['id']; }
    $uploaded = 0;
    for ($i = 0; $i < count($names); $i++) {
        if ($errs[$i] !== UPLOAD_ERR_OK) continue;
        if (count($items) + $uploaded >= 5) { $error = 'Maximum 5 photos.'; break; }
        $ext = strtolower(pathinfo($names[$i], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg','jpeg','png','webp'])) continue;
        $fn = 'carousel_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        move_uploaded_file($tmp[$i], UPLOADS_DIR . '/carousel/' . $fn);
        $items[] = ['id' => $maxId + 1 + $uploaded, 'image' => 'serve.php?f=carousel/' . $fn];
        $uploaded++;
    }
    if ($uploaded) {
        file_put_contents($file, json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        $success = $uploaded . ' photo(s) ajoutée(s).';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carousel | Administration</title>
    <link rel="stylesheet" href="../assets/fonts/fontawesome.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>.carousel-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:1rem;margin-top:1rem;}.carousel-item{background:var(--bg-card);border-radius:var(--radius-sm);overflow:hidden;border:1px solid var(--border);}.carousel-item img{width:100%;height:120px;object-fit:cover;display:block;}.carousel-item .info{padding:.5rem;display:flex;justify-content:space-between;align-items:center;font-size:.8rem;}.carousel-item .info a{color:#b91c1c;}</style>
</head>
<body>
    <div class="admin-bar">
        <div class="container" style="display:flex;justify-content:space-between;align-items:center;">
            <span><i class="fas fa-shield-alt"></i> Carousel (accueil)</span>
            <a href="index.php" style="color:var(--gold);"><i class="fas fa-arrow-left"></i> Retour</a>
        </div>
    </div>
    <main style="padding:2rem 0;">
        <div class="container" style="max-width:800px;">
            <h2 style="margin-bottom:1.5rem;">Photos du carousel</h2>
            <?php if ($error): ?><div style="background:#fef2f2;color:#b91c1c;padding:0.75rem 1rem;border-radius:var(--radius-sm);margin-bottom:1rem;"><?= $error ?></div><?php endif; ?>
            <?php if ($success): ?><div style="background:#ecfdf5;color:#065f46;padding:0.75rem 1rem;border-radius:var(--radius-sm);margin-bottom:1rem;"><?= $success ?></div><?php endif; ?>
            <?php if (count($items) < 5): ?>
            <form method="POST" enctype="multipart/form-data" style="margin-bottom:2rem;padding:1.5rem;background:var(--bg-card);border-radius:var(--radius);border:1px solid var(--border);">
                <p style="margin-bottom:1rem;">Ajouter des photos (<?= count($items) ?>/5, maximum 5).</p>
                <input type="file" name="images[]" multiple accept=".jpg,.jpeg,.png,.webp" class="form-control" style="margin-bottom:1rem;">
                <button type="submit" class="btn btn-primary"><i class="fas fa-upload"></i> Ajouter</button>
            </form>
            <?php else: ?>
            <p style="color:var(--gray-400);margin-bottom:1rem;">Maximum 5 photos atteint. Supprimez-en une pour en ajouter.</p>
            <?php endif; ?>
            <?php if (empty($items)): ?>
            <p style="color:var(--gray-400);">Aucune photo. Les photos par défaut s'afficheront.</p>
            <?php else: ?>
            <div class="carousel-grid">
                <?php foreach ($items as $e): ?>
                <div class="carousel-item">
                    <img src="<?= htmlspecialchars(fileUrl($e['image'])) ?>" alt="">
                    <div class="info">
                        <span>#<?= $e['id'] ?></span>
                        <a href="carousel.php?del=<?= $e['id'] ?>" onclick="return confirm('Supprimer cette photo ?')"><i class="fas fa-trash"></i></a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
