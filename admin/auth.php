<?php
require_once __DIR__ . '/../config/bootstrap.php';

function require_admin(): void
{
    if (empty($_SESSION['admin_id'])) {
        header('Location: /burmese-desserts/admin/login.php');
        exit;
    }
}
