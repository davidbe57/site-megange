<?php
session_start();
require_once __DIR__ . '/../config.php';
if (empty($_SESSION['admin'])) { header('Location: index.php'); exit; }

$file = __DIR__ . '/../data/comptes_rendus.json';
$items = file_exists($file) ? (json_decode(file_get_contents($file), true) ?: []) : [];
$id = (int)($_GET['id'] ?? 0);
$index = null;
foreach ($items as $k => $v) { if ($v['id'] === $id) { $index = $k; break; } }
if ($index === null) { header('Location: comptes_rendus.php'); exit; }

$cr = $items[$index];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $date  = $_POST['date'] ?? '';
    $pdf   = $_FILES['pdf'] ?? null;
    $thumb = $_FILES['thumbnail'] ?? null;

    if ($title === '' || $date === '') {
        $error = 'Le titre et la date sont obligatoires.';
    } else {
        $cr['title'] = $title;
        $cr['date']  = $date;

        if ($pdf && $pdf['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($pdf['name'], PATHINFO_EXTENSION));
            if ($ext === 'pdf') {
                if (file_exists(__DIR__ . '/../' . $cr['file'])) unlink(__DIR__ . '/../' . $cr['file']);
                $pdfName = 'cr_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.pdf';
                move_uploaded_file($pdf['tmp_name'], __DIR__ . '/../assets/pdf/' . $pdfName);
                $cr['file'] = 'assets/pdf/' . $pdfName;
            }
        }

        if ($thumb && $thumb['error'] === UPLOAD_ERR_OK) {
            $tExt = strtolower(pathinfo($thumb['name'], PATHINFO_EXTENSION));
            if (in_array($tExt, ['jpg','jpeg','png','webp'])) {
                if (!empty($cr['thumbnail']) && file_exists(__DIR__ . '/../' . $cr['thumbnail'])) unlink(__DIR__ . '/../' . $cr['thumbnail']);
                $tName = 'cr_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $tExt;
                move_uploaded_file($thumb['tmp_name'], __DIR__ . '/../assets/images/cr/' . $tName);
                $cr['thumbnail'] = 'assets/images/cr/' . $tName;
            }
        }

        $pdfPath = __DIR__ . '/../' . $cr['file'];
        if (empty($cr['thumbnail']) && extension_loaded('imagick') && file_exists($pdfPath)) {
            try {
                $img = new Imagick();
                $img->setResolution(150, 150);
                $img->readImage($pdfPath . '[0]');
                $img->setImageFormat('jpg');
                $img->setImageCompression(Imagick::COMPRESSION_JPEG);
                $img->setOption('jpeg:extent', '100KB');
                $img->stripImage();
                $tName = 'cr_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.jpg';
                $img->writeImage(__DIR__ . '/../assets/images/cr/' . $tName);
                $img->clear();
                $cr['thumbnail'] = 'assets/images/cr/' . $tName;
            } catch (Exception $e) {}
        }

        $items[$index] = $cr;
        file_put_contents($file, json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        header('Location: comptes_rendus.php?updated=1');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier | Administration</title>
    <link rel="stylesheet" href="../assets/fonts/fontawesome.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="admin-bar">
        <div class="container" style="display:flex;justify-content:space-between;align-items:center;">
            <span><i class="fas fa-shield-alt"></i> Modifier le compte-rendu</span>
            <a href="comptes_rendus.php" style="color:var(--gold);"><i class="fas fa-arrow-left"></i> Retour</a>
        </div>
    </div>
    <main style="padding:2rem 0;">
        <div class="container" style="max-width:600px;">
            <h2 style="margin-bottom:1.5rem;">Modifier</h2>
            <?php if ($error): ?><div style="background:#fef2f2;color:#b91c1c;padding:0.75rem 1rem;border-radius:var(--radius-sm);margin-bottom:1rem;"><?= $error ?></div><?php endif; ?>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Titre</label>
                    <input type="text" id="title" name="title" class="form-control" value="<?= htmlspecialchars($cr['title']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="date">Date</label>
                    <input type="date" id="date" name="date" class="form-control" value="<?= $cr['date'] ?>" required>
                </div>
                <div class="form-group">
                    <label for="pdf">Fichier PDF (laisser vide pour conserver l'actuel)</label>
                    <input type="file" id="pdf" name="pdf" accept=".pdf" class="form-control">
                    <small>Actuel : <a href="../<?= htmlspecialchars($cr['file']) ?>" target="_blank"><?= basename($cr['file']) ?></a></small>
                </div>
                <?php if (!empty($cr['thumbnail'])): ?>
                <div class="form-group">
                    <label>Preview actuelle</label>
                    <div><img src="../<?= htmlspecialchars($cr['thumbnail']) ?>" style="height:80px;border-radius:4px;"></div>
                </div>
                <?php endif; ?>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Enregistrer</button>
            </form>
        </div>
    </main>
</body>
</html>
