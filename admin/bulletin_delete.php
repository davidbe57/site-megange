<?php
session_start();
require_once __DIR__ . '/../config.php';
if (empty($_SESSION['admin'])) { header('Location: index.php'); exit; }

$file = DATA_DIR . '/bulletins.json';
$items = file_exists($file) ? (json_decode(file_get_contents($file), true) ?: []) : [];
$id = (int)($_GET['id'] ?? 0);

foreach ($items as $k => $v) {
    if ($v['id'] === $id) {
        $filePath = strpos($v['file'], 'serve.php?f=') === 0 ? UPLOADS_DIR . '/' . substr($v['file'], 12) : __DIR__ . '/../' . $v['file'];
        if (file_exists($filePath)) unlink($filePath);
        $thumbPath = strpos($v['thumbnail'], 'serve.php?f=') === 0 ? UPLOADS_DIR . '/' . substr($v['thumbnail'], 12) : __DIR__ . '/../' . $v['thumbnail'];
        if (!empty($v['thumbnail']) && file_exists($thumbPath)) unlink($thumbPath);
        array_splice($items, $k, 1);
        break;
    }
}
file_put_contents($file, json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
header('Location: bulletins.php?deleted=1');
exit;
