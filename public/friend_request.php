<?php
require_once __DIR__ . '/../includes/config.php';

$base = ($basePath !== '' ? $basePath : '');
$isAjax = strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrf()) {
  if ($isAjax) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => 0, 'redirect' => $base . '/friends.php?req=error']);
    exit;
  }
  header('Location: ' . $base . '/friends.php?req=error');
  exit;
}

$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$url = trim($_POST['url'] ?? '');
$contactEmail = trim($_POST['contactEmail'] ?? '');
$avatar = trim($_POST['avatar'] ?? '');

// 基本校验：标题、URL、邮箱必填；URL需http/https；邮箱格式校验；头像选填但若填写需http/https
$invalid = (
  $title === '' ||
  $url === '' || !preg_match('/^https?:\/\//i', $url) ||
  $contactEmail === '' || !filter_var($contactEmail, FILTER_VALIDATE_EMAIL) ||
  ($avatar !== '' && !preg_match('/^https?:\/\//i', $avatar))
);
if ($invalid) {
  if ($isAjax) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => 0, 'redirect' => $base . '/friends.php?req=invalid']);
    exit;
  }
  header('Location: ' . $base . '/friends.php?req=invalid');
  exit;
}

// 限制长度，避免异常数据
$title = mb_substr($title, 0, 100);
$description = mb_substr($description, 0, 300);
$contactEmail = mb_substr($contactEmail, 0, 100);
$avatar = mb_substr($avatar, 0, 300);

$item = [
  'title' => $title,
  'description' => $description,
  'url' => $url,
  'contactEmail' => $contactEmail,
  'avatar' => $avatar,
  'submittedAt' => time(),
];

$pending = readData('friend_requests', []);
$pending[] = $item;
saveData('friend_requests', $pending);

if ($isAjax) {
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode(['ok' => 1, 'redirect' => $base . '/friends.php?req=ok']);
  exit;
}
header('Location: ' . $base . '/friends.php?req=ok');
exit;