<?php
require_once __DIR__ . '/../includes/config.php';
requireLogin();
$currentPage = 'home';
require_once __DIR__ . '/../includes/header.php';

// 读取当前设置（来自 $siteConfig）
$site = $siteConfig['site'];
$theme = $siteConfig['theme'];
$umami = $siteConfig['umami'];
$bgDefault = 'linear-gradient(135deg, #f7e9ff 0%, #e9d5ff 50%, #d8b4fe 100%)';
$bg = $theme['backgroundGradient'] ?? $bgDefault;
$bgType = $theme['backgroundType'] ?? 'gradient';
$bgImageUrl = $theme['backgroundImageUrl'] ?? '';
$accentColor = $theme['accentColor'] ?? '#ff7aa2';
$accentImageUrl = $theme['accentImageUrl'] ?? '';
$live2d = $siteConfig['live2d'] ?? ['enable' => 1];
$musicMyhk = $siteConfig['music']['myhk'] ?? ['enable' => 0, 'key' => '', 'mode' => 1];
?>
<section class="bg-white rounded-3xl shadow-lg p-12 max-w-3xl w-full mx-auto" style="box-shadow: 0 4px 24px rgba(139,90,140,0.08);">
  <h1 class="text-primary text-3xl mb-2 text-center font-fumofumo">站点设置</h1>
  <p class="text-muted text-center mb-8">无需改源代码，在此修改基础信息与背景渐变</p>
  <form method="post" action="<?php echo $basePath; ?>/admin/save_settings.php" class="space-y-6">
    <?php echo csrfField(); ?>
    <div>
      <label class="block text-sm text-muted mb-1">网站标题</label>
      <input type="text" name="site_title" value="<?php echo htmlspecialchars($site['title']); ?>" class="w-full border border-gray-300 rounded px-3 py-2" required />
    </div>
    <div>
      <label class="block text-sm text-muted mb-1">网站副标题</label>
      <input type="text" name="site_subtitle" value="<?php echo htmlspecialchars($site['subtitle']); ?>" class="w-full border border-gray-300 rounded px-3 py-2" />
    </div>
    <div>
      <label class="block text-sm text-muted mb-1">网站描述</label>
      <input type="text" name="site_description" value="<?php echo htmlspecialchars($site['description']); ?>" class="w-full border border-gray-300 rounded px-3 py-2" />
    </div>

    <!-- 背景渐变配置 -->
    <div>
      <label class="block text-sm text-muted mb-1">背景渐变</label>
      <?php
        $matches = [];
        preg_match_all('/#([0-9a-fA-F]{3,8})/', $bg, $matches);
        $c1 = $matches[0][0] ?? '#f7e9ff';
        $c2 = $matches[0][1] ?? '#e9d5ff';
        $c3 = $matches[0][2] ?? '#d8b4fe';
      ?>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <span class="text-sm text-muted">起始色</span>
          <input type="color" name="bg_from" value="<?php echo $c1; ?>" class="w-full h-10 border border-gray-300 rounded" />
        </div>
        <div>
          <span class="text-sm text-muted">中间色</span>
          <input type="color" name="bg_via" value="<?php echo $c2; ?>" class="w-full h-10 border border-gray-300 rounded" />
        </div>
        <div>
          <span class="text-sm text-muted">结束色</span>
          <input type="color" name="bg_to" value="<?php echo $c3; ?>" class="w-full h-10 border border-gray-300 rounded" />
        </div>
      </div>
      <p class="text-xs text-muted mt-2">预览：当前为 <span class="inline-block align-middle px-2 py-1 rounded" style="background: <?php echo htmlspecialchars($bg); ?>;">&nbsp;&nbsp;&nbsp;&nbsp;</span></p>
    </div>

    <!-- 背景类型与图片（API）设置 -->
    <div>
      <label class="block text-sm text-muted mb-1">背景类型</label>
      <select name="bg_type" class="w-full border border-gray-300 rounded px-3 py-2">
        <option value="gradient" <?php echo ($bgType === 'gradient') ? 'selected' : ''; ?>>渐变</option>
        <option value="white" <?php echo ($bgType === 'white') ? 'selected' : ''; ?>>纯白（原始背景）</option>
        <option value="image" <?php echo ($bgType === 'image') ? 'selected' : ''; ?>>图片（API）</option>
      </select>
      <div class="mt-3">
        <label class="block text-sm text-muted mb-1">背景图片 API 地址</label>
        <input type="url" name="bg_image_url" value="<?php echo htmlspecialchars($bgImageUrl); ?>" placeholder="https://t.alcy.cc/ycy" class="w-full border border-gray-300 rounded px-3 py-2" />
        <p class="text-xs text-gray-500 mt-1">支持直接填入图片链接或返回图片的 API（例如： https://t.alcy.cc/ycy ）</p>
      </div>
    </div>

    <!-- 按钮强调配置 -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm text-muted mb-1">按钮强调色</label>
        <input type="color" name="accent_color" value="<?php echo htmlspecialchars($accentColor); ?>" class="w-full h-10 border border-gray-300 rounded" />
        <p class="text-xs text-muted mt-1">用于导航按钮与页面 CTA 背景</p>
      </div>
      <div>
        <label class="block text-sm text-muted mb-1">按钮背景图片（可选）</label>
        <input type="url" name="accent_image_url" value="<?php echo htmlspecialchars($accentImageUrl); ?>" placeholder="https://example.com/bg.png" class="w-full border border-gray-300 rounded px-3 py-2" />
        <p class="text-xs text-gray-500 mt-1">留空则只使用强调色；填写图片链接将作为按钮的装饰背景</p>
      </div>
    </div>

    <!-- 仅保留底部一组按钮，移除重复的“保存设置/返回后台” -->
    <div>
      <label class="block text-sm text-muted mb-1">Umami 统计：启用</label>
      <select name="umami_enable" class="w-full border border-gray-300 rounded px-3 py-2">
        <option value="1" <?php echo !empty($umami['enable']) ? 'selected' : ''; ?>>是</option>
        <option value="0" <?php echo empty($umami['enable']) ? 'selected' : ''; ?>>否</option>
      </select>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm text-muted mb-1">Umami Script 地址</label>
        <input type="text" name="umami_scriptSrc" value="<?php echo htmlspecialchars($umami['scriptSrc'] ?? ''); ?>" class="w-full border border-gray-300 rounded px-3 py-2" />
      </div>
      <div>
        <label class="block text-sm text-muted mb-1">Umami 网站ID</label>
        <input type="text" name="umami_websiteId" value="<?php echo htmlspecialchars($umami['websiteId'] ?? ''); ?>" class="w-full border border-gray-300 rounded px-3 py-2" />
      </div>
    </div>

    <!-- 看板娘（Live2D）开关 -->
    <div>
      <label class="block text-sm text-muted mb-1">看板娘：启用（仅主页显示）</label>
      <select name="live2d_enable" class="w-full border border-gray-300 rounded px-3 py-2">
        <option value="1" <?php echo !empty($live2d['enable']) ? 'selected' : ''; ?>>是</option>
        <option value="0" <?php echo empty($live2d['enable']) ? 'selected' : ''; ?>>否</option>
      </select>
      <p class="text-xs text-gray-500 mt-1">关闭后首页不显示；开启后首页显示。</p>
    </div>

    <!-- 底部音乐播放器（明月浩空 myhkw） -->
    <div class="mt-4">
      <label class="block text-sm text-muted mb-1">底部音乐播放器：启用</label>
      <select name="music_enable" class="w-full border border-gray-300 rounded px-3 py-2">
        <option value="1" <?php echo !empty($musicMyhk['enable']) ? 'selected' : ''; ?>>是</option>
        <option value="0" <?php echo empty($musicMyhk['enable']) ? 'selected' : ''; ?>>否</option>
      </select>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
        <div>
          <label class="block text-sm text-muted mb-1">播放器 Key（数字）</label>
          <input type="text" name="music_key" value="<?php echo htmlspecialchars($musicMyhk['key']); ?>" class="w-full border border-gray-300 rounded px-3 py-2" placeholder="例如：175148219537" />
          <p class="text-xs text-gray-500 mt-1">来自 myhkw 后台生成的播放器数字 Key。</p>
        </div>
        <div>
          <label class="block text-sm text-muted mb-1">模式 m（默认 1）</label>
          <input type="number" name="music_mode" value="<?php echo intval($musicMyhk['mode'] ?? 1); ?>" class="w-full border border-gray-300 rounded px-3 py-2" min="0" step="1" />
          <p class="text-xs text-gray-500 mt-1">对应脚本属性 m 值（如不确定保持 1）。</p>
        </div>
      </div>
      <p class="text-xs text-gray-500 mt-2">将注入：<code>&lt;script id="myhk" src="https://myhkw.cn/api/player/{Key}" key="{Key}" m="{m}"&gt;&lt;/script&gt;</code></p>
    </div>

    <div class="flex gap-3 mt-6">
      <button type="submit" class="bg-primary text-white rounded px-5 py-2 font-medium hover:opacity-90 transform hover:scale-105 transition-all">保存设置</button>
      <a href="<?php echo $basePath; ?>/admin/" class="bg-white border border-gray-200 rounded px-4 py-2 text-muted hover:text-primary no-underline">返回后台</a>
    </div>
  </form>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>