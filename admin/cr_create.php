<?php
session_start();
require_once __DIR__ . '/../config.php';
if (empty($_SESSION['admin'])) { header('Location: index.php'); exit; }

$file = __DIR__ . '/../data/comptes_rendus.json';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $date  = $_POST['date'] ?? '';
    $pdf   = $_FILES['pdf'] ?? null;
    $thumb = $_FILES['thumbnail'] ?? null;

    if ($title === '' || $date === '' || !$pdf || $pdf['error'] !== UPLOAD_ERR_OK) {
        $error = 'Veuillez remplir tous les champs obligatoires (titre, date, fichier PDF).';
    } else {
        $ext = strtolower(pathinfo($pdf['name'], PATHINFO_EXTENSION));
        if ($ext !== 'pdf') { $error = 'Le fichier doit être au format PDF.'; }
        else {
            $pdfName = 'cr_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.pdf';
            $pdfPath = __DIR__ . '/../assets/pdf/' . $pdfName;
            move_uploaded_file($pdf['tmp_name'], $pdfPath);

            $thumbPath = '';
            $tName = 'cr_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.jpg';
            $tFull = __DIR__ . '/../assets/images/cr/' . $tName;

            // 1) Imagick PHP extension
            if (extension_loaded('imagick')) {
                try {
                    $img = new Imagick();
                    $img->setResolution(150, 150);
                    $img->readImage($pdfPath . '[0]');
                    $img->setImageFormat('jpg');
                    $img->setImageCompression(Imagick::COMPRESSION_JPEG);
                    $img->setOption('jpeg:extent', '100KB');
                    $img->stripImage();
                    $img->writeImage($tFull);
                    $img->clear();
                    $thumbPath = 'assets/images/cr/' . $tName;
                } catch (Exception $e) {}
            }

            // 2) Ghostscript CLI
            if ($thumbPath === '') {
                $gsBin = PHP_OS_FAMILY === 'Windows' ? 'gswin64c' : 'gs';
                $cmd = sprintf('"%s" -dNOPAUSE -dBATCH -dSAFER -sDEVICE=jpeg -r150 -dFirstPage=1 -dLastPage=1 -sOutputFile="%s" "%s" 2>&1', $gsBin, $tFull, $pdfPath);
                exec($cmd, $out, $code);
                if ($code === 0 && file_exists($tFull) && filesize($tFull) > 1000) {
                    $thumbPath = 'assets/images/cr/' . $tName;
                }
            }

            // 3) ImageMagick CLI (convert)
            if ($thumbPath === '') {
                $cmd = sprintf('convert -density 150 "%s"[0] -quality 85 -strip "%s" 2>&1', $pdfPath, $tFull);
                exec($cmd, $out, $code);
                if ($code === 0 && file_exists($tFull) && filesize($tFull) > 1000) {
                    $thumbPath = 'assets/images/cr/' . $tName;
                }
            }

            // 4) pdftoppm CLI (poppler)
            if ($thumbPath === '') {
                $ppmBase = preg_replace('/\.jpg$/', '', $tFull);
                $cmd = sprintf('pdftoppm -f 1 -l 1 -r 150 -jpeg "%s" "%s" 2>&1', $pdfPath, $ppmBase);
                exec($cmd, $out, $code);
                $ppmFile = $ppmBase . '-1.jpg';
                if ($code === 0 && file_exists($ppmFile) && filesize($ppmFile) > 1000) {
                    rename($ppmFile, $tFull);
                    $thumbPath = 'assets/images/cr/' . $tName;
                }
            }

            $items = file_exists($file) ? (json_decode(file_get_contents($file), true) ?: []) : [];
            $maxId = 0;
            foreach ($items as $i) { if ($i['id'] > $maxId) $maxId = $i['id']; }
            $items[] = [
                'id'        => $maxId + 1,
                'title'     => $title,
                'date'      => $date,
                'file'      => 'assets/pdf/' . $pdfName,
                'thumbnail' => $thumbPath,
            ];
            file_put_contents($file, json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            header('Location: comptes_rendus.php?created=1');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un compte-rendu | Administration</title>
    <link rel="stylesheet" href="../assets/fonts/fontawesome.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="admin-bar">
        <div class="container" style="display:flex;justify-content:space-between;align-items:center;">
            <span><i class="fas fa-shield-alt"></i> Ajouter un compte-rendu</span>
            <a href="comptes_rendus.php" style="color:var(--gold);"><i class="fas fa-arrow-left"></i> Retour</a>
        </div>
    </div>
    <main style="padding:2rem 0;">
        <div class="container" style="max-width:600px;">
            <h2 style="margin-bottom:1.5rem;">Nouveau compte-rendu</h2>
            <?php if ($error): ?><div style="background:#fef2f2;color:#b91c1c;padding:0.75rem 1rem;border-radius:var(--radius-sm);margin-bottom:1rem;"><?= $error ?></div><?php endif; ?>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Titre</label>
                    <input type="text" id="title" name="title" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="date">Date</label>
                    <input type="date" id="date" name="date" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="pdf">Fichier PDF</label>
                    <input type="file" id="pdf" name="pdf" accept=".pdf" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Enregistrer</button>
            </form>
        </div>
    </main>
</body>
</html>
