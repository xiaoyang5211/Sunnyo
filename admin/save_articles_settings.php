<?php
require_once __DIR__ . '/../includes/config.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrf()) {
  header('Location: ' . ($basePath !== '' ? $basePath : '') . '/admin/articles.php?error=csrf');
  exit;
}

$siteConfigCurrent = $siteConfig;
$articles = $siteConfigCurrent['articles'] ?? [
  'source' => 'manual',
  'rsshubUrl' => '',
  'cacheTtlSec' => 600,
];

$source = trim($_POST['articles_source'] ?? ($articles['source'] ?? 'manual'));
$rsshubUrl = trim($_POST['articles_rsshub_url'] ?? ($articles['rsshubUrl'] ?? ''));
$cacheTtl = intval($_POST['articles_cache_ttl'] ?? ($articles['cacheTtlSec'] ?? 600));

$articles['source'] = ($source === 'rsshub') ? 'rsshub' : 'manual';
$articles['rsshubUrl'] = $rsshubUrl;
$articles['cacheTtlSec'] = ($cacheTtl >= 60) ? $cacheTtl : 600;

$siteConfigNew = array_replace_recursive($siteConfigCurrent, [ 'articles' => $articles ]);

if (saveSiteSettings($siteConfigNew)) {
  header('Location: ' . ($basePath !== '' ? $basePath : '') . '/admin/articles.php?ok=1');
  exit;
}

header('Location: ' . ($basePath !== '' ? $basePath : '') . '/admin/articles.php?error=1');
exit;