<?php
require_once __DIR__ . '/../includes/config.php';
$currentPage = 'articles';
require_once __DIR__ . '/../includes/header.php';
if (!function_exists('readData')) { function readData(string $name, $default = []) { return $default; } }
?>

<section class="bg-white rounded-2xl shadow-lg p-6 w-full mx-auto px-4 mb-12 component-card" style="box-shadow: 0 4px 24px rgba(139,90,140,0.08);">
  <h1 class="text-primary text-4xl mb-2 text-center font-fumofumo">我的文章</h1>
  <p class="text-muted text-xl text-center mb-8">技术分享与生活感悟</p>
  <?php 
    // 同时准备 RSS 与手动两类数据
    $articlesCfg = $siteConfig['articles'] ?? ['source' => 'manual'];
    $rssItems = [];
    if (!empty($articlesCfg['rsshubUrl'])) {
      $ttl = intval($articlesCfg['cacheTtlSec'] ?? 600);
      $rssItems = fetchRssHubItems($articlesCfg['rsshubUrl'], $ttl, 50);
    }
    $manualItems = readData('articles', []);

    $rssCount = is_array($rssItems) ? count($rssItems) : 0;
    $manualCount = is_array($manualItems) ? count($manualItems) : 0;

    // 分类（tab）：rss 或 manual；默认优先遵循后台 source 设置，其次择有数据的分类
    $tab = strtolower(trim($_GET['tab'] ?? ''));
    if ($tab !== 'rss' && $tab !== 'manual') {
      $preferred = (($articlesCfg['source'] ?? 'manual') === 'rsshub') ? 'rss' : 'manual';
      if ($preferred === 'rss' && $rssCount === 0) { $preferred = ($manualCount > 0) ? 'manual' : 'rss'; }
      if ($preferred === 'manual' && $manualCount === 0 && $rssCount > 0) { $preferred = 'rss'; }
      $tab = $preferred;
    }
    $items = ($tab === 'rss') ? $rssItems : $manualItems;

    // 分页参数（每类单独分页）
    $perPage = 5;
    $total = is_array($items) ? count($items) : 0;
    $page = max(1, intval($_GET['page'] ?? 1));
    $totalPages = max(1, (int)ceil($total / max(1, $perPage)));
    if ($page > $totalPages) { $page = $totalPages; }
    $offset = ($page - 1) * $perPage;
    $pageItems = array_slice($items, $offset, $perPage);

    $base = ($basePath !== '' ? $basePath : '');
    $mkUrl = function($t, $p) use ($base) {
      $p = max(1, intval($p));
      $t = ($t === 'manual') ? 'manual' : 'rss';
      return $base . '/articles.php?tab=' . urlencode($t) . '&page=' . $p;
    };
    $prevUrl = $mkUrl($tab, max(1, $page - 1));
    $nextUrl = $mkUrl($tab, min($totalPages, $page + 1));
  ?>

  <!-- 分类切换 -->
  <div class="flex items-center justify-center gap-3 mb-6">
    <a href="<?php echo htmlspecialchars($mkUrl('rss', 1)); ?>" class="px-4 py-2 rounded border <?php echo ($tab==='rss') ? 'bg-primary text-white border-primary' : 'bg-white text-muted border-gray-300'; ?> no-underline hover:opacity-90">RSS订阅（<?php echo $rssCount; ?>）</a>
    <a href="<?php echo htmlspecialchars($mkUrl('manual', 1)); ?>" class="px-4 py-2 rounded border <?php echo ($tab==='manual') ? 'bg-primary text-white border-primary' : 'bg-white text-muted border-gray-300'; ?> no-underline hover:opacity-90">手动添加（<?php echo $manualCount; ?>）</a>
  </div>

  <?php if (empty($items)): ?>
    <?php if ($tab === 'rss'): ?>
      <div class="text-center text-muted">RSS订阅暂无内容。请在 <a href="/admin/articles.php" class="text-primary no-underline hover:underline">后台文章配置</a> 填写 RSSHub 路由。</div>
    <?php else: ?>
      <div class="text-center text-muted">暂无手动文章，前往 <a href="/admin/articles.php" class="text-primary no-underline hover:underline">后台文章管理</a> 添加。</div>
    <?php endif; ?>
  <?php else: ?>
    <div class="space-y-6">
      <?php foreach ($pageItems as $a): ?>
        <a href="<?php echo htmlspecialchars($a['url'] ?? '#'); ?>" target="_blank" rel="noopener noreferrer" class="block p-6 no-underline text-inherit h-full relative bg-gradient-to-br from-pink-50 to-purple-50 rounded-2xl border border-purple-200 hover:shadow-xl transition-all duration-200 hover:-translate-y-0.5">
          <h3 class="text-primary text-xl mb-3 leading-snug font-fumofumo"><?php echo htmlspecialchars($a['title'] ?? '未命名'); ?></h3>
          <p class="text-muted text-sm leading-relaxed mb-8 line-clamp-3 overflow-hidden"><?php echo htmlspecialchars($a['description'] ?? ''); ?></p>
          <div class="flex items-center justify-between">
            <?php if (!empty($a['pubDate'])): ?>
              <time class="text-gray-400 text-xs font-normal"><?php echo htmlspecialchars($a['pubDate']); ?></time>
            <?php endif; ?>
            <span class="inline-flex items-center gap-1 text-primary font-medium">点击前往 <i class="fa-solid fa-arrow-right"></i></span>
          </div>
        </a>
      <?php endforeach; ?>
    </div>

    <!-- 分页导航（当前分类） -->
    <div class="mt-8 flex items-center justify-center gap-4">
      <?php if ($page > 1): ?>
        <a href="<?php echo htmlspecialchars($prevUrl); ?>" class="bg-white border border-gray-300 rounded px-4 py-2 text-muted no-underline hover:text-primary">上一页</a>
      <?php else: ?>
        <span class="bg-white border border-gray-200 rounded px-4 py-2 text-gray-400 cursor-not-allowed">上一页</span>
      <?php endif; ?>
      <span class="text-muted text-sm">第 <?php echo $page; ?> / <?php echo $totalPages; ?> 页（<?php echo ($tab==='rss') ? 'RSS订阅' : '手动添加'; ?>）</span>
      <?php if ($page < $totalPages): ?>
        <a href="<?php echo htmlspecialchars($nextUrl); ?>" class="bg-white border border-gray-300 rounded px-4 py-2 text-muted no-underline hover:text-primary">下一页</a>
      <?php else: ?>
        <span class="bg-white border border-gray-200 rounded px-4 py-2 text-gray-400 cursor-not-allowed">下一页</span>
      <?php endif; ?>
    </div>
  <?php endif; ?>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>