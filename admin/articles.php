<?php
require_once __DIR__ . '/../includes/config.php';
requireLogin();
$currentPage = 'articles';
$items = readData('articles', []);
require_once __DIR__ . '/../includes/header.php';
?>
<section class="bg-white rounded-3xl shadow-lg p-12 max-w-4xl w-full mx-auto" style="box-shadow: 0 4px 24px rgba(139,90,140,0.08);">
  <h1 class="text-primary text-3xl mb-2 text-center font-fumofumo">文章管理</h1>
  <p class="text-muted text-center mb-8">新增、编辑、删除文章条目（标题、描述、链接）</p>

  <?php if (!empty($_GET['ok'])): ?>
    <div class="mb-4 rounded-lg bg-green-50 text-green-700 border border-green-200 px-4 py-2">已保存设置。</div>
  <?php elseif (!empty($_GET['error'])): ?>
    <div class="mb-4 rounded-lg bg-red-50 text-red-700 border border-red-200 px-4 py-2">保存失败或校验错误，请重试。</div>
  <?php endif; ?>

  <h3 class="text-primary text-xl mb-2">RSSHub订阅配置</h3>
  <?php $articlesCfg = $siteConfig['articles'] ?? ['source' => 'manual', 'rsshubUrl' => '', 'cacheTtlSec' => 600]; ?>
  <form method="post" action="<?php echo $basePath; ?>/admin/save_articles_settings.php" class="space-y-3">
    <?php echo csrfField(); ?>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div class="flex items-center gap-4">
        <label class="inline-flex items-center gap-2"><input type="radio" name="articles_source" value="manual" <?php echo (($articlesCfg['source'] ?? 'manual') !== 'rsshub') ? 'checked' : ''; ?>> <span>手动维护</span></label>
        <label class="inline-flex items-center gap-2"><input type="radio" name="articles_source" value="rsshub" <?php echo (($articlesCfg['source'] ?? 'manual') === 'rsshub') ? 'checked' : ''; ?>> <span>RSSHub订阅</span></label>
      </div>
      <input type="url" name="articles_rsshub_url" placeholder="RSSHub订阅地址，如 https://rsshub.app/github/release/vercel/next.js" value="<?php echo htmlspecialchars($articlesCfg['rsshubUrl'] ?? ''); ?>" class="border border-gray-300 rounded px-3 py-2 w-full md:col-span-2" />
      <input type="number" name="articles_cache_ttl" placeholder="缓存时间（秒），建议 600" value="<?php echo intval($articlesCfg['cacheTtlSec'] ?? 600); ?>" min="60" step="60" class="border border-gray-300 rounded px-3 py-2" />
    </div>
    <div class="text-muted text-sm">前台文章页支持“RSS订阅/手动添加”分类切换；此处的“订阅/手动维护”用于设定默认展示的分类（仍可在前台切换）。缓存时间至少 60 秒。</div>
    <button type="submit" class="bg-primary text-white rounded px-4 py-2">保存订阅配置</button>
  </form>

  <hr class="my-6" />

  <h3 class="text-primary text-xl mb-2">新增文章</h3>
  <form method="post" action="<?php echo $basePath; ?>/admin/save_items.php" class="space-y-3">
    <?php echo csrfField(); ?>
    <input type="hidden" name="type" value="articles" />
    <input type="hidden" name="action" value="add" />
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <input type="text" name="title" placeholder="标题" class="border border-gray-300 rounded px-3 py-2" required />
      <input type="text" name="description" placeholder="描述" class="border border-gray-300 rounded px-3 py-2" />
      <input type="url" name="url" placeholder="链接" class="border border-gray-300 rounded px-3 py-2" />
    </div>
    <button type="submit" class="bg-primary text-white rounded px-4 py-2">添加</button>
  </form>

  <hr class="my-6" />

  <h3 class="text-primary text-xl mb-2">已有文章</h3>
  <div class="space-y-4">
    <?php foreach ($items as $i => $it): ?>
      <form method="post" action="<?php echo $basePath; ?>/admin/save_items.php" class="bg-gradient-to-br from-gray-50 to-pink-50 rounded-2xl p-4 border border-gray-200 grid grid-cols-1 md:grid-cols-4 gap-3">
        <?php echo csrfField(); ?>
        <input type="hidden" name="type" value="articles" />
        <input type="hidden" name="action" value="update" />
        <input type="hidden" name="index" value="<?php echo $i; ?>" />
        <input type="text" name="title" value="<?php echo htmlspecialchars($it['title'] ?? ''); ?>" class="border border-gray-300 rounded px-3 py-2" />
        <input type="text" name="description" value="<?php echo htmlspecialchars($it['description'] ?? ''); ?>" class="border border-gray-300 rounded px-3 py-2" />
        <input type="url" name="url" value="<?php echo htmlspecialchars($it['url'] ?? ''); ?>" class="border border-gray-300 rounded px-3 py-2" />
        <div class="flex items-center gap-2">
          <button type="submit" class="bg-primary text-white rounded px-4 py-2">保存</button>
          <a class="bg-white border border-gray-300 rounded px-4 py-2 text-muted no-underline" href="<?php echo $basePath; ?>/admin/delete_item.php?type=articles&index=<?php echo $i; ?>&csrf=<?php echo urlencode(csrfToken()); ?>">删除</a>
        </div>
      </form>
    <?php endforeach; ?>
  </div>

  <div class="mt-6 flex gap-3">
    <a href="<?php echo $basePath; ?>/admin/" class="bg-white border border-gray-200 rounded px-4 py-2 text-muted no-underline">返回后台</a>
  </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>