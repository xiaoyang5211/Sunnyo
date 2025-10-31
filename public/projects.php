<?php
$currentPage = 'projects';
require_once __DIR__ . '/../includes/header.php';
?>

<section class="bg-white rounded-3xl shadow-lg p-12 w-full mx-auto px-4" style="box-shadow: 0 4px 24px rgba(139,90,140,0.08);">
  <h1 class="text-primary text-4xl mb-2 text-center font-fumofumo">项目作品</h1>
  <p class="text-muted text-xl text-center mb-8">我的一些项目作品</p>
  <?php $projects = readData('projects', []); ?>
  <?php if (empty($projects)): ?>
    <div class="text-center text-muted">暂无项目，前往 <a href="/admin/projects.php" class="text-primary no-underline hover:underline">后台项目管理</a> 添加。</div>
  <?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <?php foreach ($projects as $p): ?>
        <a href="<?php echo htmlspecialchars($p['url'] ?? '#'); ?>" target="_blank" rel="noopener noreferrer" class="flex items-center gap-4 bg-gradient-to-br from-pink-50 to-purple-50 rounded-2xl p-6 border border-purple-200 no-underline text-inherit hover:shadow-xl transition-all duration-200 hover:-translate-y-0.5">
          <div class="flex-1 text-left">
            <h3 class="text-primary text-xl mb-1 font-fumofumo mt-1"><?php echo htmlspecialchars($p['title'] ?? '未命名'); ?></h3>
            <p class="text-muted text-base leading-relaxed m-0"><?php echo htmlspecialchars($p['description'] ?? ''); ?></p>
            <span class="mt-2 inline-flex items-center gap-1 text-primary font-medium">点击前往 <i class="fa-solid fa-arrow-right"></i></span>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>