<?php
require_once __DIR__ . '/../config/bootstrap.php';
unset($_SESSION['admin_id'], $_SESSION['admin_name']);
header('Location: ' . asset_url('admin/login.php'));
exit;
