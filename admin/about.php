<?php
require_once __DIR__ . '/../includes/config.php';
requireLogin();
$currentPage = 'about';
require_once __DIR__ . '/../includes/header.php';
$personal = $siteConfig['personal'];
$aboutContent = $siteConfig['aboutContent'] ?? '';
?>
<section class="bg-white rounded-3xl shadow-lg p-12 max-w-4xl w-full mx-auto" style="box-shadow: 0 4px 24px rgba(139,90,140,0.08);">
  <h1 class="text-primary text-3xl mb-2 text-center font-fumofumo">关于页管理</h1>
  <p class="text-muted text-center mb-8">编辑个人信息与关于页内容</p>

  <form method="post" action="<?php echo $basePath; ?>/admin/save_about.php" class="space-y-6">
    <?php echo csrfField(); ?>
    <h3 class="text-primary text-xl">个人信息</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm text-muted mb-1">昵称</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($personal['name']); ?>" class="w-full border border-gray-300 rounded px-3 py-2" />
      </div>
      <div>
        <label class="block text-sm text-muted mb-1">头像URL</label>
        <input type="url" name="avatar" value="<?php echo htmlspecialchars($personal['avatar']); ?>" class="w-full border border-gray-300 rounded px-3 py-2" />
      </div>
      <div>
        <label class="block text-sm text-muted mb-1">Bio</label>
        <input type="text" name="bio" value="<?php echo htmlspecialchars($personal['bio']); ?>" class="w-full border border-gray-300 rounded px-3 py-2" />
      </div>
      <div>
        <label class="block text-sm text-muted mb-1">地理位置</label>
        <input type="text" name="location" value="<?php echo htmlspecialchars($personal['location']); ?>" class="w-full border border-gray-300 rounded px-3 py-2" />
      </div>
      <div>
        <label class="block text-sm text-muted mb-1">兴趣爱好</label>
        <input type="text" name="hobby" value="<?php echo htmlspecialchars($personal['hobby']); ?>" class="w-full border border-gray-300 rounded px-3 py-2" />
      </div>
      <div>
        <label class="block text-sm text-muted mb-1">学习中</label>
        <input type="text" name="learning" value="<?php echo htmlspecialchars($personal['learning']); ?>" class="w-full border border-gray-300 rounded px-3 py-2" />
      </div>
      <div>
        <label class="block text-sm text-muted mb-1">GitHub</label>
        <input type="url" name="github" value="<?php echo htmlspecialchars($personal['social']['github']); ?>" class="w-full border border-gray-300 rounded px-3 py-2" />
      </div>
      <div>
        <label class="block text-sm text-muted mb-1">邮箱</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($personal['social']['email']); ?>" class="w-full border border-gray-300 rounded px-3 py-2" />
      </div>
    </div>

    <h3 class="text-primary text-xl">关于页内容</h3>
    <textarea name="aboutContent" rows="6" class="w-full border border-gray-300 rounded px-3 py-2" placeholder="在此填写关于页的详细内容（支持 HTML）"><?php echo htmlspecialchars($aboutContent); ?></textarea>
    <p class="text-xs text-gray-500">提示：内容支持 HTML 标签（如 h2、p、a、img 等）。为避免布局溢出，图片建议添加样式 <code>style=&quot;max-width:100%&quot;</code>。</p>

    <div class="mt-4 flex gap-3">
      <button type="submit" class="bg-primary text-white rounded px-5 py-2">保存</button>
      <a href="<?php echo $basePath; ?>/admin/" class="bg-white border border-gray-200 rounded px-4 py-2 text-muted no-underline">返回后台</a>
    </div>
  </form>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>