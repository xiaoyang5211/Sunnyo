<?php
// 基础站点配置（从 Nuxt 配置迁移）
$siteConfig = [
    'site' => [
        'title' => 'Haku Fumomo',
        'subtitle' => "Hi, I'm Haku Fumomo",
        'description' => '你好，欢迎来到Haku的个人主页Fumomo！',
        'url' => 'https://haku.sakura.ink',
    ],
    'home' => [
        'mainTitle' => 'Fumomo',
        'welcomeText' => '一个充满温暖与创意的小窝',
        'features' => [
            [
                'title' => '技术分享',
                'description' => '分享编程技巧、框架使用心得和技术思考'
            ],
            [
                'title' => '生活记录',
                'description' => '记录日常生活中的美好瞬间和感悟'
            ],
            [
                'title' => '创意项目',
                'description' => '展示个人项目和创意作品'
            ],
        ]
    ],
    'navigation' => [
        ['name' => '首页', 'href' => '/index.php', 'key' => 'home'],
        ['name' => '文章', 'href' => '/articles.php', 'key' => 'articles'],
        ['name' => '关于', 'href' => '/about.php', 'key' => 'about'],
        ['name' => '友链', 'href' => '/friends.php', 'key' => 'friends'],
        ['name' => '项目', 'href' => '/projects.php', 'key' => 'projects'],
        ['name' => '网站', 'href' => '/website.php', 'key' => 'website'],
    ],

    'personal' => [
        'name' => 'Haku',
        'bio' => '平时喜欢整点新奇玩意，喜欢做点小项目。',
        'hobby' => '编程开发、网页设计',
        'location' => '广东深圳',
        'learning' => 'Astro、VUE、C#',
        'avatar' => 'https://q2.qlogo.cn/headimg_dl?dst_uin=2731443459&spec=5',
        'social' => [
            'github' => 'https://github.com/Hakutyan-bai',
            'email' => 'lzj159035@foxmail.com'
        ],
    ],
    'theme' => [
        'primaryColor' => '#8b5a8c',
        'textColor' => '#666',
        'fontFamily' => "'Comic Sans MS', 'XiaokeNailao', cursive, sans-serif",
        'customCursor' => true,
        'backgroundGradient' => 'linear-gradient(135deg, #f7e9ff 0%, #e9d5ff 50%, #d8b4fe 100%)',
        'backgroundType' => 'gradient',
        'backgroundImageUrl' => 'https://t.alcy.cc/ycy',
        // 新增：按钮强调色与背景图（用于导航/CTA）
        'accentColor' => '#ff7aa2',
        'accentImageUrl' => '',
    ],
    'umami' => [
        'enable' => true,
        'websiteId' => 'ac5e0626-4863-41ec-8a66-98ba076846a0',
        'scriptSrc' => 'https://cloud.umami.is/script.js'
    ],
];

// 后台管理配置
$adminConfig = [
    'login_enabled' => true,
    // 默认用户名和密码（请在生产环境中修改）
    'username' => 'admin',
    // 使用 password_hash 生成的哈希（示例密码：admin123）
    'password' => 'admin123',
    'password_hash' => ''
];

// 启用会话（设置安全的 Cookie 参数）
if (session_status() === PHP_SESSION_NONE) {
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}
// CSRF 令牌：生成、输出隐藏字段与校验
if (empty($_SESSION['_csrf'])) { $_SESSION['_csrf'] = bin2hex(random_bytes(16)); }
function csrfToken(): string { return $_SESSION['_csrf'] ?? ''; }
function csrfField(): string { return '<input type="hidden" name="csrf" value="' . htmlspecialchars(csrfToken(), ENT_QUOTES) . '">'; }
function verifyCsrf(): bool {
    $token = $_POST['csrf'] ?? ($_GET['csrf'] ?? '');
    return is_string($token) && hash_equals($_SESSION['_csrf'] ?? '', $token);
}

function isLoggedIn(): bool {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function requireLogin() {
    if (!isLoggedIn()) {
        global $basePath; // 使用部署子路径
        header('Location: ' . ($basePath !== '' ? $basePath : '') . '/admin/login.php');
        exit;
    }
}

// 读取可配置设置（覆盖默认配置）
$settingsFile = __DIR__ . '/../data/settings.json';
if (is_file($settingsFile)) {
    $loaded = json_decode(file_get_contents($settingsFile), true);
    if (is_array($loaded)) {
        $siteConfig = array_replace_recursive($siteConfig, $loaded);
    }
}

// 看板娘（Live2D）默认配置
if (!isset($siteConfig['live2d']) || !is_array($siteConfig['live2d'])) {
    $siteConfig['live2d'] = [ 'enable' => 1 ];
} else {
    $siteConfig['live2d']['enable'] = !empty($siteConfig['live2d']['enable']) ? 1 : 0;
}

// 新增：底部音乐播放器（明月浩空 myhkw）默认配置与归一化
if (!isset($siteConfig['music']) || !is_array($siteConfig['music'])) {
    $siteConfig['music'] = [ 'myhk' => [ 'enable' => 0, 'key' => '', 'mode' => 1 ] ];
} else {
    $m = isset($siteConfig['music']['myhk']) && is_array($siteConfig['music']['myhk']) ? $siteConfig['music']['myhk'] : [];
    $siteConfig['music']['myhk'] = [
      'enable' => !empty($m['enable']) ? 1 : 0,
      'key' => preg_replace('/[^0-9]/', '', (string)($m['key'] ?? '')),
      'mode' => intval($m['mode'] ?? 1),
    ];
}
// 文章源默认配置（支持 RSSHub 或手动维护）
if (!isset($siteConfig['articles']) || !is_array($siteConfig['articles'])) {
    $siteConfig['articles'] = [
        'source' => 'manual',
        'rsshubUrl' => '',
        'cacheTtlSec' => 600,
    ];
} else {
    $a = $siteConfig['articles'];
    $siteConfig['articles']['source'] = (($a['source'] ?? 'manual') === 'rsshub') ? 'rsshub' : 'manual';
    $siteConfig['articles']['rsshubUrl'] = trim($a['rsshubUrl'] ?? '');
    $siteConfig['articles']['cacheTtlSec'] = intval($a['cacheTtlSec'] ?? 600);
}

// 保存设置辅助函数（供后台使用）
function saveSiteSettings(array $data): bool {
    $settingsDir = __DIR__ . '/../data';
    if (!is_dir($settingsDir)) { mkdir($settingsDir, 0777, true); }
    $settingsFile = $settingsDir . '/settings.json';
    $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    return file_put_contents($settingsFile, $json) !== false;
}

// 通用数据读写（articles/friends/projects/websites 等）
function readData(string $name, $default = []) {
    $file = __DIR__ . '/../data/' . $name . '.json';
    if (!is_file($file)) return $default;
    $data = json_decode(@file_get_contents($file), true);
    return is_array($data) ? $data : $default;
}

function saveData(string $name, $data): bool {
    $dir = __DIR__ . '/../data';
    if (!is_dir($dir)) { mkdir($dir, 0777, true); }
    $file = $dir . '/' . $name . '.json';
    $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    return file_put_contents($file, $json) !== false;
}

function addDataItem(string $name, array $item): bool {
    $list = readData($name, []);
    $list[] = $item;
    return saveData($name, $list);
}

function updateDataItem(string $name, int $index, array $item): bool {
    $list = readData($name, []);
    if (!isset($list[$index])) return false;
    $list[$index] = $item;
    return saveData($name, $list);
}

function deleteDataItem(string $name, int $index): bool {
    $list = readData($name, []);
    if (!isset($list[$index])) return false;
    array_splice($list, $index, 1);
    return saveData($name, $list);
}

// 加载后台账号配置（覆盖默认值），支持持久化到 data/admin.json
$__adminLoaded = readData('admin', []);
if (is_array($__adminLoaded) && count($__adminLoaded)) {
    $adminConfig = array_replace($adminConfig, $__adminLoaded);
}

// RSSHub 拉取与解析（带缓存）
function fetchRssHubItems(string $url, int $ttl = 600, int $maxItems = 50): array {
    $url = trim($url);
    if ($url === '') return [];
    $cacheDir = __DIR__ . '/../data';
    if (!is_dir($cacheDir)) { mkdir($cacheDir, 0777, true); }
    $cacheFile = $cacheDir . '/rss_cache_' . md5($url) . '.json';
    $now = time();

    // 命中缓存
    if (is_file($cacheFile) && ($now - filemtime($cacheFile) < max(60, $ttl))) {
        $json = @file_get_contents($cacheFile);
        $data = json_decode($json, true);
        if (is_array($data)) return $data;
    }

    // 抓取
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_CONNECTTIMEOUT => 8,
        CURLOPT_TIMEOUT => 12,
        CURLOPT_USERAGENT => 'PHP RSSHub Client/1.0',
        CURLOPT_HTTPHEADER => [
            'Accept: application/rss+xml, application/atom+xml;q=0.9, text/xml;q=0.8',
        ],
    ]);
    $res = curl_exec($ch);
    $err = ($res === false) ? curl_error($ch) : '';
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($res === false || $code < 200 || $code >= 300) {
        // 回退缓存
        if (is_file($cacheFile)) {
            $json = @file_get_contents($cacheFile);
            $data = json_decode($json, true);
            return is_array($data) ? $data : [];
        }
        return [];
    }

    // 解析 XML
    libxml_use_internal_errors(true);
    $xml = simplexml_load_string($res, 'SimpleXMLElement', LIBXML_NOCDATA);
    if (!$xml) {
        // 回退缓存
        if (is_file($cacheFile)) {
            $json = @file_get_contents($cacheFile);
            $data = json_decode($json, true);
            return is_array($data) ? $data : [];
        }
        return [];
    }

    $items = [];
    // RSS 2.0
    if (isset($xml->channel) && isset($xml->channel->item)) {
        foreach ($xml->channel->item as $it) {
            $items[] = [
                'title' => (string) ($it->title ?? ''),
                'url' => (string) ($it->link ?? ''),
                'description' => (string) ($it->description ?? ''),
                'pubDate' => (string) ($it->pubDate ?? ''),
            ];
            if (count($items) >= $maxItems) break;
        }
    }
    // Atom
    elseif (isset($xml->entry)) {
        foreach ($xml->entry as $entry) {
            $link = '';
            foreach ($entry->link as $ln) {
                $attrs = $ln->attributes();
                if (isset($attrs['href'])) { $link = (string) $attrs['href']; break; }
            }
            $items[] = [
                'title' => (string) ($entry->title ?? ''),
                'url' => $link,
                'description' => (string) ($entry->summary ?? $entry->content ?? ''),
                'pubDate' => (string) ($entry->updated ?? $entry->published ?? ''),
            ];
            if (count($items) >= $maxItems) break;
        }
    }

    // 写入缓存
    @file_put_contents($cacheFile, json_encode($items, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    return $items;
}

// 新增：URL 在线状态检测（带简易文件缓存，默认 60s）
function checkUrlStatus(string $url, int $ttl = 60): array {
  $url = trim($url);
  if ($url === '') { return ['online' => false, 'code' => 0, 'checkedAt' => time()]; }

  $cacheDir = __DIR__ . '/../data';
  if (!is_dir($cacheDir)) { @mkdir($cacheDir, 0777, true); }
  $cacheFile = $cacheDir . '/url_status_' . md5($url) . '.json';
  $now = time();

  // 命中缓存（至少 10s，默认 ttl）
  $ttl = max(10, intval($ttl));
  if (is_file($cacheFile) && ($now - @filemtime($cacheFile) < $ttl)) {
    $json = @file_get_contents($cacheFile);
    $data = json_decode($json, true);
    if (is_array($data) && isset($data['online'])) { return $data; }
  }

  // 执行 HEAD 请求快速探测（跟随跳转）；若服务器不支持 HEAD，将回退到 GET
  $ch = curl_init($url);
  curl_setopt_array($ch, [
    CURLOPT_NOBODY => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_CONNECTTIMEOUT => 5,
    CURLOPT_TIMEOUT => 8,
    CURLOPT_USERAGENT => 'PHP SiteStatus/1.0',
    // 状态检测容忍自签名证书，避免误判为离线
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => 0,
  ]);
  $res = curl_exec($ch);
  $code = curl_getinfo($ch, CURLINFO_HTTP_CODE) ?: 0;
  $err = ($res === false) ? (curl_error($ch) ?: '') : '';
  $ok = ($code >= 200 && $code < 400);

  // 某些站点对 HEAD 返回 405/403，尝试一次 GET（不读取内容）
  if (!$ok && ($code === 0 || $code === 405 || $code === 403)) {
    curl_setopt_array($ch, [
      CURLOPT_NOBODY => false,
      CURLOPT_HTTPGET => true,
      CURLOPT_RANGE => '0-0', // 只取 1 字节，避免流量
    ]);
    $res = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE) ?: 0;
    $err = ($res === false) ? (curl_error($ch) ?: '') : '';
    $ok = ($code >= 200 && $code < 400);
  }
  curl_close($ch);

  $data = ['online' => $ok, 'code' => $code, 'error' => $err, 'checkedAt' => $now];
  @file_put_contents($cacheFile, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
  return $data;
}

// 增加部署子路径支持：根据当前脚本路径计算资源前缀（去除 /admin 尾段）
$script = $_SERVER['SCRIPT_NAME'] ?? '';
$dir = rtrim(dirname($script), '/\\');
if ($dir === '/' || $dir === '\\' || $dir === '.') { $dir = ''; }
// 当处于后台（/admin）目录时，资产仍应从站点根路径提供，移除尾部的 /admin
if ($dir !== '' && preg_match('#/admin$#', $dir)) {
  $dir = rtrim(dirname($dir), '/\\');
  if ($dir === '/' || $dir === '\\' || $dir === '.') { $dir = ''; }
}
$basePath = $dir;

// 资源版本：用于 cache busting（优先使用实际文件修改时间）
$assetVersionJs = (function(){
    $file = __DIR__ . '/../public/assets/js/main.js';
    if (is_file($file)) { return (string) filemtime($file); }
    return (string) time();
})();

$assetVersionCss = (function(){
    $file = __DIR__ . '/../public/assets/css/style.css';
    if (is_file($file)) { return (string) filemtime($file); }
    // 回退到非 public 版本
    $alt = __DIR__ . '/../assets/css/style.css';
    if (is_file($alt)) { return (string) filemtime($alt); }
    return (string) time();
})();