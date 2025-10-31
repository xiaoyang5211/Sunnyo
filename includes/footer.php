</main>
  <footer class="py-8 text-center bg-primary-10">
    <div class="max-w-screen-xl mx-auto px-4">
      <p class="text-xl font-fumofumo m-0 opacity-80 text-primary">Fumomo - 简约而不简单</p>
      <p class="text-sm mt-2 text-muted">© <?php echo date('Y'); ?> Fumomo. 保留所有权利。</p>
      <p class="text-sm mt-1 text-muted">
        Powered by 
        <a href="https://www.php.net/" target="" rel="noopener" class="hover:underline transition-all duration-300 text-primary">PHP</a> &
        <a href="https://tailwindcss.com/" target="" rel="noopener" class="hover:underline transition-all duration-300 text-primary">TailwindCSS</a>
      </p>
    </div>
  </footer>

  <?php
    // 非后台页面且为桌面端，显示底部滚动提示（固定位置样式）
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $isAdminPage = preg_match('~(?:^|/)admin(?:/|$)~', $uri) === 1;
    if (!$isAdminPage):
      // 复用导航顺序，计算下一页提示
      $navList = $siteConfig['navigation'] ?? [];
      $preferredOrder = ['home','articles','projects','website','about'];
      $sequenceKeysFromNav = array_map(function($it){ return isset($it['key']) ? $it['key'] : ''; }, $navList);
      $sequenceKeysFromNav = array_values(array_filter($sequenceKeysFromNav));
      if (!empty($sequenceKeysFromNav)) {
        $sequenceKeys = array_values(array_intersect($preferredOrder, $sequenceKeysFromNav));
        $extras = array_values(array_diff($sequenceKeysFromNav, $preferredOrder));
        $sequenceKeys = array_merge($sequenceKeys, $extras);
      } else {
        $sequenceKeys = $preferredOrder;
      }
      $currentKey = $currentPage ?? 'home';
      $idx = array_search($currentKey, $sequenceKeys, true);
      if ($idx === false) { $idx = 0; }
      $nextKey = $sequenceKeys[($idx + 1) % count($sequenceKeys)];
      $nextName = '下一页';
      $nextHref = null;
      foreach ($navList as $it) {
        if (($it['key'] ?? '') === $nextKey) {
          $nextName = $it['name'] ?? $nextName;
          $nextHref = $it['href'] ?? null;
          break;
        }
      }
      // 默认映射（兜底），保持与导航一致
      if (!$nextHref) {
        $map = [
          'home' => '/index.php',
          'articles' => '/articles.php',
          'projects' => '/projects.php',
          'website' => '/website.php',
          'about' => '/about.php',
        ];
        $nextHref = $map[$nextKey] ?? '/index.php';
      }
      $nextLabelText = $nextName . '页';
  ?>
    <div class="hidden md:block fixed bottom-8 left-1/2 transform -translate-x-1/2 text-center opacity-70 hover:opacity-100 transition-opacity duration-300">
      <div class="animate-bounce">
        <i class="fas fa-mouse text-2xl text-primary mb-2 block"></i>
        <p class="text-sm text-muted">向下滚动进入<?php echo htmlspecialchars($nextLabelText); ?></p>
      </div>
    </div>
    <script>
      // 提供下一页地址给前端逻辑，优先使用服务端计算
      window.__NEXT_PAGE = '<?php echo $basePath . $nextHref; ?>';
    </script>
  <?php endif; ?>

  <script>window.__BASE_PATH = '<?php echo $basePath; ?>';</script>
  <script src="<?php echo $basePath; ?>/assets/js/main.js?v=<?php echo $assetVersionJs; ?>"></script>
  <!-- jQuery（保留） -->
  <script src="https://fastly.jsdelivr.net/npm/jquery@3.2/dist/jquery.min.js" crossorigin="anonymous"></script>

  <?php 
    // 统一后台保存后的跳转逻辑：拦截表单提交，使用 window.__navTo 切换页面
    $__isAdminPageUnified = preg_match('~(?:^|/)admin(?:/|$)~', parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/') === 1;
  ?>
  <?php if ($__isAdminPageUnified): ?>
  <script>
    (function(){
      try {
        var base = window.__BASE_PATH || '';
        function nav(url){ if (typeof window.__navTo === 'function') { window.__navTo(url); } else { window.location.href = url; } }
        document.addEventListener('submit', function(e){
          var form = e.target;
          if (!(form instanceof HTMLFormElement)) return;
          var action = form.getAttribute('action') || form.action || '';
          if (!/\/admin\//.test(action)) return;
          e.preventDefault();
          var fd = new FormData(form);
          fetch(action, { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function(res){
              var ct = res.headers.get('content-type') || '';
              if (ct.indexOf('application/json') >= 0) {
                return res.json().then(function(j){ return (j && j.redirect) ? j.redirect : (base + '/admin/index.php'); });
              }
              var dest = base + '/admin/index.php';
              if (/\/save_items\.php$/.test(action)) {
                var type = fd.get('type') || '';
                dest = base + '/admin/' + (type || 'index') + '.php?ok=1';
              } else if (/\/process_friend_request\.php$/.test(action)) {
                dest = base + '/admin/friends.php?ok=1';
              } else if (/\/save_notice\.php$/.test(action)) {
                dest = base + '/admin/index.php';
              } else if (/\/save_settings\.php$/.test(action)) {
                dest = base + '/admin/settings.php?updated=1';
              } else if (/\/save_about\.php$/.test(action)) {
                dest = base + '/admin/about.php?ok=1';
              } else if (/\/save_articles_settings\.php$/.test(action)) {
                dest = base + '/admin/articles.php?ok=1';
              } else if (/\/save_account\.php$/.test(action)) {
                dest = base + '/admin/account.php?ok=1';
              }
              return dest;
            })
            .then(function(url){ nav(url); })
            .catch(function(){ nav(base + '/admin/index.php'); });
        }, true);
      } catch(_) {}
    })();
  </script>
  <?php endif; ?>
  <?php 
    // 底部音乐播放器（仅前台页面注入；需要 jQuery）
    $__isAdminPageMusic = preg_match('~(?:^|/)admin(?:/|$)~', parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/') === 1; 
    $musicConf = $siteConfig['music']['myhk'] ?? ['enable' => 0, 'key' => '', 'mode' => 1];
    $musicEnabled = !empty($musicConf['enable']);
    $musicKey = preg_replace('/[^0-9]/', '', (string)($musicConf['key'] ?? ''));
    $musicMode = intval($musicConf['mode'] ?? 1);
  ?>
  <?php if ($musicEnabled && !$__isAdminPageMusic && !empty($musicKey)): ?>
    <script type="text/javascript" id="myhk" src="https://myhkw.cn/api/player/<?php echo htmlspecialchars($musicKey); ?>" key="<?php echo htmlspecialchars($musicKey); ?>" m="<?php echo $musicMode; ?>"></script>
  <?php endif; ?>
  
  <?php if (!empty($siteConfig['live2d']['enable']) && (($currentPage ?? '') === 'home')): ?>
  <!-- 看板娘（多CDN脚本与模型回退；仅首页且开关开启时加载） -->
  <script>
    (function(){
      var libUrls = [
        'https://fastly.jsdelivr.net/npm/live2d-widget@3.1.4/lib/L2Dwidget.min.js',
        'https://cdn.jsdelivr.net/npm/live2d-widget@3.1.4/lib/L2Dwidget.min.js',
        'https://unpkg.com/live2d-widget@3.1.4/lib/L2Dwidget.min.js'
      ];
      var models = [
        'https://fastly.jsdelivr.net/npm/live2d-widget-model-shizuku@1.0.5/assets/shizuku.model.json',
        'https://cdn.jsdelivr.net/npm/live2d-widget-model-shizuku@1.0.5/assets/shizuku.model.json',
        'https://unpkg.com/live2d-widget-model-shizuku@1.0.5/assets/shizuku.model.json'
      ];
      function loadScriptSequential(urls, done){
        var i = 0;
        (function tryNext(){
          if (i >= urls.length) return; // 放弃
          var s = document.createElement('script');
          s.src = urls[i];
          s.async = true;
          s.crossOrigin = 'anonymous';
          s.onload = function(){ try { done && done(); } catch(_){} };
          s.onerror = function(){ setTimeout(function(){ i++; tryNext(); }, 300); };
          document.head.appendChild(s);
        })();
      }
      function initWith(i){
        if (i >= models.length) return;
        if (!(window.L2Dwidget && typeof window.L2Dwidget.init === 'function')) return;
        try {
          window.L2Dwidget.init({
            model: { jsonPath: models[i], scale: 1 },
            display: { position: 'left', width: 200, height: 350, hOffset: 10, vOffset: 0 },
            mobile: { show: true, scale: 0.6 },
            react: { opacityDefault: 0.9, opacityOnHover: 1 }
          });
        } catch (e) {
          setTimeout(function(){ initWith(i+1); }, 300);
        }
      }
      function start(){ loadScriptSequential(libUrls, function(){ initWith(0); }); }
      if (document.readyState === 'complete') start();
      else window.addEventListener('load', start, { once: true });
    })();
  </script>
  <?php endif; ?>
</body>
</html>