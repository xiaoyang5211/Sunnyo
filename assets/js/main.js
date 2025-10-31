// 移动端菜单开关与外部点击处理
(function() {
  const toggle = document.getElementById('nav-toggle');
  const menu = document.getElementById('nav-menu');
  const overlay = document.getElementById('nav-overlay');

  if (!toggle || !menu || !overlay) return;

  const closeMenu = () => {
    menu.classList.add('hidden');
    overlay.classList.add('hidden');
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
    if (e.type === 'touchstart') { /* 防止默认滚动 */ }
    if (isOpen()) { closeMenu(); } else { openMenu(); }
  };

  toggle.addEventListener('click', handleToggle);
  toggle.addEventListener('touchstart', handleToggle, { passive: true });
  toggle.addEventListener('pointerdown', handleToggle);

  overlay.addEventListener('click', closeMenu);
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

// 通用页面进场效果：为主内容 main 添加与源代码一致的淡入动画
(function() {
  window.addEventListener('load', () => {
    const main = document.querySelector('main');
    if (!main) return;
    main.classList.add('content');
    setTimeout(() => { main.classList.add('fade-in'); }, 50);
  });
})();

// 首页动画（简化）
(function() {
  const logo = document.getElementById('home-logo');
  const subtitle = document.getElementById('home-subtitle');
  const content = document.getElementById('home-content');

  window.addEventListener('load', () => {
    setTimeout(() => { if (logo) logo.classList.add('bounce-in'); }, 200);
    setTimeout(() => { if (subtitle) subtitle.classList.add('fade-in'); }, 500);
    setTimeout(() => { if (content) content.classList.add('fade-in-delayed'); }, 800);
  });
})();

// 桌面端：到达底部后再次下滑才进入“下一个页面”，并显示滚动进度百分比（后台禁用；支持 __NEXT_PAGE 优先）
(function() {
  const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) || window.innerWidth <= 768;
  if (isMobile) return;

  const base = (window.__BASE_PATH || '');
  const path = window.location.pathname || '';
  const isAdmin = /\/admin\//.test(path) || path.startsWith(base + '/admin');
  if (isAdmin) return;

  const normalize = (p) => {
    if (!p) return '/';
    try { p = new URL(p, window.location.origin).pathname; } catch(_) {}
    p = p.replace(/\/+$/, '');
    return p === '' ? '/' : p;
  };

  let nextCandidate = (typeof window.__NEXT_PAGE === 'string' && window.__NEXT_PAGE) ? window.__NEXT_PAGE : null;

  let pages = Array.from(document.querySelectorAll('#nav-menu-desktop a.nav-link'))
    .map(a => a.getAttribute('href'))
    .filter(h => h && !/\/admin\//.test(h))
    .map(normalize);

  if (!pages.length) {
    pages = [
      base + '/',
      base + '/index.php',
      base + '/articles.php',
      base + '/about.php',
      base + '/friends.php',
      base + '/projects.php',
      base + '/website.php'
    ].map(normalize);
  }

  const seen = new Set();
  pages = pages.filter(p => { const n = normalize(p); if (seen.has(n)) return false; seen.add(n); return true; });

  const current = normalize(path);
  let idx = pages.indexOf(current);
  if (idx === -1 && (current === normalize(base + '/') || current === normalize(base + '/index.php'))) {
    idx = pages.indexOf(normalize(base + '/'));
    if (idx === -1) idx = pages.indexOf(normalize(base + '/index.php'));
  }
  if (idx === -1 && !nextCandidate) return;

  const next = nextCandidate ? normalize(nextCandidate) : pages[(idx + 1) % pages.length];

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

  const atBottom = () => {
    const doc = document.documentElement;
    const scrollTop = window.scrollY || doc.scrollTop || 0;
    const clientHeight = doc.clientHeight || window.innerHeight;
    const scrollHeight = doc.scrollHeight;
    return scrollTop + clientHeight >= scrollHeight - 2;
  };

  const updateProgress = (fromScroll) => {
    const doc = document.documentElement;
    const scrollTop = window.scrollY || doc.scrollTop || 0;
    const clientHeight = doc.clientHeight || window.innerHeight;
    const scrollHeight = doc.scrollHeight || document.body.scrollHeight;
    const percent = Math.min(100, Math.round(((scrollTop + clientHeight) / Math.max(1, scrollHeight)) * 100));

    valueEl.textContent = percent + '%';

    if (atBottom()) {
      if (!fromScroll) return;
      hint.classList.remove('hidden');
    } else {
      hint.classList.add('hidden');
    }

    if (atBottom()) {
      hint.classList.remove('hidden');
      bottomReady = true;
      requireSecondScroll = true;
      lastAfterReadyTs = 0;
    }
    if (fromScroll && percent < 99) {
      bottomReady = false;
      requireSecondScroll = false;
    }

    labelEl.textContent = (bottomReady && requireSecondScroll) ? '已到达底部，再次下滑进入下一页' : '滚动到底部';
  };

  updateProgress(false);
  window.addEventListener('scroll', () => updateProgress(true), { passive: true });

  let hasScrolled = false;
  let bottomReady = false;
  let requireSecondScroll = false;
  let lastAfterReadyTs = 0;

  const handler = (e) => {
    if (e.deltaY > 0) {
      if (!bottomReady && atBottom()) {
        bottomReady = true;
        requireSecondScroll = true;
        lastAfterReadyTs = performance.now();
        labelEl.textContent = '已到达底部，再次下滑进入下一页';
        return;
      }
      if (!bottomReady) return;
      if (requireSecondScroll) {
        requireSecondScroll = false;
        lastAfterReadyTs = performance.now();
        return;
      }
      if (performance.now() - lastAfterReadyTs < 250) return;
      e.preventDefault();
      window.removeEventListener('wheel', handler);
      setTimeout(() => { window.location.href = next; }, 900);
    } else if (e.deltaY < 0) {
      bottomReady = false;
      requireSecondScroll = false;
      lastAfterReadyTs = 0;
      labelEl.textContent = '滚动到底部';
    }
  };
  window.addEventListener('wheel', handler, { passive: false });
})();

// 自定义光标（跟随效果简化版）
(function() {
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
    cursor.style.left = mouseX + 'px';
    cursor.style.top = mouseY + 'px';
  }, { passive: true });

  const animate = () => {
    fx += (mouseX - fx) * 0.12;
    fy += (mouseY - fy) * 0.12;
    follower.style.left = fx + 'px';
    follower.style.top = fy + 'px';
    requestAnimationFrame(animate);
  };
  animate();

  const hoverTargets = document.querySelectorAll('a, button, input, textarea, select, [role="button"], [tabindex]:not([tabindex="-1"])');
  hoverTargets.forEach(el => {
    el.addEventListener('mouseenter', () => { cursor.classList.add('hover'); follower.classList.add('hover'); });
    el.addEventListener('mouseleave', () => { cursor.classList.remove('hover'); follower.classList.remove('hover'); });
  });
})();