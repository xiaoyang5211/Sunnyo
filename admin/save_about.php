<?php
require_once __DIR__ . '/../includes/config.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrf()) {
  header('Location: ' . ($basePath !== '' ? $basePath : '') . '/admin/about.php?error=csrf');
  exit;
}

$personal = [
  'name' => trim($_POST['name'] ?? ''),
  'avatar' => trim($_POST['avatar'] ?? ''),
  'bio' => trim($_POST['bio'] ?? ''),
  'location' => trim($_POST['location'] ?? ''),
  'hobby' => trim($_POST['hobby'] ?? ''),
  'learning' => trim($_POST['learning'] ?? ''),
  'social' => [
    'github' => trim($_POST['github'] ?? ''),
    'email' => trim($_POST['email'] ?? ''),
  ]
];
$aboutContent = trim($_POST['aboutContent'] ?? '');

$payload = [
  'personal' => array_replace($siteConfig['personal'], $personal),
  'aboutContent' => $aboutContent,
];

if (saveSiteSettings(array_replace_recursive($siteConfig, $payload))) {
  header('Location: ' . ($basePath !== '' ? $basePath : '') . '/admin/about.php?ok=1');
  exit;
}
header('Location: ' . ($basePath !== '' ? $basePath : '') . '/admin/about.php?error=1');
exit;