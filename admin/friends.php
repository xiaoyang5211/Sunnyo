<?php
require_once __DIR__ . '/../includes/config.php';
requireLogin();
$currentPage = 'friends';
$items = readData('friends', []);
$requests = readData('friend_requests', []);
require_once __DIR__ . '/../includes/header.php';
?>
<section class="bg-white rounded-3xl shadow-lg p-12 max-w-4xl w-full mx-auto" style="box-shadow: 0 4px 24px rgba(139,90,140,0.08);">
  <h1 class="text-primary text-3xl mb-2 text-center font-fumofumo">友链管理</h1>
  <p class="text-muted text-center mb-8">新增、编辑、删除友链（标题、描述、链接、头像）</p>

  <h3 class="text-primary text-xl mb-2">新增友链</h3>
  <form method="post" action="<?php echo $basePath; ?>/admin/save_items.php" class="space-y-3">
    <?php echo csrfField(); ?>
    <input type="hidden" name="type" value="friends" />
    <input type="hidden" name="action" value="add" />
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <input type="text" name="title" placeholder="标题" class="border border-gray-300 rounded px-3 py-2" required />
      <input type="text" name="description" placeholder="描述（选填）" class="border border-gray-300 rounded px-3 py-2" />
      <input type="url" name="url" placeholder="链接（http/https）" class="border border-gray-300 rounded px-3 py-2" required />
      <input type="url" name="avatar" placeholder="头像链接（选填，http/https）" class="border border-gray-300 rounded px-3 py-2" />
    </div>
    <button type="submit" class="bg-primary text-white rounded px-4 py-2">添加</button>
  </form>

  <hr class="my-6" />

  <h3 class="text-primary text-xl mb-2">待审核友链申请</h3>
  <?php if (empty($requests)): ?>
    <p class="text-muted">暂无待审核申请。</p>
  <?php else: ?>
    <div class="space-y-4">
      <?php foreach ($requests as $ri => $req): ?>
        <div class="bg-gradient-to-br from-gray-50 to-pink-50 rounded-2xl p-4 border border-gray-200">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div class="md:col-span-2 flex items-center gap-3">
              <?php if (!empty($req['avatar'])): ?>
                <img src="<?php echo htmlspecialchars($req['avatar']); ?>" alt="avatar" class="w-10 h-10 rounded-full object-cover" />
              <?php endif; ?>
              <div>
                <div class="text-sm text-muted">标题</div>
                <div class="font-medium"><?php echo htmlspecialchars($req['title'] ?? ''); ?></div>
              </div>
            </div>
            <div>
              <div class="text-sm text-muted">链接</div>
              <a href="<?php echo htmlspecialchars($req['url'] ?? ''); ?>" target="_blank" class="text-primary no-underline">
                <?php echo htmlspecialchars($req['url'] ?? ''); ?>
              </a>
            </div>
            <div>
              <div class="text-sm text-muted">联系邮箱</div>
              <div><?php echo htmlspecialchars($req['contactEmail'] ?? ''); ?></div>
            </div>
            <div class="md:col-span-2">
              <div class="text-sm text-muted">描述</div>
              <div><?php echo htmlspecialchars($req['description'] ?? ''); ?></div>
            </div>
            <div class="md:col-span-2 text-sm text-muted">
              提交时间：<?php echo isset($req['submittedAt']) ? date('Y-m-d H:i', (int)$req['submittedAt']) : '未知'; ?>
            </div>
          </div>
          <div class="mt-3 flex gap-2">
            <form method="post" action="<?php echo $basePath; ?>/admin/process_friend_request.php">
              <?php echo csrfField(); ?>
              <input type="hidden" name="index" value="<?php echo $ri; ?>" />
              <input type="hidden" name="action" value="approve" />
              <button type="submit" class="bg-primary text-white rounded px-4 py-2">批准并加入友链</button>
            </form>
            <form method="post" action="<?php echo $basePath; ?>/admin/process_friend_request.php">
              <?php echo csrfField(); ?>
              <input type="hidden" name="index" value="<?php echo $ri; ?>" />
              <input type="hidden" name="action" value="reject" />
              <button type="submit" class="bg-white border border-gray-300 text-muted rounded px-4 py-2">拒绝</button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <hr class="my-6" />

  <h3 class="text-primary text-xl mb-2">已有友链</h3>
  <div class="space-y-4">
    <?php foreach ($items as $i => $it): ?>
      <form method="post" action="<?php echo $basePath; ?>/admin/save_items.php" class="bg-gradient-to-br from-gray-50 to-pink-50 rounded-2xl p-4 border border-gray-200 grid grid-cols-1 md:grid-cols-5 gap-3">
        <?php echo csrfField(); ?>
        <input type="hidden" name="type" value="friends" />
        <input type="hidden" name="action" value="update" />
        <input type="hidden" name="index" value="<?php echo $i; ?>" />
        <input type="text" name="title" value="<?php echo htmlspecialchars($it['title'] ?? ''); ?>" class="border border-gray-300 rounded px-3 py-2" required />
        <input type="text" name="description" value="<?php echo htmlspecialchars($it['description'] ?? ''); ?>" class="border border-gray-300 rounded px-3 py-2" />
        <input type="url" name="url" value="<?php echo htmlspecialchars($it['url'] ?? ''); ?>" class="border border-gray-300 rounded px-3 py-2" required />
        <input type="url" name="avatar" value="<?php echo htmlspecialchars($it['avatar'] ?? ''); ?>" class="border border-gray-300 rounded px-3 py-2" placeholder="头像链接（选填）" />
        <div class="flex items-center gap-2">
          <button type="submit" class="bg-primary text-white rounded px-4 py-2">保存</button>
          <a class="bg-white border border-gray-300 rounded px-4 py-2 text-muted no-underline" href="<?php echo $basePath; ?>/admin/delete_item.php?type=friends&index=<?php echo $i; ?>&csrf=<?php echo urlencode(csrfToken()); ?>">删除</a>
        </div>
      </form>
    <?php endforeach; ?>
  </div>

  <div class="mt-6 flex gap-3">
    <a href="<?php echo $basePath; ?>/admin/" class="bg-white border border-gray-200 rounded px-4 py-2 text-muted no-underline">返回后台</a>
  </div>
</section>
<script>
  (function(){
    const base = "<?php echo $basePath; ?>";
    const refreshUrl = base + "/admin/friends.php?ok=1";
    document.addEventListener('submit', async (e) => {
      const form = e.target;
      if (!(form instanceof HTMLFormElement)) return;
      const action = form.getAttribute('action') || '';
      if (!/\/admin\/(save_items|process_friend_request)\.php$/.test(action)) return;
      e.preventDefault();
      const fd = new FormData(form);
      try { await fetch(action, { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } }); } catch(_) {}
      if (window.__navTo) { window.__navTo(refreshUrl); } else { window.location.href = refreshUrl; }
    }, true);
  })();
</script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>