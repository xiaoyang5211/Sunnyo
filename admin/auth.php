<?php
require_once __DIR__ . '/../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf()) {
        header('Location: ' . ($basePath !== '' ? $basePath : '') . '/admin/login.php?error=csrf');
        exit;
    }
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // 支持明文或哈希两种校验方式（默认明文 admin123）
    $valid = false;
    if (!empty($adminConfig['password'])) {
        $valid = ($username === $adminConfig['username'] && $password === $adminConfig['password']);
    } elseif (!empty($adminConfig['password_hash'])) {
        $valid = ($username === $adminConfig['username'] && password_verify($password, $adminConfig['password_hash']));
    }

    if ($valid) {
        $_SESSION['admin_logged_in'] = true;
        // 成功后使用 PJAX 进入后台：返回一个极简页面，立即触发 navTo('/admin/')，无任何可见提示
        $target = ($basePath !== '' ? $basePath : '') . '/admin/';
        $mainJs = $basePath . '/assets/js/main.js?v=' . $assetVersionJs;
        $mainJsEsc = htmlspecialchars($mainJs, ENT_QUOTES);
        $targetEsc = htmlspecialchars($target, ENT_QUOTES);

        echo "<!DOCTYPE html>\n<html lang=\"zh-CN\">\n<head>\n<meta charset=\"UTF-8\">\n<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">\n<title></title>\n";
        // 注入 BASE_PATH，保持与前端一致
        echo "<script>window.__BASE_PATH = " . json_encode($basePath) . ";</script>\n";
        echo "<script>(function(){\n" .
             "  var mainJs = " . json_encode($mainJsEsc) . ";\n" .
             "  var to = " . json_encode($targetEsc) . ";\n" .
             "  function loadMainAndGo(){\n" .
             "    var s = document.createElement('script'); s.src = mainJs; s.crossOrigin = 'anonymous'; s.onload = function(){ if (typeof window.__navTo === 'function') { window.__navTo(to); } else { window.location.href = to; } }; document.head.appendChild(s);\n" .
             "  }\n" .
             "  function go(){\n" .
             "    try {\n" .
             "      if (document.querySelector('main')) { loadMainAndGo(); } else {\n" .
             "        var already = [].slice.call(document.scripts).some(function(x){ return x.src && x.src.indexOf(mainJs) !== -1; });\n" .
             "        if (!already) loadMainAndGo(); else setTimeout(function(){ if (typeof window.__navTo === 'function') { window.__navTo(to); } else { window.location.href = to; } }, 0);\n" .
             "      }\n" .
             "    } catch(e) { window.location.href = to; }\n" .
             "  }\n" .
             "  // 优先在 DOM 就绪后执行，确保存在 <main>\n" .
             "  if (document.readyState === 'complete' || document.readyState === 'interactive') { setTimeout(go, 0); } else { document.addEventListener('DOMContentLoaded', go); }\n" .
             "  // 备份触发\n" .
             "  window.addEventListener('load', go);\n" .
             "  // 最终兜底\n" .
             "  setTimeout(function(){ if (typeof window.__navTo === 'function') { window.__navTo(to); } else { window.location.href = to; } }, 4000);\n" .
             "})();\n" .
             "</script>\n";
        // noscript 兜底：直接跳转
        echo "<noscript><meta http-equiv=\"refresh\" content=\"0;url={$targetEsc}\"></noscript>\n";
        echo "</head>\n<body><main class=\"mx-auto mt-8 w-full px-4\"></main></body>\n</html>";
        exit;
    }
}

// 失败时保持原行为（或可改为 PJAX）
header('Location: ' . ($basePath !== '' ? $basePath : '') . '/admin/login.php?error=1');
exit;