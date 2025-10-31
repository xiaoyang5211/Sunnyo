<?php
require_once __DIR__ . '/../includes/config.php';
requireLogin();
$currentPage = 'projects';
$items = readData('projects', []);
require_once __DIR__ . '/../includes/header.php';
?>
<section class="bg-white rounded-3xl shadow-lg p-12 max-w-4xl w-full mx-auto" style="box-shadow: 0 4px 24px rgba(139,90,140,0.08);">
  <h1 class="text-primary text-3xl mb-2 text-center font-fumofumo">项目管理</h1>
  <p class="text-muted text-center mb-8">新增、编辑、删除项目（标题、描述、链接）</p>

  <h3 class="text-primary text-xl mb-2">新增项目</h3>
  <form method="post" action="<?php echo $basePath; ?>/admin/save_items.php" class="space-y-3">
    <?php echo csrfField(); ?>
    <input type="hidden" name="type" value="projects" />
    <input type="hidden" name="action" value="add" />
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <input type="text" name="title" placeholder="标题" class="border border-gray-300 rounded px-3 py-2" required />
      <input type="text" name="description" placeholder="描述" class="border border-gray-300 rounded px-3 py-2" />
      <input type="url" name="url" placeholder="链接" class="border border-gray-300 rounded px-3 py-2" />
    </div>
    <button type="submit" class="bg-primary text-white rounded px-4 py-2">添加</button>
  </form>

  <hr class="my-6" />

  <h3 class="text-primary text-xl mb-2">已有项目</h3>
  <div class="space-y-4">
    <?php foreach ($items as $i => $it): ?>
      <form method="post" action="<?php echo $basePath; ?>/admin/save_items.php" class="bg-gradient-to-br from-gray-50 to-pink-50 rounded-2xl p-4 border border-gray-200 grid grid-cols-1 md:grid-cols-4 gap-3">
        <?php echo csrfField(); ?>
        <input type="hidden" name="type" value="projects" />
        <input type="hidden" name="action" value="update" />
        <input type="hidden" name="index" value="<?php echo $i; ?>" />
        <input type="text" name="title" value="<?php echo htmlspecialchars($it['title'] ?? ''); ?>" class="border border-gray-300 rounded px-3 py-2" />
        <input type="text" name="description" value="<?php echo htmlspecialchars($it['description'] ?? ''); ?>" class="border border-gray-300 rounded px-3 py-2" />
        <input type="url" name="url" value="<?php echo htmlspecialchars($it['url'] ?? ''); ?>" class="border border-gray-300 rounded px-3 py-2" />
        <div class="flex items-center gap-2">
          <button type="submit" class="bg-primary text-white rounded px-4 py-2">保存</button>
          <a class="bg-white border border-gray-300 rounded px-4 py-2 text-muted no-underline" href="<?php echo $basePath; ?>/admin/delete_item.php?type=projects&index=<?php echo $i; ?>&csrf=<?php echo urlencode(csrfToken()); ?>">删除</a>
        </div>
      </form>
    <?php endforeach; ?>
  </div>

  <div class="mt-6 flex gap-3">
    <a href="<?php echo $basePath; ?>/admin/" class="bg-white border border-gray-200 rounded px-4 py-2 text-muted no-underline">返回后台</a>
  </div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>