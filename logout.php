<?php
require_once __DIR__ . '/config/bootstrap.php';

logout_user();
session_regenerate_id(true);
set_flash('success', 'You have been logged out.');
header('Location: ' . asset_url('login.php'));
exit;
