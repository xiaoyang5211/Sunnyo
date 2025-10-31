<?php
require_once __DIR__ . '/../includes/config.php';
$_SESSION['admin_logged_in'] = false;
session_destroy();
header('Location: ' . ($basePath !== '' ? $basePath : '') . '/admin/login.php');
exit;