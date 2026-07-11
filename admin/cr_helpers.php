<?php
function generateCrThumbnail($pdfPath) {
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
            return 'assets/images/cr/' . $tName;
        } catch (Exception $e) {}
    }

    // 2) Ghostscript CLI
    $gsBin = PHP_OS_FAMILY === 'Windows' ? 'gswin64c' : 'gs';
    $cmd = sprintf('"%s" -dNOPAUSE -dBATCH -dSAFER -sDEVICE=jpeg -r150 -dFirstPage=1 -dLastPage=1 -sOutputFile="%s" "%s" 2>&1', $gsBin, $tFull, $pdfPath);
    exec($cmd, $out, $code);
    if ($code === 0 && file_exists($tFull) && filesize($tFull) > 1000) {
        return 'assets/images/cr/' . $tName;
    }

    // 3) ImageMagick CLI (convert)
    $cmd = sprintf('convert -density 150 "%s"[0] -quality 85 -strip "%s" 2>&1', $pdfPath, $tFull);
    exec($cmd, $out, $code);
    if ($code === 0 && file_exists($tFull) && filesize($tFull) > 1000) {
        return 'assets/images/cr/' . $tName;
    }

    // 4) pdftoppm CLI (poppler)
    $ppmBase = preg_replace('/\.jpg$/', '', $tFull);
    $cmd = sprintf('pdftoppm -f 1 -l 1 -r 150 -jpeg "%s" "%s" 2>&1', $pdfPath, $ppmBase);
    exec($cmd, $out, $code);
    $ppmFile = $ppmBase . '-1.jpg';
    if ($code === 0 && file_exists($ppmFile) && filesize($ppmFile) > 1000) {
        rename($ppmFile, $tFull);
        return 'assets/images/cr/' . $tName;
    }

    // 5) Fallback GD — thumbnail stylisée avec date
    $w = 210; $h = 280;
    $im = imagecreatetruecolor($w, $h);
    $bg = imagecolorallocate($im, 248, 245, 240);
    $border = imagecolorallocate($im, 220, 215, 205);
    $accent = imagecolorallocate($im, 200, 103, 61);
    $textColor = imagecolorallocate($im, 80, 75, 70);
    $white = imagecolorallocate($im, 255, 255, 255);
    imagefill($im, 0, 0, $bg);
    imagerectangle($im, 0, 0, $w - 1, $h - 1, $border);
    // Accent bar top
    imagefilledrectangle($im, 0, 0, $w - 1, 6, $accent);
    // PDF icon area
    $iconX = ($w - 50) / 2;
    $iconY = 35;
    imagefilledrectangle($im, $iconX, $iconY, $iconX + 50, $iconY + 60, $white);
    imagerectangle($im, $iconX, $iconY, $iconX + 50, $iconY + 60, $border);
    // Red PDF stripe
    imagefilledrectangle($im, $iconX + 10, $iconY + 15, $iconX + 40, $iconY + 20, $accent);
    imagefilledrectangle($im, $iconX + 10, $iconY + 26, $iconX + 35, $iconY + 30, $accent);
    imagefilledrectangle($im, $iconX + 10, $iconY + 37, $iconX + 38, $iconY + 41, $accent);
    imagefilledrectangle($im, $iconX + 10, $iconY + 48, $iconX + 30, $iconY + 52, $accent);
    imagejpeg($im, $tFull, 85);
    imagedestroy($im);

    if (file_exists($tFull) && filesize($tFull) > 100) {
        return 'assets/images/cr/' . $tName;
    }
    return '';
}

function saveBase64Thumbnail($base64Data) {
    $tName = 'cr_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.jpg';
    $tFull = __DIR__ . '/../assets/images/cr/' . $tName;
    if (!is_dir(__DIR__ . '/../assets/images/cr/')) {
        mkdir(__DIR__ . '/../assets/images/cr/', 0755, true);
    }
    $data = base64_decode($base64Data);
    if ($data === false) return '';
    file_put_contents($tFull, $data);
    if (file_exists($tFull) && filesize($tFull) > 100) {
        return 'assets/images/cr/' . $tName;
    }
    return '';
}
