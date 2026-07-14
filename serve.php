<?php
require_once __DIR__ . '/config.php';

$f = $_GET['f'] ?? '';
if (!$f || strpos($f, '..') !== false || strpos($f, '/') === false) {
    http_response_code(404); exit;
}

$safe = basename($f);
$dir = dirname($f);
$allowed = ['pdf', 'thumbnails', 'blog', 'elus', 'conseil', 'carousel', 'gallery'];
if (!in_array($dir, $allowed)) { http_response_code(404); exit; }

$path = UPLOADS_DIR . '/' . $dir . '/' . $safe;
$legacyPath = __DIR__ . '/assets/' . $dir . '/' . $safe;

if (!file_exists($path)) {
    if (file_exists($legacyPath)) {
        $path = $legacyPath;
    } else {
        http_response_code(404); exit;
    }
}

$ext = strtolower(pathinfo($safe, PATHINFO_EXTENSION));
$mime = [
    'pdf'  => 'application/pdf',
    'jpg'  => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png'  => 'image/png',
    'webp' => 'image/webp',
    'gif'  => 'image/gif',
];
$ct = $mime[$ext] ?? 'application/octet-stream';

header('Content-Type: ' . $ct);
header('Content-Length: ' . filesize($path));
header('Cache-Control: public, max-age=86400');
readfile($path);
