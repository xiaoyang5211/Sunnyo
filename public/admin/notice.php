<?php
// Public shim: route to private admin implementation, require login
require_once __DIR__ . '/../../includes/config.php';
requireLogin();
require_once __DIR__ . '/../../admin/notice.php';
