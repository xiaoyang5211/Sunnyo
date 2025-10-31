<?php
// Public webroot shim: serve admin account page under /admin/account.php
require_once __DIR__ . '/../../includes/config.php';
requireLogin();
// Delegate to the actual admin page implementation
require_once __DIR__ . '/../../admin/account.php';