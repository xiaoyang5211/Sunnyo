// 移动端菜单开关与外部点击处理
(function() {
  const toggle = document.getElementById('nav-toggle');
  const menu = document.getElementById('nav-menu');
  const overlay = document.getElementById('nav-overlay');

  if (!toggle || !menu || !overlay) return;

  const closeMenu = () => {
    menu.classList.add('hidden');
    overlay.classList.add('hidden');
    // 同步 hidden 属性，避免与模板默认 hidden 冲突
    if (!menu.hasAttribute('hidden')) menu.setAttribute('hidden', '');
    if (!overlay.hasAttribute('hidden')) overlay.setAttribute('hidden', '');
    toggle.classList.remove('active');
    toggle.setAttribute('aria-expanded', 'false');
    document.documentElement.classList.remove('no-scroll');
    document.body.classList.remove('no-scroll');
  };

  const openMenu = () => {
    menu.classList.remove('hidden');
    overlay.classList.remove('hidden');
    // 同步移除 hidden 属性，确保元素可见
    if (menu.hasAttribute('hidden')) menu.removeAttribute('hidden');
    if (overlay.hasAttribute('hidden')) overlay.removeAttribute('hidden');
    toggle.classList.add('active');
    toggle.setAttribute('aria-expanded', 'true');
    document.documentElement.classList.add('no-scroll');
    document.body.classList.add('no-scroll');
  };

  const isOpen = () => !(menu.classList.contains('hidden') || menu.hasAttribute('hidden'));
  const handleToggle = (e) => {
    e.stopPropagation();
    // 在触摸场景下防止默认滚动/双击缩放
    if (e.type === 'touchstart') { /* 不阻止点击，但避免默认滚动 */ }
    if (isOpen()) { closeMenu(); } else { openMenu(); }
  };

  // 统一绑定多种事件，提升手机端点击可靠性
  toggle.addEventListener('click', handleToggle);
  toggle.addEventListener('touchstart', handleToggle, { passive: true });
  toggle.addEventListener('pointerdown', handleToggle);

  overlay.addEventListener('click', closeMenu);
  // 移动端防止背景滚动穿透（iOS/Android），并支持触摸关闭
  overlay.addEventListener('touchmove', (e) => { e.preventDefault(); }, { passive: false });
  overlay.addEventListener('touchstart', (e) => { e.preventDefault(); closeMenu(); }, { passive: false });

  document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeMenu(); });
  document.addEventListener('click', (e) => {
    const target = e.target;
    if (!(target.closest && (target.closest('#nav-toggle') || target.closest('#nav-menu')))) {
      closeMenu();
    }
  });
})();

// 通用页面进场效果：为齐 Nuxt 的 page 过渡（enter/leave）
(function() {
  const applyEnterTransition = () => {
    const main = document.querySelector('main');
    if (!main) return;
    // 初始化 enter 状态
    main.classList.add('page-enter-active', 'page-enter-from');
    requestAnimationFrame(() => {
      // 下一帧切换到目标状态
      main.classList.remove('page-enter-from');
      main.classList.add('page-enter-to');
      // 动画结束后清理 active/to（留在正常状态）
      setTimeout(() => {
        main.classList.remove('page-enter-active', 'page-enter-to');
      }, 420);
    });
  };

  const animateSections = () => {
    const sections = Array.from(document.querySelectorAll('main > section'));
    sections.forEach((sec, i) => {
      sec.style.opacity = '0';
      const delay = 80 + i * 80;
      setTimeout(() => { sec.classList.add('card-fade-up'); }, delay);
    });
  };

  const setActiveByPath = () => {
    try {
      const base = (window.__BASE_PATH || '');
      const current = new URL(window.location.href).pathname.replace(/\/+$/, '') || '/';
      const matchHref = (href) => {
        try {
          const p = new URL(href, window.location.origin).pathname.replace(/\/+$/, '') || '/';
          // 兼容首页两种写法
          const home1 = (base + '/').replace(/\/+$/, '') || '/';
          const home2 = (base + '/index.php').replace(/\/+$/, '') || '/index.php';
          if ((current === home1 || current === home2) && (p === home1 || p === home2)) return true;
          // 后台路径前缀：当前位于任何 /admin/* 时，高亮“后台”链接（指向 /admin/）
          const adminPrefix = (base + '/admin').replace(/\/+$/, '') || '/admin';
          if (current.startsWith(adminPrefix) && (p === adminPrefix)) return true;
          return p === current;
        } catch(_) { return false; }
      };
      const links = Array.from(document.querySelectorAll('header nav a, #nav-menu a')); 
      links.forEach(a => {
        const href = a.getAttribute('href') || '';
        if (matchHref(href)) a.classList.add('active'); else a.classList.remove('active');
      });
    } catch(_) {}
  };

  const interceptInternalLinks = () => {
    // 若已存在 PJAX 导航，则不再注册旧的拦截器，避免重复处理
    if (typeof window.__navTo === 'function') return;
    const base = (window.__BASE_PATH || '');
    const sameOrigin = (url) => {
      try { const u = new URL(url, window.location.origin); return u.origin === window.location.origin; } catch { return false; }
    };
    const isInternal = (href) => sameOrigin(href) && (!/^https?:\/\//i.test(href) || href.startsWith(window.location.origin));
    // const isAdminPath = (href) => /\/admin\//.test(href);
    document.addEventListener('click', (e) => {
      const a = e.target && (e.target.closest ? e.target.closest('a') : null);
      if (!a) return;
      const href = a.getAttribute('href');
      const target = a.getAttribute('target');
      if (!href || target === '_blank') return;
      if (!isInternal(href)) return; // 允许后台也使用 PJAX 过场
      e.preventDefault();
      if (typeof window.__navTo === 'function') { window.__navTo(href); } else { window.location.href = href; }
    }, true);
  };

  window.addEventListener('load', () => {
    applyEnterTransition();

    // 恢复默认点击跳转逻辑：不再拦截站内链接
    setActiveByPath();
    animateSections();
  });
})();

// 首页动画（同步前台版本）：让首页标题、副文与卡片淡入显示
(function() {
  const logo = document.getElementById('home-logo');
  const subtitle = document.getElementById('home-subtitle');
  const content = document.getElementById('home-content');

  window.addEventListener('load', () => {
    // 渐进淡入，避免初始样式保持为透明
    setTimeout(() => { if (logo) logo.classList.add('bounce-in'); }, 200);
    setTimeout(() => { if (subtitle) subtitle.classList.add('fade-in'); }, 500);
    setTimeout(() => { if (content) content.classList.add('fade-in-delayed'); }, 800);
  });
})();

// 桌面端：到达底部后再次下滑才进入“下一个页面”，并显示滚动进度百分比（后台禁用；支持 __NEXT_PAGE 优先）
(function() {
  const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) || window.innerWidth <= 768;
  const base = (window.__BASE_PATH || '');
  const path = window.location.pathname || '';
  const isAdmin = /\/admin\//.test(path) || path.startsWith(base + '/admin');
  if (isAdmin) return;
  // 防连跳：在滚动模块里使用全局导航锁
  window.__SCROLL_NAVIGATING = window.__SCROLL_NAVIGATING || false;

  const normalize = (p) => {
    if (!p) return '/';
    try { p = new URL(p, window.location.origin).pathname; } catch(_) {}
    p = p.replace(/\/+$/, '');
    return p === '' ? '/' : p;
  };

  // 新增：仅在可下滑跳转的页面启用该模块（排除后台及其他非导航序列页面）
  const isScrollNavigablePath = (p) => {
    const allowed = [
      base + '/',
      base + '/articles.php',
      base + '/projects.php',
      base + '/friends.php',
      base + '/website.php',
      base + '/about.php'
    ].map(normalize);
    const n = normalize(p);
    return allowed.includes(n);
  };
  if (!isScrollNavigablePath(path)) return;

  // 依据当前页面的导航结构（header nav a[href]）构建顺序，并去重过滤后台链接
  const computePages = () => {
    // 使用固定顺序： 首页 → 文章 → 项目 → 网站 → 关于 → 首页（循环）
    const pagesFixed = [
      base + '/',
      base + '/articles.php',
      base + '/projects.php',
      base + '/friends.php',
      base + '/website.php',
      base + '/about.php'
    ].map(normalize);
    const seen = new Set();
    return pagesFixed.filter(p => { const n = normalize(p); if (seen.has(n)) return false; seen.add(n); return true; });
  };

  const computeNext = () => {
    const pages = computePages();
    const current = normalize(window.location.pathname || '');
    let idx = pages.indexOf(current);
    // 兼容首页两种写法（/ 与 /index.php）
    if (idx === -1 && (current === normalize(base + '/') || current === normalize(base + '/index.php'))) {
      idx = pages.indexOf(normalize(base + '/'));
    }
    // 固定顺序：严格按 pages 的下一个，不再使用服务端 __NEXT_PAGE 覆盖
    let next = normalize(pages[(idx + 1 + pages.length) % pages.length]);
    // 防止跳转到同一页面（可能由于不同写法导致等价路径）
    if (next === current && pages.length > 1) {
      next = normalize(pages[(idx + 2) % pages.length]);
    }
    return next;
  };

  // 进度提示 UI（桌面端底部居中）
  const hint = document.createElement('div');
  hint.id = 'scroll-progress-hint';
  hint.className = 'hidden md:block fixed bottom-8 left-1/2 transform -translate-x-1/2 text-center opacity-80 transition-opacity duration-300 z-40';
  hint.innerHTML = `
    <div class="flex items-center gap-2 bg-white bg-opacity-90 backdrop-blur-md border border-purple-100 rounded-xl px-4 py-2 shadow-sm select-none">
      <span id="scroll-progress-label" class="text-sm text-muted">滚动到底部</span>
      <span id="scroll-progress-value" class="text-sm font-medium text-primary">0%</span>
    </div>
  `;
  document.body.appendChild(hint);

  const labelEl = document.getElementById('scroll-progress-label');
  const valueEl = document.getElementById('scroll-progress-value');

  // 观察哨：基于 IntersectionObserver 判定是否到达底部，提升精度与兼容性
  let sentinelVisible = false;
  let sentinel = null;
  let sentinelObserver = null;
  const ensureSentinel = () => {
    try {
      const main = document.querySelector('main');
      if (!main) return;
      // 若不存在或被 PJAX 替换后丢失，则重建并挂载到 main 尾部
      if (!sentinel || !document.body.contains(sentinel)) {
        sentinel = document.createElement('div');
        sentinel.id = 'page-bottom-sentinel';
        sentinel.style.cssText = 'width:100%;height:1px;';
        main.appendChild(sentinel);
      }
      // 重建观察器以避免旧节点失效
      if (sentinelObserver) { try { sentinelObserver.disconnect(); } catch(_) {} }
      sentinelObserver = new IntersectionObserver((entries) => {
        sentinelVisible = entries.some(e => e.isIntersecting);
        // 到达底部时刷新提示状态
        try { updateProgress(false); } catch(_) {}
      }, { root: null, threshold: [0, 0.01, 0.25, 0.5, 0.75, 1] });
      sentinelObserver.observe(sentinel);
    } catch(_) {}
  };
  ensureSentinel();
  // 暴露给 PJAX 完成后重新初始化滚动提示与观察哨
  window.__ensureScrollNavReady = () => { try { ensureSentinel(); updateProgress(false); } catch(_) {} };

  // 根据当前页面动态生成提示文案，显示具体将进入的页面名称
  const getScrollLabel = (ready) => {
    const base = (window.__BASE_PATH || '');
    const nameByNext = () => {
      const next = computeNext();
      const n = normalize(next);
      const home1 = normalize(base + '/');
      const home2 = normalize(base + '/index.php');
      if (n === home1 || n === home2) return '首页';
      if (n === normalize(base + '/articles.php')) return '文章页';
      if (n === normalize(base + '/projects.php')) return '项目页';
      if (n === normalize(base + '/friends.php')) return '友链页';
      if (n === normalize(base + '/website.php')) return '网站页';
      if (n === normalize(base + '/about.php')) return '关于页';
      return '下一页';
    };
    const nextName = nameByNext();
    return ready ? `已到达底部，再次下滑进入${nextName}` : `向下滚动进入${nextName}`;
  };

  const atBottom = () => {
    return !!sentinelVisible;
  };

  let bottomReady = false;
  let requireSecondScroll = false;
  let lastAfterReadyTs = 0;
  let lastReadyTs = 0;
  let progressPercent = 0; // 当前滚动进度百分比

  const updateProgress = (fromScroll) => {
    const doc = document.documentElement;
    const scrollTop = window.scrollY || doc.scrollTop || 0;
    const clientHeight = doc.clientHeight || window.innerHeight;
    const scrollHeight = doc.scrollHeight || document.body.scrollHeight;
    const percent = Math.min(100, Math.round(((scrollTop + clientHeight) / Math.max(1, scrollHeight)) * 100));

    progressPercent = percent;
    valueEl.textContent = percent + '%';

    if (atBottom()) {
      hint.classList.remove('hidden');
      // 只有到达 100% 才视为“准备就绪”
      if (percent === 100) {
        if (!bottomReady) { bottomReady = true; lastReadyTs = performance.now(); }
      } else {
        if (fromScroll) { bottomReady = false; }
      }
    } else {
      hint.classList.add('hidden');
      if (fromScroll) bottomReady = false;
    }

    labelEl.textContent = getScrollLabel(bottomReady);
  };

  updateProgress(false);
  window.addEventListener('scroll', () => updateProgress(true), { passive: true });

  const navigateInternal = (href) => {
    if (typeof window.__navTo === 'function') {
      window.__navTo(href);
    } else {
      window.location.href = href;
    }
  };

  const handler = (e) => {
    if (e.deltaY > 0) {
      // 仅在下滑且进度达到 100% 时触发跳转
      if (atBottom() && bottomReady && progressPercent === 100) {
        if (performance.now() - lastReadyTs < 200) return;
        if (window.__SCROLL_NAVIGATING) return;
        window.__SCROLL_NAVIGATING = true;
        e.preventDefault();
        const next = computeNext();
        setTimeout(() => { navigateInternal(next); window.__SCROLL_NAVIGATING = false; }, 600);
      } else if (atBottom() && !bottomReady) {
        // 仅当到达 100% 时才设置为准备就绪
        if (progressPercent === 100) { bottomReady = true; lastReadyTs = performance.now(); labelEl.textContent = getScrollLabel(bottomReady); }
      } else {
        bottomReady = false; labelEl.textContent = getScrollLabel(bottomReady);
      }
    } else if (e.deltaY < 0) {
      // 上滑则重置，需要再次达到 100% 后再下滑才可跳转
      bottomReady = false; lastReadyTs = 0; labelEl.textContent = getScrollLabel(bottomReady);
    }
  };
  window.addEventListener('wheel', handler, { passive: false });

  // 手机端触摸
  const overlayOrMenuOpen = () => {
    const ov = document.getElementById('nav-overlay');
    const mn = document.getElementById('nav-menu');
    const ovOpen = ov && !ov.classList.contains('hidden') && !ov.hasAttribute('hidden');
    const mnOpen = mn && !mn.classList.contains('hidden') && !mn.hasAttribute('hidden');
    return !!(ovOpen || mnOpen);
  };
  let touchStartY = 0, touchLastY = 0, touchActive = false;
  window.addEventListener('touchstart', (e) => {
    if (!isMobile || overlayOrMenuOpen()) return;
    const t = e.touches && e.touches[0]; if (!t) return;
    touchActive = true; touchStartY = t.clientY; touchLastY = t.clientY;
  }, { passive: true });
  window.addEventListener('touchmove', (e) => { if (!isMobile || !touchActive) return; const t = e.touches && e.touches[0]; if (!t) return; touchLastY = t.clientY; }, { passive: true });
  window.addEventListener('touchend', () => {
    if (!isMobile || !touchActive) return; touchActive = false; const deltaUp = touchStartY - touchLastY;
    if (deltaUp > 20) {
      // 仅在上划手势（向上滚动）且到达 100% 时触发跳转
      if (atBottom() && bottomReady && progressPercent === 100) {
        if (performance.now() - lastReadyTs < 200) return;
        if (window.__SCROLL_NAVIGATING) return;
        window.__SCROLL_NAVIGATING = true;
        const next = computeNext();
        navigateInternal(next);
        setTimeout(() => { window.__SCROLL_NAVIGATING = false; }, 600);
      } else if (atBottom() && !bottomReady) {
        if (progressPercent === 100) { bottomReady = true; lastReadyTs = performance.now(); labelEl.textContent = getScrollLabel(bottomReady); }
      } else {
        bottomReady = false; labelEl.textContent = getScrollLabel(bottomReady);
      }
    } else {
      bottomReady = false; lastReadyTs = 0; labelEl.textContent = getScrollLabel(bottomReady);
    }
  }, { passive: true });

  // 桌面端不再自动第二次到底部触发跳转，统一为“提示后再次下滑触发”
})();

// 自定义光标（跟随效果简化版）
(function() {
  // 仅在具有细指针且支持 hover 的设备启用，禁用手机/触摸设备
  const hasFinePointer = window.matchMedia && window.matchMedia('(hover: hover) and (pointer: fine)').matches;
  const isTouch = ('ontouchstart' in window) || (navigator.maxTouchPoints > 0);
  // 如果页面存在 Live2D 看板娘组件，则避免启用自定义光标以防事件冲突
  const hasLive2D = !!(window.L2Dwidget) || !!document.querySelector('canvas[id^="live2d"]') || !!document.querySelector('[class*="live2d"]');
  if (!hasFinePointer || isTouch || hasLive2D) return;

  const cursor = document.createElement('div');
  const follower = document.createElement('div');
  cursor.className = 'custom-cursor';
  follower.className = 'custom-cursor-follower';

  document.body.appendChild(cursor);
  document.body.appendChild(follower);

  let mouseX = 0, mouseY = 0, fx = 0, fy = 0;

  document.body.classList.add('custom-cursor-active');

  document.addEventListener('mousemove', (e) => {
    mouseX = e.clientX; mouseY = e.clientY;
    try {
      cursor.style.left = mouseX + 'px';
      cursor.style.top = mouseY + 'px';
    } catch (_) {}
  }, { passive: true });

  const animate = () => {
    fx += (mouseX - fx) * 0.12;
    fy += (mouseY - fy) * 0.12;
    try {
      follower.style.left = fx + 'px';
      follower.style.top = fy + 'px';
    } catch (_) {}
    requestAnimationFrame(animate);
  };
  animate();

  const hoverTargets = document.querySelectorAll('a, button, input, textarea, select, [role="button"], [tabindex]:not([tabindex="-1"])');
  hoverTargets.forEach(el => {
    el.addEventListener('mouseenter', () => { cursor.classList.add('hover'); follower.classList.add('hover'); });
    el.addEventListener('mouseleave', () => { cursor.classList.remove('hover'); follower.classList.remove('hover'); });
  });
})();

// 无刷新导航（PJAX）：保持看板娘等全局组件不重载
(function() {
  const sameOrigin = (url) => {
    try { const u = new URL(url, window.location.origin); return u.origin === window.location.origin; } catch { return false; }
  };
  const isInternal = (href) => sameOrigin(href) && (!/^https?:\/\//i.test(href) || href.startsWith(window.location.origin));
  const isAdminPath = (href) => /\/admin\//.test(href);

  const applyEnterTransition = () => {
    const main = document.querySelector('main');
    if (!main) return;
    main.classList.add('page-enter-active', 'page-enter-from');
    requestAnimationFrame(() => {
      main.classList.remove('page-enter-from');
      main.classList.add('page-enter-to');
      setTimeout(() => { main.classList.remove('page-enter-active', 'page-enter-to'); }, 420);
    });
  };

  const animateSections = () => {
    const sections = Array.from(document.querySelectorAll('main > section'));
    sections.forEach((sec, i) => {
      sec.style.opacity = '0';
      const delay = 80 + i * 80;
      setTimeout(() => { sec.classList.add('card-fade-up'); }, delay);
    });
  };

  async function navTo(href, opts) {
    const main = document.querySelector('main');
    if (!main) { window.location.href = href; return; }

    // 离场动画
    main.classList.add('page-leave-active', 'page-leave-from');
    await new Promise(resolve => requestAnimationFrame(() => { main.classList.remove('page-leave-from'); main.classList.add('page-leave-to'); setTimeout(resolve, 220); }));

    try {
      const res = await fetch(href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      if (!res.ok) throw new Error('HTTP ' + res.status);
      const html = await res.text();
      const doc = new DOMParser().parseFromString(html, 'text/html');
      const newMain = doc.querySelector('main');
      const newTitle = doc.querySelector('title');
      if (!newMain) throw new Error('No <main> in response');

      // 替换内容
      main.innerHTML = newMain.innerHTML;
      // 更新标题
      if (newTitle) document.title = newTitle.textContent || document.title;
      // 更新地址（除非是popstate）
      if (!(opts && opts.pop)) history.pushState({ href }, '', href);
      // 回到顶部、入场动画
      window.scrollTo({ top: 0, behavior: 'instant' });
      applyEnterTransition();
      animateSections();
      // 更新导航高亮
      try { const fn = (typeof setActiveByPath === 'function') ? setActiveByPath : null; } catch(_) {}
      // 直接调用同作用域的函数
      (function(){
        try {
          const base = (window.__BASE_PATH || '');
          const current = new URL(window.location.href).pathname.replace(/\/+$/, '') || '/';
          const matchHref = (href) => {
            try {
              const p = new URL(href, window.location.origin).pathname.replace(/\/+$/, '') || '/';
              const home1 = (base + '/').replace(/\/+$/, '') || '/';
              const home2 = (base + '/index.php').replace(/\/+$/, '') || '/index.php';
              if ((current === home1 || current === home2) && (p === home1 || p === home2)) return true;
              // 后台路径前缀匹配：在任意 /admin/* 页面，高亮“后台”链接（指向 /admin/）
              const adminPrefix = (base + '/admin').replace(/\/+$/, '') || '/admin';
              if (current.startsWith(adminPrefix) && (p === adminPrefix)) return true;
              return p === current;
            } catch(_) { return false; }
          };
          const links = Array.from(document.querySelectorAll('header nav a, #nav-menu a'));
          links.forEach(a => { const href = a.getAttribute('href') || ''; if (matchHref(href)) a.classList.add('active'); else a.classList.remove('active'); });
        } catch(_) {}
      })();
      // 清理离场状态
      main.classList.remove('page-leave-active', 'page-leave-to');
      // PJAX 完成后，重建滚动底部观察哨与提示 UI
      try { if (typeof window.__ensureScrollNavReady === 'function') window.__ensureScrollNavReady(); } catch(_) {}
      // PJAX 完成后，初始化页面交互（如友链申请弹窗按钮）
      try { if (typeof window.__initPageInteractions === 'function') window.__initPageInteractions(); } catch(_) {}
    } catch (e) {
      // 回退到正常跳转
      main.classList.remove('page-leave-active', 'page-leave-to');
      window.location.href = href;
    }
  }
  // 导出到全局供旧拦截器识别
  window.__navTo = navTo;

  // 拦截站内链接
  // 拦截站内链接

  document.addEventListener('click', (e) => {
    const a = e.target && (e.target.closest ? e.target.closest('a') : null);
    if (!a) return;
    const href = a.getAttribute('href');
    const target = a.getAttribute('target');
    if (!href || target === '_blank') return;
    if (!isInternal(href)) return;
    // 统一使用 PJAX（包括后台），表单提交不受影响
    e.preventDefault();
    navTo(href);
  }, true);
  // 处理浏览器前进/后退
  window.addEventListener('popstate', (e) => {
    const href = (e.state && e.state.href) ? e.state.href : window.location.href;
    navTo(href, { pop: true });
  });
})();

// 全局：预加载并缓存背景图，避免在 PJAX 切换时重新请求
(function(){
  try {
    const bg = window.getComputedStyle(document.documentElement).backgroundImage || '';
    const m = bg.match(/url\(["']?(.*?)["']?\)/);
    const url = m && m[1] ? m[1] : '';
    if (url && !window.__BG_CACHE) {
      const img = new Image();
      img.src = url;
      window.__BG_CACHE = img;
    }
  } catch(e) { /* noop */ }
})();

(function(){
  window.__initPageInteractions = function(){
    try {
      var modal = document.getElementById('friendModal');
      var openBtn = document.getElementById('openFriendModal');
      var cancelBtn = document.getElementById('cancelFriendModal');
      var panel = modal ? modal.querySelector('.modal-panel') : null;
      var toggle = function(show){
        if (!modal) return;
        if (show) {
          modal.classList.remove('hidden');
          modal.classList.add('flex');
          // 进场动画：遮罩与面板
          try { modal.classList.add('modal-overlay-in'); setTimeout(function(){ modal.classList.remove('modal-overlay-in'); }, 280); } catch(_){}
          try { if (panel) { panel.classList.add('modal-panel-in'); setTimeout(function(){ panel.classList.remove('modal-panel-in'); }, 340); } } catch(_){}
        } else {
          modal.classList.add('hidden');
          modal.classList.remove('flex');
          try { modal.classList.remove('modal-overlay-in'); } catch(_){}
          try { if (panel) panel.classList.remove('modal-panel-in'); } catch(_){}
        }
      };
      if (openBtn && !openBtn.dataset.bound) {
        openBtn.addEventListener('click', function(){ toggle(true); });
        openBtn.dataset.bound = '1';
      }
      if (cancelBtn && !cancelBtn.dataset.bound) {
        cancelBtn.addEventListener('click', function(){ toggle(false); });
        cancelBtn.dataset.bound = '1';
      }
      if (modal && !modal.dataset.bound) {
        modal.addEventListener('click', function(e){ if (e.target === modal) toggle(false); });
        modal.dataset.bound = '1';
      }
      // 友链申请表单无刷新提交
      var form = document.querySelector('form[action$="/friend_request.php"]');
      if (form && !form.dataset.boundSubmit) {
        form.addEventListener('submit', function(e){
          e.preventDefault();
          var fd = new FormData(form);
          var action = form.getAttribute('action') || '/friend_request.php';
          // 发送 AJAX 请求
          fetch(action, { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function(res){ return res.json().catch(function(){ return null; }); })
            .then(function(data){
              var dest = (data && data.redirect) ? data.redirect : ((window.__BASE_PATH || '') + '/friends.php?req=ok');
              try { toggle(false); } catch(_){ }
              if (typeof window.__navTo === 'function') window.__navTo(dest); else window.location.href = dest;
            })
            .catch(function(){
              // 失败时，仍回到错误页以提示用户
              var destErr = (window.__BASE_PATH || '') + '/friends.php?req=error';
              try { toggle(false); } catch(_){ }
              if (typeof window.__navTo === 'function') window.__navTo(destErr); else window.location.href = destErr;
            });
        });
        form.dataset.boundSubmit = '1';
      }
    } catch(_){}
  };
})();

(function(){
  try {
    var modal = document.getElementById('site-notice-modal');
    var overlay = document.getElementById('site-notice-overlay');
    if (!modal || !overlay) return;

    var panel = modal.querySelector('.modal-panel');
    var base = (window.__BASE_PATH || '');
    var path = window.location.pathname || '';
    var isHome = (path === base || path === (base + '/') || /\/index\.php$/.test(path));
    var show = function(){
      overlay.classList.remove('hidden');
      modal.classList.remove('hidden');
      modal.classList.add('flex');
      try { overlay.classList.add('modal-overlay-in'); setTimeout(function(){ overlay.classList.remove('modal-overlay-in'); }, 280); } catch(_){}
      try {
        if (panel) {
          var cls = isHome ? 'modal-panel-in-top' : 'modal-panel-in';
          panel.classList.add(cls);
          setTimeout(function(){ panel.classList.remove(cls); }, 340);
        }
      } catch(_){}
    };
    var hide = function(){
      overlay.classList.add('hidden');
      modal.classList.add('hidden');
      modal.classList.remove('flex');
    };

    // 每次访问（页面加载）均显示，只在用户主动关闭后隐藏
    show();

    var closeBtn = document.getElementById('closeSiteNotice');
    var onClose = function(){ hide(); };
    if (closeBtn && !closeBtn.dataset.bound) { closeBtn.addEventListener('click', onClose); closeBtn.dataset.bound = '1'; }
    if (!overlay.dataset.bound) { overlay.addEventListener('click', onClose); overlay.dataset.bound = '1'; }
  } catch(_){}
})();