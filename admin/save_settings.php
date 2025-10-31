<?php
require_once __DIR__ . '/../includes/config.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrf()) {
  header('Location: ' . ($basePath !== '' ? $basePath : '') . '/admin/settings.php?error=csrf');
  exit;
}

// 读取当前配置
$siteConfigCurrent = $siteConfig;
$site = $siteConfigCurrent['site'];
$theme = $siteConfigCurrent['theme'];
$umami = $siteConfigCurrent['umami'];

// 基础信息
$site['title'] = trim($_POST['site_title'] ?? $site['title']);
$site['subtitle'] = trim($_POST['site_subtitle'] ?? $site['subtitle']);
$site['description'] = trim($_POST['site_description'] ?? $site['description']);

// 背景渐变三色
$from = $_POST['bg_from'] ?? '#f7e9ff';
$via  = $_POST['bg_via']  ?? '#e9d5ff';
$to   = $_POST['bg_to']   ?? '#d8b4fe';
$theme['backgroundGradient'] = "linear-gradient(135deg, {$from} 0%, {$via} 50%, {$to} 100%)";

// 背景类型与图片
$bgType = $_POST['bg_type'] ?? ($theme['backgroundType'] ?? 'gradient');
$bgImageUrl = trim($_POST['bg_image_url'] ?? ($theme['backgroundImageUrl'] ?? ''));
if ($bgType === 'image') {
  $theme['backgroundType'] = 'image';
} elseif ($bgType === 'white') {
  $theme['backgroundType'] = 'white';
} else {
  $theme['backgroundType'] = 'gradient';
}
$theme['backgroundImageUrl'] = $bgImageUrl;

// 按钮强调：颜色与可选背景图
$accentColor = trim($_POST['accent_color'] ?? ($theme['accentColor'] ?? '#ff7aa2'));
$accentImageUrl = trim($_POST['accent_image_url'] ?? ($theme['accentImageUrl'] ?? ''));
// 简单校验颜色格式（#xxx 或 #xxxxxx）
if (!preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $accentColor)) { $accentColor = '#ff7aa2'; }
$theme['accentColor'] = $accentColor;
$theme['accentImageUrl'] = $accentImageUrl;

// Umami
$umami['enable'] = !empty($_POST['umami_enable']) && $_POST['umami_enable'] == '1' ? 1 : 0;
$umami['scriptSrc'] = trim($_POST['umami_scriptSrc'] ?? ($umami['scriptSrc'] ?? ''));
$umami['websiteId'] = trim($_POST['umami_websiteId'] ?? ($umami['websiteId'] ?? ''));

// Live2D（看板娘）
$live2d = $siteConfigCurrent['live2d'] ?? ['enable' => 1];
$live2d['enable'] = !empty($_POST['live2d_enable']) && $_POST['live2d_enable'] == '1' ? 1 : 0;

// 底部音乐播放器（明月浩空 myhkw）
$musicMyhk = $siteConfigCurrent['music']['myhk'] ?? ['enable' => 0, 'key' => '', 'mode' => 1];
$musicMyhk['enable'] = !empty($_POST['music_enable']) && $_POST['music_enable'] == '1' ? 1 : 0;
$musicMyhk['key'] = preg_replace('/[^0-9]/', '', (string)($_POST['music_key'] ?? $musicMyhk['key']));
$musicMyhk['mode'] = intval($_POST['music_mode'] ?? $musicMyhk['mode']);

$siteConfigNew = [
  'site' => $site,
  'theme' => $theme,
  'umami' => $umami,
  'live2d' => $live2d,
  'music' => [ 'myhk' => $musicMyhk ],
];

saveSiteSettings($siteConfigNew);

header('Location: ' . ($basePath !== '' ? $basePath : '') . '/admin/settings.php?updated=1');
exit;