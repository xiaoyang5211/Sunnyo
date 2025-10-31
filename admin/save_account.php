<?php
require_once __DIR__ . '/../includes/config.php';
requireLogin();

$base = ($basePath !== '' ? $basePath : '');
$isAjax = strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrf()) {
  if ($isAjax) { header('Content-Type: application/json; charset=utf-8'); echo json_encode(['ok' => 0, 'redirect' => $base . '/admin/account.php?error=csrf']); exit; }
  header('Location: ' . $base . '/admin/account.php?error=csrf');
  exit;
}

$username = trim($_POST['username'] ?? '');
$password = (string)($_POST['password'] ?? '');
$passwordConfirm = (string)($_POST['password_confirm'] ?? '');
$storePlain = !empty($_POST['store_plain']);
$currentPassword = (string)($_POST['current_password'] ?? '');

if ($username === '') {
  $dest = $base . '/admin/account.php?error=empty_username';
  if ($isAjax) { header('Content-Type: application/json; charset=utf-8'); echo json_encode(['ok' => 0, 'redirect' => $dest]); exit; }
  header('Location: ' . $dest); exit;
}

// 必须校验当前密码
$validCurrent = false;
if (!empty($adminConfig['password'])) {
  $validCurrent = ($currentPassword === $adminConfig['password']);
} elseif (!empty($adminConfig['password_hash'])) {
  $validCurrent = password_verify($currentPassword, $adminConfig['password_hash']);
} else {
  // 默认情况（未设置哈希但有默认明文）
  $validCurrent = ($currentPassword === 'admin123');
}
if (!$validCurrent) {
  $dest = $base . '/admin/account.php?error=old_password_incorrect';
  if ($isAjax) { header('Content-Type: application/json; charset=utf-8'); echo json_encode(['ok' => 0, 'redirect' => $dest]); exit; }
  header('Location: ' . $dest); exit;
}

// 读取已有配置（保持未修改项）
$existing = readData('admin', []);
if (!is_array($existing)) { $existing = []; }
$new = array_replace(['login_enabled' => true, 'username' => $username, 'password' => '', 'password_hash' => ''], $existing);
$new['username'] = $username;

if ($password !== '') {
  if (strlen($password) < 6) {
    $dest = $base . '/admin/account.php?error=password_too_short';
    if ($isAjax) { header('Content-Type: application/json; charset=utf-8'); echo json_encode(['ok' => 0, 'redirect' => $dest]); exit; }
    header('Location: ' . $dest); exit;
  }
  if ($password !== $passwordConfirm) {
    $dest = $base . '/admin/account.php?error=confirm_mismatch';
    if ($isAjax) { header('Content-Type: application/json; charset=utf-8'); echo json_encode(['ok' => 0, 'redirect' => $dest]); exit; }
    header('Location: ' . $dest); exit;
  }
  if ($storePlain) {
    $new['password'] = $password;
    $new['password_hash'] = '';
  } else {
    $new['password'] = '';
    $new['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
  }
}

if (saveData('admin', $new)) {
  $dest = $base . '/admin/account.php?ok=1';
  if ($isAjax) { header('Content-Type: application/json; charset=utf-8'); echo json_encode(['ok' => 1, 'redirect' => $dest]); exit; }
  header('Location: ' . $dest); exit;
}
$dest = $base . '/admin/account.php?error=save_failed';
if ($isAjax) { header('Content-Type: application/json; charset=utf-8'); echo json_encode(['ok' => 0, 'redirect' => $dest]); exit; }
header('Location: ' . $dest); exit;