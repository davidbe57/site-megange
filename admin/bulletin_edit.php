<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/cr_helpers.php';
if (empty($_SESSION['admin'])) { header('Location: index.php'); exit; }

$file = DATA_DIR . '/bulletins.json';
$items = file_exists($file) ? (json_decode(file_get_contents($file), true) ?: []) : [];
$id = (int)($_GET['id'] ?? 0);
$index = null;
foreach ($items as $k => $v) { if ($v['id'] === $id) { $index = $k; break; } }
if ($index === null) { header('Location: bulletins.php'); exit; }

$b = $items[$index];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'] ?? '';
    $pdf  = $_FILES['pdf'] ?? null;
    $thumbBase64 = $_POST['thumb_data'] ?? '';

    if ($date === '') {
        $error = 'La date est obligatoire.';
    } else {
        $b['date'] = $date;
        $months = ['','janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre'];
        $ts = strtotime($date);
        $b['title'] = 'Bulletin municipal - ' . $months[(int)date('m', $ts)] . ' ' . date('Y', $ts);

        if ($pdf && $pdf['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($pdf['name'], PATHINFO_EXTENSION));
            if ($ext === 'pdf') {
                if (file_exists(__DIR__ . '/../' . $b['file'])) unlink(__DIR__ . '/../' . $b['file']);
                $pdfName = 'bulletin_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.pdf';
                $pdfPath = __DIR__ . '/../assets/pdf/' . $pdfName;
                move_uploaded_file($pdf['tmp_name'], $pdfPath);
                $b['file'] = 'assets/pdf/' . $pdfName;

                if (!empty($b['thumbnail']) && file_exists(__DIR__ . '/../' . $b['thumbnail'])) unlink(__DIR__ . '/../' . $b['thumbnail']);
                if (!empty($thumbBase64)) {
                    $b['thumbnail'] = saveBase64Thumbnail($thumbBase64);
                } else {
                    $b['thumbnail'] = generateCrThumbnail($pdfPath);
                }
            }
        }

        $items[$index] = $b;
        file_put_contents($file, json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        header('Location: bulletins.php?updated=1');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le bulletin | Administration</title>
    <link rel="stylesheet" href="../assets/fonts/fontawesome.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';</script>
    <style>.thumb-preview{max-width:140px;max-height:180px;border-radius:4px;margin-top:.5rem;display:none;}</style>
</head>
<body>
    <div class="admin-bar">
        <div class="container" style="display:flex;justify-content:space-between;align-items:center;">
            <span><i class="fas fa-shield-alt"></i> Modifier le bulletin</span>
            <a href="bulletins.php" style="color:var(--gold);"><i class="fas fa-arrow-left"></i> Retour</a>
        </div>
    </div>
    <main style="padding:2rem 0;">
        <div class="container" style="max-width:600px;">
            <h2 style="margin-bottom:1.5rem;">Modifier</h2>
            <?php if ($error): ?><div style="background:#fef2f2;color:#b91c1c;padding:0.75rem 1rem;border-radius:var(--radius-sm);margin-bottom:1rem;"><?= $error ?></div><?php endif; ?>
            <form method="POST" enctype="multipart/form-data" id="bulletin-form">
                <div class="form-group">
                    <label for="date">Date d'édition</label>
                    <input type="date" id="date" name="date" class="form-control" value="<?= $b['date'] ?>" required>
                </div>
                <div class="form-group">
                    <label for="pdf">Fichier PDF (laisser vide pour conserver l'actuel)</label>
                    <input type="file" id="pdf" name="pdf" accept=".pdf" class="form-control" onchange="generateThumbnail(this)">
                    <img id="thumb-preview" class="thumb-preview" alt="Aperçu">
                    <small>Actuel : <a href="../<?= htmlspecialchars($b['file']) ?>" target="_blank"><?= basename($b['file']) ?></a></small>
                </div>
                <input type="hidden" name="thumb_data" id="thumb_data" value="">
                <?php if (!empty($b['thumbnail'])): ?>
                <div class="form-group">
                    <label>Preview actuelle</label>
                    <div><img src="../<?= htmlspecialchars($b['thumbnail']) ?>" style="height:120px;border-radius:4px;"></div>
                </div>
                <?php endif; ?>
                <button type="submit" class="btn btn-primary" id="submit-btn"><i class="fas fa-save"></i> Enregistrer</button>
            </form>
        </div>
    </main>
    <script>
    async function generateThumbnail(input) {
        const file = input.files[0];
        if (!file || file.type !== 'application/pdf') return;
        try {
            const url = URL.createObjectURL(file);
            const pdf = await pdfjsLib.getDocument(url).promise;
            const page = await pdf.getPage(1);
            const vp = page.getViewport({ scale: 0.5 });
            const canvas = document.createElement('canvas');
            canvas.width = vp.width;
            canvas.height = vp.height;
            const ctx = canvas.getContext('2d');
            await page.render({ canvasContext: ctx, viewport: vp }).promise;
            canvas.toBlob(function(blob) {
                const reader = new FileReader();
                reader.onloadend = function() {
                    const base64 = reader.result.split(',')[1];
                    document.getElementById('thumb_data').value = base64;
                    const preview = document.getElementById('thumb-preview');
                    preview.src = URL.createObjectURL(blob);
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(blob);
            }, 'image/jpeg', 0.85);
            URL.revokeObjectURL(url);
        } catch(e) {
            console.warn('PDF.js thumbnail generation failed:', e);
        }
    }
    </script>
</body>
</html>
