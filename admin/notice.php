<?php
require_once __DIR__ . '/../includes/config.php';
requireLogin();
$currentPage = 'admin';
require_once __DIR__ . '/../includes/header.php';

$noticeFile = __DIR__ . '/../data/notice.txt';
$noticeContent = '';
if (is_file($noticeFile)) {
  $noticeContent = file_get_contents($noticeFile);
}
?>
<section class="bg-white rounded-3xl shadow-lg p-12 max-w-4xl w-full mx-auto" style="box-shadow: 0 4px 24px rgba(139,90,140,0.08);">
  <h1 class="text-primary text-3xl mb-2 text-center font-fumofumo">站点公告配置</h1>
  <p class="text-muted text-center mb-8">配置站点公告内容，支持 HTML（可插入图片、链接、段落等）。公告将按你的配置在前台弹出显示。</p>

  <?php if (!empty($_GET['ok'])): ?>
    <div class="mb-4 rounded-lg bg-green-50 text-green-700 border border-green-200 px-4 py-2">已保存公告。</div>
  <?php elseif (!empty($_GET['error'])): ?>
    <div class="mb-4 rounded-lg bg-red-50 text-red-700 border border-red-200 px-4 py-2">保存失败或校验错误，请重试。</div>
  <?php endif; ?>

  <form method="post" action="<?php echo $basePath; ?>/admin/save_notice.php" class="space-y-4">
    <?php echo csrfField(); ?>
    <label class="block text-sm text-muted mb-1">公告内容（支持 HTML）</label>
    <textarea name="notice" rows="10" class="w-full border border-gray-300 rounded px-3 py-2" placeholder="例如：&lt;h3&gt;欢迎来到本站&lt;/h3&gt;\n&lt;p&gt;这里是站点公告，支持 &lt;strong&gt;加粗&lt;/strong&gt;、&lt;em&gt;斜体&lt;/em&gt;、&lt;a href='https://example.com'&gt;链接&lt;/a&gt; 等；也支持图片：&lt;img src='https://example.com/a.jpg' style='max-width:100%' /&gt;&lt;/p&gt;" ><?php echo htmlspecialchars($noticeContent); ?></textarea>

    <div class="flex gap-3">
      <button type="submit" class="bg-primary text-white rounded px-5 py-2">保存公告</button>
      <a href="<?php echo $basePath; ?>/admin/" class="bg-white border border-gray-200 rounded px-4 py-2 text-muted no-underline">返回后台</a>
    </div>
    <p class="text-xs text-gray-500 mt-2">提示：为避免布局溢出，建议为图片添加样式 <code>style=\"max-width:100%\"</code>，或使用合理尺寸图片。</p>
  </form>
</section>
<script>
  (function(){
    const base = "<?php echo $basePath; ?>";
    const defaultRedirect = base + "/admin/index.php";
    document.addEventListener('submit', async (e) => {
      const form = e.target;
      if (!(form instanceof HTMLFormElement)) return;
      const action = form.getAttribute('action') || '';
      if (!/\/admin\/save_notice\.php$/.test(action)) return;
      e.preventDefault();
      const fd = new FormData(form);
      let redirect = defaultRedirect;
      try {
        const res = await fetch(action, { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        const ct = res.headers.get('content-type') || '';
        if (ct.includes('application/json')) {
          const j = await res.json().catch(() => null);
          if (j && j.redirect) redirect = j.redirect;
        }
      } catch(_) {}
      if (window.__navTo) { window.__navTo(redirect); } else { window.location.href = redirect; }
    }, true);
  })();
</script>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>