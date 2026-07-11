<?php
session_start();
require_once __DIR__ . '/../config.php';

if (empty($_SESSION['admin'])) {
    header('Location: index.php');
    exit;
}

$art_file = DATA_DIR . '/articles.json';
$articles = file_exists($art_file) ? (json_decode(file_get_contents($art_file), true) ?: []) : [];

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$articles = array_values(array_filter($articles, function ($a) use ($id) {
    return $a['id'] !== $id;
}));

file_put_contents($art_file, json_encode($articles, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
header('Location: index.php?deleted=1');
exit;
