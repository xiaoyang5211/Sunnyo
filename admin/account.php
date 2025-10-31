<?php
require_once __DIR__ . '/../includes/config.php';
requireLogin();
$currentPage = 'home';
$page_title = '账号设置';
require_once __DIR__ . '/../includes/header.php';
?>
<section class="bg-white rounded-3xl shadow-lg p-12 max-w-xl w-full mx-auto" style="box-shadow: 0 4px 24px rgba(139,90,140,0.08);">
  <h1 class="text-primary text-3xl mb-2 text-center font-fumofumo">账号设置</h1>
  <p class="text-muted text-center mb-6">修改后台登录的用户名与密码</p>

  <?php if (isset($_GET['ok'])): ?>
    <div class="bg-green-50 border border-green-200 text-green-700 rounded p-3 mb-4 text-sm">保存成功</div>
  <?php elseif (isset($_GET['error'])): ?>
    <div class="bg-red-50 border border-red-200 text-red-700 rounded p-3 mb-4 text-sm">保存失败：<?php echo htmlspecialchars($_GET['error']); ?></div>
  <?php endif; ?>

  <form action="<?php echo $basePath; ?>/admin/save_account.php" method="post" class="space-y-4">
    <?php echo csrfField(); ?>
    <div>
      <label class="block text-sm text-muted mb-1">当前用户名</label>
      <div class="w-full border border-gray-200 rounded px-3 py-2 bg-gray-50 text-gray-600"><?php echo htmlspecialchars($adminConfig['username'] ?? ''); ?></div>
    </div>
    <div>
      <label class="block text-sm text-muted mb-1">新用户名</label>
      <input type="text" name="username" value="<?php echo htmlspecialchars($adminConfig['username'] ?? ''); ?>" class="w-full border border-gray-300 rounded px-3 py-2" required maxlength="50" />
    </div>
    <div>
      <label class="block text-sm text-muted mb-1">当前密码（必填，用于验证）</label>
      <input type="password" name="current_password" class="w-full border border-gray-300 rounded px-3 py-2" required />
    </div>
    <div>
      <label class="block text-sm text-muted mb-1">新密码（留空则不修改，至少 6 位）</label>
      <input type="password" name="password" class="w-full border border-gray-300 rounded px-3 py-2" minlength="6" />
    </div>
    <div>
      <label class="block text-sm text-muted mb-1">确认新密码</label>
      <input type="password" name="password_confirm" class="w-full border border-gray-300 rounded px-3 py-2" minlength="6" />
    </div>
    <div class="flex items-center gap-2">
      <input type="checkbox" id="store_plain" name="store_plain" value="1" class="w-4 h-4" />
      <label for="store_plain" class="text-sm text-muted">以明文存储密码（不推荐，默认使用加密哈希）</label>
    </div>
    <p class="text-xs text-gray-500">提示：修改用户名或密码前需正确填写当前密码。</p>
    <div class="pt-2">
      <button type="submit" class="w-full bg-primary text-white rounded py-2 font-medium hover:opacity-90 transform hover:scale-105 transition-all">保存</button>
    </div>
  </form>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>