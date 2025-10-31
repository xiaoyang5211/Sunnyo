<?php
require_once __DIR__ . '/../../includes/config.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrf()) {
  header('Location: ' . ($basePath !== '' ? $basePath : '') . '/admin/friends.php?error=csrf');
  exit;
}

$action = $_POST['action'] ?? '';
$index = intval($_POST['index'] ?? -1);

$requests = readData('friend_requests', []);
if ($index < 0 || !isset($requests[$index])) {
  header('Location: ' . ($basePath !== '' ? $basePath : '') . '/admin/friends.php?error=index');
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
  header('Location: ' . ($basePath !== '' ? $basePath : '') . '/admin/friends.php?ok=1');
  exit;
} elseif ($action === 'reject') {
  deleteDataItem('friend_requests', $index);
  header('Location: ' . ($basePath !== '' ? $basePath : '') . '/admin/friends.php?ok=1');
  exit;
}

header('Location: ' . ($basePath !== '' ? $basePath : '') . '/admin/friends.php?error=action');
exit;