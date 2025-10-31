<?php
// Dynamic theme CSS to avoid inline styles and set proper headers
header('Content-Type: text/css; charset=utf-8');
// 允许缓存主题样式，结合版本号 query（v=assetVersionCss）实现长期缓存
header('Cache-Control: public, max-age=31536000, immutable');
header('X-Content-Type-Options: nosniff');
require_once __DIR__ . '/../../../includes/config.php';
$theme = $siteConfig['theme'] ?? [];
$bgType = $theme['backgroundType'] ?? 'gradient';
$bgImageUrl = $theme['backgroundImageUrl'] ?? '';
$bgGradient = $theme['backgroundGradient'] ?? 'linear-gradient(135deg, #f7e9ff 0%, #e9d5ff 50%, #d8b4fe 100%)';
$accentColor = $theme['accentColor'] ?? '#ff7aa2';
$accentImageUrl = trim($theme['accentImageUrl'] ?? '');
?>
/* Global background injected from PHP configuration */
html {
<?php if ($bgType === 'image' && !empty($bgImageUrl)) : ?>
  background-image: url('<?php echo addslashes($bgImageUrl); ?>');
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  background-attachment: fixed;
  min-height: 100%;
<?php else : ?>
  background: <?php echo $bgGradient; ?>;
  background-attachment: fixed;
  min-height: 100%;
<?php endif; ?>
}
body { background: transparent !important; }

/* Accent variables */
:root {
  --accent-color: <?php echo $accentColor; ?>;
}

/* 桌面导航当前页面高亮 */
.navbar nav a { position: relative; }
.navbar nav a.active {
  color: #ffffff !important;
  background: var(--accent-color);
  border-radius: 0.5rem;
  padding: 0.5rem 0.75rem;
}
<?php if (!empty($accentImageUrl)) : ?>
.navbar nav a.active::before {
  content: '';
  position: absolute;
  inset: 0;
  background-image: url('<?php echo addslashes($accentImageUrl); ?>');
  background-size: cover;
  background-position: center;
  opacity: 0.22;
  border-radius: 0.5rem;
  pointer-events: none;
}
<?php endif; ?>

/* 移动端菜单当前页面高亮 */
#nav-menu a.active {
  color: #ffffff !important;
  background: var(--accent-color);
  border-radius: 0.5rem;
}
<?php if (!empty($accentImageUrl)) : ?>
#nav-menu a.active::before {
  content: '';
  position: absolute;
  inset: 0;
  background-image: url('<?php echo addslashes($accentImageUrl); ?>');
  background-size: cover;
  background-position: center;
  opacity: 0.2;
  border-radius: 0.5rem;
  pointer-events: none;
}
<?php endif; ?>