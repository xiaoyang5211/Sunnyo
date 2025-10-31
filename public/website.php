<?php
$currentPage = 'website';
require_once __DIR__ . '/../includes/header.php';
?>

<section class="bg-white rounded-3xl shadow-lg p-12 w-full mx-auto px-4" style="box-shadow: 0 4px 24px rgba(139,90,140,0.08);">
  <h1 class="text-primary text-4xl mb-2 text-center font-fumofumo">我的网站</h1>
  <p class="text-muted text-xl text-center mb-8">正在运行的网站信息</p>
  <?php $websites = readData('websites', []); ?>
  <?php if (empty($websites)): ?>
    <div class="text-center text-muted">暂无网站数据，前往 <a href="/admin/websites.php" class="text-primary no-underline hover:underline">后台网站管理</a> 添加。</div>
  <?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <?php foreach ($websites as $w): ?>
        <?php $status = checkUrlStatus($w['url'] ?? '', 60); $online = !empty($status['online']); $code = intval($status['code'] ?? 0); ?>
        <a href="<?php echo htmlspecialchars($w['url'] ?? '#'); ?>" target="_blank" rel="noopener noreferrer" class="flex items-center gap-4 bg-gradient-to-br from-pink-50 to-purple-50 rounded-2xl p-6 border border-purple-200 transition-all duration-200 hover:-translate-y-0.5 no-underline text-inherit hover:shadow-xl">
          <div class="flex-1 text-left">
            <div class="flex items-center gap-3 mb-1">
              <h3 class="text-primary text-xl font-fumofumo mt-1 m-0"><?php echo htmlspecialchars($w['title'] ?? '未命名'); ?></h3>
              <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs border <?php echo $online ? 'bg-green-100 text-green-700 border-green-200' : 'bg-red-100 text-red-700 border-red-200'; ?>">
                <i class="fa-solid <?php echo $online ? 'fa-circle-check' : 'fa-circle-xmark'; ?>"></i>
                <?php echo $online ? '在线' : '离线'; ?><?php if (!$online && $code): ?> (<?php echo $code; ?>)<?php endif; ?>
              </span>
            </div>
            <p class="text-muted text-base leading-relaxed m-0"><?php echo htmlspecialchars($w['description'] ?? ''); ?></p>
            <span class="mt-2 inline-flex items-center gap-1 text-primary font-medium">点击前往 <i class="fa-solid fa-arrow-right"></i></span>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>