<?php
require_once __DIR__ . '/../includes/config.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrf()) {
  header('Location: ' . ($basePath !== '' ? $basePath : '') . '/admin/index.php?error=csrf');
  exit;
}

$allowed = ['articles', 'friends', 'projects', 'websites'];
$type = $_POST['type'] ?? '';
$action = $_POST['action'] ?? '';
if (!in_array($type, $allowed, true)) {
  header('Location: ' . ($basePath !== '' ? $basePath : '') . '/admin/index.php?error=type');
  exit;
}

// 简单校验：对于 friends，title/url 必填，url 必须 http/https，avatar 选填但若填写需 http/https
$validateFriends = function(array $item) {
  if (trim($item['title'] ?? '') === '') return false;
  $url = trim($item['url'] ?? '');
  if ($url === '' || !preg_match('/^https?:\/\//i', $url)) return false;
  $avatar = trim($item['avatar'] ?? '');
  if ($avatar !== '' && !preg_match('/^https?:\/\//i', $avatar)) return false;
  return true;
};

if ($action === 'add') {
  $item = [
    'title' => trim($_POST['title'] ?? ''),
    'description' => trim($_POST['description'] ?? ''),
    'url' => trim($_POST['url'] ?? ''),
  ];
  if ($type === 'friends') {
    $item['avatar'] = trim($_POST['avatar'] ?? '');
    if (!$validateFriends($item)) {
      header('Location: ' . ($basePath !== '' ? $basePath : '') . '/admin/friends.php?error=invalid');
      exit;
    }
  }
  addDataItem($type, $item);
} elseif ($action === 'update') {
  $index = intval($_POST['index'] ?? -1);
  $item = [
    'title' => trim($_POST['title'] ?? ''),
    'description' => trim($_POST['description'] ?? ''),
    'url' => trim($_POST['url'] ?? ''),
  ];
  if ($type === 'friends') {
    $item['avatar'] = trim($_POST['avatar'] ?? '');
    if (!$validateFriends($item)) {
      header('Location: ' . ($basePath !== '' ? $basePath : '') . '/admin/friends.php?error=invalid');
      exit;
    }
  }
  updateDataItem($type, $index, $item);
}

header('Location: ' . ($basePath !== '' ? $basePath : '') . '/admin/' . $type . '.php?ok=1');
exit;