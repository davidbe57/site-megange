<?php
session_start();
require_once __DIR__ . '/../config.php';
if (empty($_SESSION['admin'])) { header('Location: index.php'); exit; }

$file = __DIR__ . '/../data/comptes_rendus.json';
$items = file_exists($file) ? (json_decode(file_get_contents($file), true) ?: []) : [];
$id = (int)($_GET['id'] ?? 0);

foreach ($items as $k => $v) {
    if ($v['id'] === $id) {
        if (file_exists(__DIR__ . '/../' . $v['file'])) unlink(__DIR__ . '/../' . $v['file']);
        if (!empty($v['thumbnail']) && file_exists(__DIR__ . '/../' . $v['thumbnail'])) unlink(__DIR__ . '/../' . $v['thumbnail']);
        array_splice($items, $k, 1);
        break;
    }
}
file_put_contents($file, json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
header('Location: comptes_rendus.php?deleted=1');
exit;
