<?php
require_once __DIR__ . '/../includes/config.php';
requireLogin();

$base = ($basePath !== '' ? $basePath : '');
$isAjax = strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrf()) {
  if ($isAjax) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => 0, 'redirect' => $base . '/admin/friends.php?error=csrf']);
    exit;
  }
  header('Location: ' . $base . '/admin/friends.php?error=csrf');
  exit;
}

$action = $_POST['action'] ?? '';
$index = intval($_POST['index'] ?? -1);

$requests = readData('friend_requests', []);
if ($index < 0 || !isset($requests[$index])) {
  if ($isAjax) { header('Content-Type: application/json; charset=utf-8'); echo json_encode(['ok' => 0, 'redirect' => $base . '/admin/friends.php?error=index']); exit; }
  header('Location: ' . $base . '/admin/friends.php?error=index');
  exit;
}

if ($action === 'approve') {
  $item = $requests[$index];
  addDataItem('friends', [
    'title' => (string)($item['title'] ?? ''),
    'description' => (string)($item['description'] ?? ''),
    'url' => (string)($item['url'] ?? ''),
    'avatar' => (string)($item['avatar'] ?? ''),
  ]);
  deleteDataItem('friend_requests', $index);
  if ($isAjax) { header('Content-Type: application/json; charset=utf-8'); echo json_encode(['ok' => 1, 'redirect' => $base . '/admin/friends.php?ok=1']); exit; }
  header('Location: ' . $base . '/admin/friends.php?ok=1');
  exit;
} elseif ($action === 'reject') {
  deleteDataItem('friend_requests', $index);
  if ($isAjax) { header('Content-Type: application/json; charset=utf-8'); echo json_encode(['ok' => 1, 'redirect' => $base . '/admin/friends.php?ok=1']); exit; }
  header('Location: ' . $base . '/admin/friends.php?ok=1');
  exit;
}

if ($isAjax) { header('Content-Type: application/json; charset=utf-8'); echo json_encode(['ok' => 0, 'redirect' => $base . '/admin/friends.php?error=action']); exit; }
header('Location: ' . $base . '/admin/friends.php?error=action');
exit;