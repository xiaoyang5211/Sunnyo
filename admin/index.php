<?php
require_once __DIR__ . '/../includes/config.php';
requireLogin();
$currentPage = 'home';
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>

<section class="bg-white rounded-3xl shadow-lg p-12 max-w-4xl w-full mx-auto" style="box-shadow: 0 4px 24px rgba(139,90,140,0.08);">
  <h1 class="text-primary text-3xl mb-2 text-center font-fumofumo">后台控制台</h1>
  <p class="text-muted text-center mb-8">欢迎，<?php echo htmlspecialchars($adminConfig['username']); ?>！</p>

  <div class="grid grid-cols-1 gap-6 place-items-center">
    <!-- 快捷操作重构为一致的卡片网格 -->
    <div class="bg-gradient-to-br from-gray-50 to-pink-50 rounded-2xl p-6 border border-gray-200">
      <h3 class="text-primary text-xl mb-4 font-fumofumo">快捷操作</h3>
      <div class="grid grid-cols-2 md:grid-cols-3 gap-3 justify-items-center w-fit mx-auto">
        <a href="<?php echo $basePath; ?>/" class="group flex items-center gap-2 px-4 py-3 rounded-xl bg-white border border-purple-200 text-primary no-underline hover:bg-primary-10 transition">
          <i class="fa-solid fa-house"></i>
          <span class="text-sm">打开首页</span>
        </a>
        <a href="<?php echo $basePath; ?>/admin/settings.php" class="group flex items-center gap-2 px-4 py-3 rounded-xl bg-white border border-purple-200 text-primary no-underline hover:bg-primary-10 transition">
          <i class="fa-solid fa-sliders"></i>
          <span class="text-sm">站点设置</span>
        </a>
        <a href="<?php echo $basePath; ?>/admin/articles.php" class="group flex items-center gap-2 px-4 py-3 rounded-xl bg-white border border-purple-200 text-primary no-underline hover:bg-primary-10 transition">
          <i class="fa-solid fa-file-lines"></i>
          <span class="text-sm">文章配置</span>
        </a>
        <a href="<?php echo $basePath; ?>/admin/projects.php" class="group flex items-center gap-2 px-4 py-3 rounded-xl bg-white border border-purple-200 text-primary no-underline hover:bg-primary-10 transition">
          <i class="fa-solid fa-diagram-project"></i>
          <span class="text-sm">项目配置</span>
        </a>
        <a href="<?php echo $basePath; ?>/admin/friends.php" class="group flex items-center gap-2 px-4 py-3 rounded-xl bg-white border border-purple-200 text-primary no-underline hover:bg-primary-10 transition">
          <i class="fa-solid fa-user-group"></i>
          <span class="text-sm">友链配置</span>
        </a>
        <a href="<?php echo $basePath; ?>/admin/websites.php" class="group flex items-center gap-2 px-4 py-3 rounded-xl bg-white border border-purple-200 text-primary no-underline hover:bg-primary-10 transition">
          <i class="fa-solid fa-globe"></i>
          <span class="text-sm">网站配置</span>
        </a>
        <a href="<?php echo $basePath; ?>/admin/about.php" class="group flex items-center gap-2 px-4 py-3 rounded-xl bg-white border border-purple-200 text-primary no-underline hover:bg-primary-10 transition">
          <i class="fa-solid fa-id-card"></i>
          <span class="text-sm">关于页配置</span>
        </a>
        <a href="<?php echo $basePath; ?>/admin/account.php" class="group flex items-center gap-2 px-4 py-3 rounded-xl bg-white border border-purple-200 text-primary no-underline hover:bg-primary-10 transition">
          <i class="fa-solid fa-user-gear"></i>
          <span class="text-sm">账号设置</span>
        </a>
        <a href="<?php echo $basePath; ?>/admin/notice.php" class="group flex items-center gap-2 px-4 py-3 rounded-xl bg-white border border-purple-200 text-primary no-underline hover:bg-primary-10 transition">
          <i class="fa-solid fa-bullhorn"></i>
          <span class="text-sm">站点公告</span>
        </a>
      </div>
      <div class="mt-6">
        <a href="<?php echo $basePath; ?>/admin/logout.php" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-gray-200 text-muted no-underline hover:bg-gray-50 transition">
          <i class="fa-solid fa-right-from-bracket"></i>
          <span class="text-sm">退出登录</span>
        </a>
      </div>
    </div>
  </div>

  <!-- 配置中心区域已删除，按你的要求仅保留快捷操作 -->
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>