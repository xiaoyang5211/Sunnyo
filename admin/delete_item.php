<?php
require_once __DIR__ . '/../includes/config.php';
requireLogin();

if (!verifyCsrf()) {
  header('Location: ' . ($basePath !== '' ? $basePath : '') . '/admin/index.php?error=csrf');
  exit;
}

$allowed = ['articles', 'friends', 'projects', 'websites'];
$type = $_GET['type'] ?? '';
$index = isset($_GET['index']) ? intval($_GET['index']) : -1;
if (!in_array($type, $allowed, true) || $index < 0) {
  header('Location: ' . ($basePath !== '' ? $basePath : '') . '/admin/index.php?error=delete');
  exit;
}

deleteDataItem($type, $index);
header('Location: ' . ($basePath !== '' ? $basePath : '') . '/admin/' . $type . '.php?ok=1');
exit;