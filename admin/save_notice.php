<?php
require_once __DIR__ . '/../includes/config.php';
requireLogin();

$base = ($basePath !== '' ? $basePath : '');
$isAjax = strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrf()) {
  if ($isAjax) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => 0, 'redirect' => $base . '/admin/index.php?error=csrf']);
    exit;
  }
  header('Location: ' . $base . '/admin/index.php?error=csrf');
  exit;
}

$notice = trim($_POST['notice'] ?? '');
$dataDir = __DIR__ . '/../data';
$file = $dataDir . '/notice.txt';

if (!is_dir($dataDir)) {
    mkdir($dataDir, 0777, true);
}

file_put_contents($file, $notice);

if ($isAjax) {
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode(['ok' => 1, 'redirect' => $base . '/admin/index.php']);
  exit;
}
header('Location: ' . $base . '/admin/index.php');
exit;