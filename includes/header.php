<?php
require_once __DIR__ . '/config.php';
// Security & content negotiation headers
header('Content-Type: text/html; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: private, no-cache');
// 使用 CSP 限制页面被外部站点嵌入
header("Content-Security-Policy: frame-ancestors 'self'");
// 仅在 HTTPS 环境启用 HSTS（本地开发不会注入）
$__isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
  || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
if ($__isHttps) {
  header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
}

$currentPage = $currentPage ?? '';
$theme = $siteConfig['theme'];
$__isAdmin = strpos($_SERVER['REQUEST_URI'] ?? '', '/admin/') !== false;

$bgType = $theme['backgroundType'] ?? 'gradient';
$bgImageUrl = $theme['backgroundImageUrl'] ?? '';
$bgGradient = $theme['backgroundGradient'] ?? 'linear-gradient(135deg, #f7e9ff 0%, #e9d5ff 50%, #d8b4fe 100%)';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php if ($__isAdmin): ?><meta name="robots" content="noindex, nofollow"><?php endif; ?>
  <title><?php echo isset($page_title) ? $page_title : htmlspecialchars($siteConfig['site']['title'] ?? 'Fumomo'); ?></title>
  <!-- Tailwind CSS（本地自托管为主）-->
  <link id="tailwind-local" rel="stylesheet" href="<?php echo $basePath; ?>/assets/css/tailwind.min.css?v=<?php echo $assetVersionCss; ?>">
  
  <!-- Tailwind CSS 回退链：jsDelivr → unpkg → cdnjs → 本地lite -->
  <script>
    (function(){
      function hasTailwind(){
        try {
          var el = document.createElement('div');
          el.className = 'hidden';
          (document.body || document.documentElement).appendChild(el);
          var style = window.getComputedStyle(el);
          var ok = style && style.display === 'none';
          el.remove();
          return ok;
        } catch(e){ return false; }
      }
      function injectCss(url, id){
        if (id && document.getElementById(id)) return;
        var l = document.createElement('link');
        if (id) l.id = id;
        l.rel = 'stylesheet';
        l.href = url;
        l.crossOrigin = 'anonymous';
        document.head.appendChild(l);
      }
      function tryChain(){
        // 1) 等主CDN加载
        setTimeout(function(){
          if (hasTailwind()) return;
          // 2) 回退到 unpkg
          injectCss('https://unpkg.com/tailwindcss@2.2.19/dist/tailwind.min.css','tailwind-fallback-unpkg');
          setTimeout(function(){
            if (hasTailwind()) return;
            // 3) 再回退到 cdnjs
            injectCss('https://cdnjs.cloudflare/ajax/libs/tailwindcss/2.2.19/tailwind.min.css','tailwind-fallback-cdnjs');
            setTimeout(function(){
              if (hasTailwind()) return;
              // 4) 最终本地兜底，保证基本布局可用
              injectCss('<?php echo $basePath; ?>/assets/css/tailwind-lite.css?v=<?php echo $assetVersionCss; ?>','tailwind-fallback-local');
            }, 2000);
          }, 2000);
        }, 1500);
      }
      document.addEventListener('DOMContentLoaded', tryChain);
    })();
  </script>
  
  <!-- 站点样式 -->
  <link rel="stylesheet" href="<?php echo $basePath; ?>/assets/css/style.css?v=<?php echo $assetVersionCss; ?>">

  <!-- 动态主题 CSS（替代内联样式，避免内联 CSS）-->
  <link rel="stylesheet" href="<?php echo $basePath; ?>/assets/css/theme.css.php?v=<?php echo $assetVersionCss; ?>">
  <!-- Font Awesome（看板娘工具栏图标所需） -->
  <link rel="stylesheet" href="https://fastly.jsdelivr.net/npm/@fortawesome/fontawesome-free@6/css/all.min.css" crossorigin="anonymous">
  <!-- Live2D Widget 移动端显示与缩放覆盖样式（适用于 CDN 版） -->
  <style>
    .waifu { display: block !important; }
    @media (max-width: 768px) {
      .waifu { right: 10px !important; bottom: 10px !important; }
      #live2d { width: 180px !important; height: 300px !important; }
    }
  </style>

  <noscript>
    <style>
      /* JS 不可用时显示首页内容 */
      .logo, .subtitle, .content { opacity: 1 !important; transform: none !important; }
    </style>
  </noscript>
</head>
<body style="background: <?php echo ($bgType==='gradient') ? $bgGradient : (($bgType==='white') ? '#fff' : 'transparent'); ?>;<?php echo ($bgType==='image' && $bgImageUrl) ? ' background-image:url(' . htmlspecialchars($bgImageUrl) . '); background-size:cover; background-position:center;' : ''; ?>">
  <header class="navbar bg-white bg-opacity-90 rounded-xl shadow-sm p-4 mx-auto mt-6 w-full px-4 backdrop-blur-3">
    <div class="flex items-center justify-between">
      <a href="<?php echo $basePath; ?>/" class="text-primary no-underline font-fumofumo text-xl"><?php echo htmlspecialchars($siteConfig['site']['title'] ?? 'Fumomo'); ?></a>
      <!-- 移动端汉堡按钮 -->
      <button id="nav-toggle" class="md:hidden flex flex-col bg-transparent border-none cursor-pointer p-3 relative z-50 w-10 h-10 justify-center items-center" aria-label="切换菜单">
        <span class="w-6 h-0.5 my-0.5 rounded transition-all duration-300 bg-primary"></span>
        <span class="w-6 h-0.5 my-0.5 rounded transition-all duration-300 bg-primary"></span>
        <span class="w-6 h-0.5 my-0.5 rounded transition-all duration-300 bg-primary"></span>
      </button>
      <!-- 桌面端导航 -->
      <nav class="hidden md:flex items-center gap-4">
        <a href="<?php echo $basePath; ?>/" class="text-muted no-underline hover:text-primary relative py-2 px-3 rounded-md <?php echo ($currentPage==='home') ? 'active' : ''; ?>">首页</a>
        <a href="<?php echo $basePath; ?>/articles.php" class="text-muted no-underline hover:text-primary relative py-2 px-3 rounded-md <?php echo ($currentPage==='articles') ? 'active' : ''; ?>">文章</a>
        <a href="<?php echo $basePath; ?>/projects.php" class="text-muted no-underline hover:text-primary relative py-2 px-3 rounded-md <?php echo ($currentPage==='projects') ? 'active' : ''; ?>">项目</a>
        <a href="<?php echo $basePath; ?>/friends.php" class="text-muted no-underline hover:text-primary relative py-2 px-3 rounded-md <?php echo ($currentPage==='friends') ? 'active' : ''; ?>">友链</a>
        <a href="<?php echo $basePath; ?>/website.php" class="text-muted no-underline hover:text-primary relative py-2 px-3 rounded-md <?php echo ($currentPage==='website') ? 'active' : ''; ?>">网站</a>
        <a href="<?php echo $basePath; ?>/about.php" class="text-muted no-underline hover:text-primary relative py-2 px-3 rounded-md <?php echo ($currentPage==='about') ? 'active' : ''; ?>">关于</a>
        <?php if (isLoggedIn()) : ?>
          <a href="<?php echo $basePath; ?>/admin/" class="text-primary no-underline <?php echo $__isAdmin ? 'active' : ''; ?>">后台</a>
        <?php endif; ?>
      </nav>
    </div>
  </header>

  <!-- 移动端菜单遮罩（默认隐藏，无 Tailwind 也隐藏） -->
  <div id="nav-overlay" hidden class="hidden fixed inset-0 bg-black bg-opacity-20 z-30 md:hidden transition-opacity duration-300"></div>
  <!-- 移动端菜单（默认隐藏，无 Tailwind 也隐藏） -->
  <div id="nav-menu" hidden class="hidden fixed top-20 right-4 w-48 bg-white bg-opacity-95 backdrop-blur-md shadow-lg border border-purple-100 rounded-lg z-50 py-3 px-3 md:hidden">
    <ul class="list-none m-0 p-0 flex flex-col gap-1">
      <li class="w-full"><a href="<?php echo $basePath; ?>/" class="no-underline font-medium transition-all duration-300 relative py-2 px-3 rounded-md block hover:bg-primary-10 text-center text-sm text-muted hover:text-primary <?php echo ($currentPage==='home') ? 'active' : ''; ?>">首页</a></li>
      <li class="w-full"><a href="<?php echo $basePath; ?>/articles.php" class="no-underline font-medium transition-all duration-300 relative py-2 px-3 rounded-md block hover:bg-primary-10 text-center text-sm text-muted hover:text-primary <?php echo ($currentPage==='articles') ? 'active' : ''; ?>">文章</a></li>
      <li class="w-full"><a href="<?php echo $basePath; ?>/projects.php" class="no-underline font-medium transition-all duration-300 relative py-2 px-3 rounded-md block hover:bg-primary-10 text-center text-sm text-muted hover:text-primary <?php echo ($currentPage==='projects') ? 'active' : ''; ?>">项目</a></li>
      <li class="w-full"><a href="<?php echo $basePath; ?>/friends.php" class="no-underline font-medium transition-all duration-300 relative py-2 px-3 rounded-md block hover:bg-primary-10 text-center text-sm text-muted hover:text-primary <?php echo ($currentPage==='friends') ? 'active' : ''; ?>">友链</a></li>
      <li class="w-full"><a href="<?php echo $basePath; ?>/website.php" class="no-underline font-medium transition-all duration-300 relative py-2 px-3 rounded-md block hover:bg-primary-10 text-center text-sm text-muted hover:text-primary <?php echo ($currentPage==='website') ? 'active' : ''; ?>">网站</a></li>
      <li class="w-full"><a href="<?php echo $basePath; ?>/about.php" class="no-underline font-medium transition-all duration-300 relative py-2 px-3 rounded-md block hover:bg-primary-10 text-center text-sm text-muted hover:text-primary <?php echo ($currentPage==='about') ? 'active' : ''; ?>">关于</a></li>
      <?php if (isLoggedIn()) : ?>
      <li class="w-full"><a href="<?php echo $basePath; ?>/admin/" class="no-underline font-medium transition-all duration-300 relative py-2 px-3 rounded-md block hover:bg-primary-10 text-center text-sm text-primary <?php echo $__isAdmin ? 'active' : ''; ?>">后台</a></li>
      <?php endif; ?>
    </ul>
  </div>

  <main class="mx-auto mt-8 w-full px-4">
<?php
  // 站点公告：读取内容并准备弹窗（仅前台，且内容非空时）
  $noticeFile = __DIR__ . '/../data/notice.txt';
  $noticeHtml = '';
  if (is_file($noticeFile)) { $noticeHtml = trim(file_get_contents($noticeFile)); }
  $noticeHash = $noticeHtml !== '' ? md5($noticeHtml) : '';
  if (!$__isAdmin && $noticeHtml !== ''):
?>
  <!-- 站点公告弹窗（首次访问显示），支持 HTML 内容与图片 -->
  <div id="site-notice-overlay" class="hidden fixed inset-0 bg-black bg-opacity-30 z-50"></div>
  <div id="site-notice-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center" data-hash="<?php echo htmlspecialchars($noticeHash); ?>">
    <div class="modal-panel relative bg-white rounded-2xl shadow-lg max-w-2xl w-[92%] md:w-[700px] p-6">
      <div class="flex justify-between items-center mb-3">
        <h3 class="text-primary text-xl m-0">站点公告</h3>
        <button type="button" id="closeSiteNotice" class="text-muted hover:text-primary" aria-label="关闭公告">
          <i class="fa-solid fa-xmark"></i>
        </button>
      </div>
      <div class="leading-relaxed text-base text-muted">
        <?php echo $noticeHtml; ?>
      </div>
    </div>
  </div>
<?php endif; ?>