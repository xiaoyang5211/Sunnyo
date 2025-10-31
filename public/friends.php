<?php
$currentPage = 'friends';
require_once __DIR__ . '/../includes/header.php';
?>

<section class="bg-white rounded-3xl shadow-lg p-12 w-full mx-auto px-4" style="box-shadow: 0 4px 24px rgba(139,90,140,0.08);">
  <h1 class="text-primary text-4xl mb-2 text-center font-fumofumo">友链</h1>
  <p class="text-muted text-xl text-center mb-8">欢迎加入我的友链交流</p>

  <?php $friends = readData('friends', []); ?>
  <?php if (empty($friends)): ?>
    <div class="text-center text-muted">暂无友链，前往 <a href="/admin/friends.php" class="text-primary no-underline hover:underline">后台友链管理</a> 添加。</div>
  <?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
      <?php foreach ($friends as $f): ?>
        <a href="<?php echo htmlspecialchars($f['url'] ?? '#'); ?>" target="_blank" rel="noopener noreferrer" class="flex items-center gap-4 bg-gradient-to-br from-pink-50 to-purple-50 rounded-2xl p-6 border border-purple-200 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-xl no-underline">
          <?php if (!empty($f['avatar'])): ?>
            <img src="<?php echo htmlspecialchars($f['avatar']); ?>" alt="avatar" class="w-14 h-14 rounded-full object-cover ring-2 ring-purple-200" />
          <?php else: ?>
            <div class="w-14 h-14 rounded-full bg-primary/10 flex items-center justify-center text-primary">👤</div>
          <?php endif; ?>
          <div class="flex-1 text-left">
            <h3 class="text-primary text-xl mb-1 font-fumofumo mt-1"><?php echo htmlspecialchars($f['title'] ?? '未命名'); ?></h3>
            <p class="text-muted text-base leading-relaxed m-0"><?php echo htmlspecialchars($f['description'] ?? ''); ?></p>
            <span class="mt-2 inline-flex items-center gap-1 text-primary font-medium">
              点击前往 <i class="fa-solid fa-arrow-right"></i>
            </span>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <div class="text-center">
    <button id="openFriendModal" class="bg-primary text-white rounded px-5 py-2 font-medium hover:opacity-90 transform hover:scale-105 transition-all">申请友链</button>
  </div>
</section>

<!-- Modal -->
<div id="friendModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm hidden items-center justify-center z-50">
  <div class="bg-white rounded-2xl w-full max-w-xl mx-4 p-6 shadow-lg modal-panel">
    <h3 class="text-primary text-2xl mb-4 font-fumofumo">申请友链</h3>
    <?php $req = $_GET['req'] ?? ''; if ($req === 'ok'): ?>
      <div class="mb-4 p-3 rounded bg-green-50 text-green-700 border border-green-200">提交成功，待管理员审核通过后显示。</div>
    <?php elseif ($req === 'invalid' || $req === 'error'): ?>
      <div class="mb-4 p-3 rounded bg-red-50 text-red-700 border border-red-200">提交失败，请检查必填项与 URL/邮箱 格式。</div>
    <?php endif; ?>
    <form method="post" action="<?php echo $basePath; ?>/friend_request.php" class="space-y-3">
      <?php echo csrfField(); ?>
      <div>
        <label class="block text-sm text-muted mb-1">网站名称（必填）</label>
        <input type="text" name="title" class="w-full border border-gray-300 rounded px-3 py-2" required maxlength="100" />
      </div>
      <div>
        <label class="block text-sm text-muted mb-1">网站链接（必填，http/https）</label>
        <input type="url" name="url" class="w-full border border-gray-300 rounded px-3 py-2" required />
      </div>
      <div>
        <label class="block text-sm text-muted mb-1">网站描述（选填）</label>
        <input type="text" name="description" class="w-full border border-gray-300 rounded px-3 py-2" maxlength="300" />
      </div>
      <div>
        <label class="block text-sm text-muted mb-1">联系邮箱（必填）</label>
        <input type="email" name="contactEmail" class="w-full border border-gray-300 rounded px-3 py-2" required maxlength="100" />
      </div>
      <div>
        <label class="block text-sm text-muted mb-1">头像链接（选填，http/https）</label>
        <input type="url" name="avatar" class="w-full border border-gray-300 rounded px-3 py-2" />
      </div>
      <div class="flex justify-between pt-2">
        <button type="button" id="cancelFriendModal" class="bg-white border border-gray-300 rounded px-4 py-2 text-muted">取消</button>
        <button type="submit" class="bg-primary text-white rounded px-5 py-2 font-medium hover:opacity-90 transform hover:scale-105 transition-all">提交申请</button>
      </div>
    </form>
  </div>
</div>

<script>
  (function(){
    const modal = document.getElementById('friendModal');
    const openBtn = document.getElementById('openFriendModal');
    const cancelBtn = document.getElementById('cancelFriendModal');
    const panel = modal ? modal.querySelector('.modal-panel') : null;
    const toggle = (show) => {
      if (!modal) return;
      if (show) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        try { modal.classList.add('modal-overlay-in'); setTimeout(() => { modal.classList.remove('modal-overlay-in'); }, 280); } catch(_){}
        try { if (panel) { panel.classList.add('modal-panel-in'); setTimeout(() => { panel.classList.remove('modal-panel-in'); }, 340); } } catch(_){}
      } else {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        try { modal.classList.remove('modal-overlay-in'); } catch(_){}
        try { if (panel) panel.classList.remove('modal-panel-in'); } catch(_){}
      }
    };
    openBtn && openBtn.addEventListener('click', () => toggle(true));
    cancelBtn && cancelBtn.addEventListener('click', () => toggle(false));
    modal && modal.addEventListener('click', (e) => { if (e.target === modal) toggle(false); });
  })();
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>