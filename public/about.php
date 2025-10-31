<?php
$currentPage = 'about';
require_once __DIR__ . '/../includes/header.php';
?>

<section class="bg-white rounded-3xl shadow-lg p-12 w-full mx-auto px-4" style="box-shadow: 0 4px 24px rgba(139,90,140,0.08);">
  <h1 class="text-primary text-4xl mb-2 text-center font-fumofumo">关于我</h1>
  <p class="text-muted text-xl text-center mb-8">个人信息与关于页内容</p>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
      <img src="<?php echo htmlspecialchars($siteConfig['personal']['avatar']); ?>" alt="头像" class="w-24 h-24 rounded-full object-cover mx-auto mb-4" />
      <h2 class="text-2xl text-primary text-center mb-2"><?php echo htmlspecialchars($siteConfig['personal']['name']); ?></h2>
      <p class="text-muted text-center mb-4"><?php echo htmlspecialchars($siteConfig['personal']['bio']); ?></p>
    </div>
    <ul class="list-none text-muted m-0 p-0 space-y-2">
      <li><strong>地理位置：</strong><?php echo htmlspecialchars($siteConfig['personal']['location']); ?></li>
      <li><strong>兴趣爱好：</strong><?php echo htmlspecialchars($siteConfig['personal']['hobby']); ?></li>
      <li><strong>学习中：</strong><?php echo htmlspecialchars($siteConfig['personal']['learning']); ?></li>
    </ul>
  </div>
</section>

<section class="bg-white rounded-3xl shadow-lg p-12 w-full mx-auto px-4" style="box-shadow: 0 4px 24px rgba(139,90,140,0.08);">
  <h2 class="text-2xl text-primary mb-4">关于我</h2>
  <?php $aboutContent = $siteConfig['aboutContent'] ?? ''; ?>
  <?php if (empty($aboutContent)): ?>
    <p class="text-muted">暂未填写关于页内容，前往 <a href="/admin/about.php" class="text-primary no-underline hover:underline">后台关于页管理</a> 编辑。</p>
  <?php else: ?>
    <div class="text-muted leading-relaxed" id="about-content"><?php echo $aboutContent; ?></div>
  <?php endif; ?>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>