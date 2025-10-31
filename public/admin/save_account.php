<?php
// Public webroot shim for saving admin account changes
require_once __DIR__ . '/../../includes/config.php';
requireLogin();
require_once __DIR__ . '/../../admin/save_account.php';