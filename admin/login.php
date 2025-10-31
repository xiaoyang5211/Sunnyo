<?php
$currentPage = 'home'; // 登录页沿用首页风格
require_once __DIR__ . '/../includes/config.php';
?>
<?php require_once __DIR__ . '/../includes/header.php'; ?>

<section class="bg-white rounded-3xl shadow-lg p-12 max-w-md w-full mx-auto" style="box-shadow: 0 4px 24px rgba(139,90,140,0.08);">
  <h1 class="text-primary text-3xl mb-2 text-center font-fumofumo">后台登录</h1>
  <p class="text-muted text-center mb-6">使用与首页一致的风格</p>
  <?php if (isset($_GET['error'])): ?>
    <div class="bg-red-50 border border-red-200 text-red-600 rounded p-3 mb-4 text-sm">用户名或密码错误</div>
  <?php endif; ?>
  <form action="<?php echo $basePath; ?>/admin/auth.php" method="post" class="space-y-4">
    <?php echo csrfField(); ?>
    <div>
      <label class="block text-sm text-muted mb-1">用户名</label>
      <input type="text" name="username" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-300" required />
    </div>
    <div>
      <label class="block text-sm text-muted mb-1">密码</label>
      <input type="password" name="password" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-300" required />
    </div>
    <button type="submit" class="w-full bg-primary text-white rounded py-2 font-medium hover:opacity-90 transform hover:scale-105 transition-all">登录</button>
  </form>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>