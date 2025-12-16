<?php
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/lib/auth.php';
require_once __DIR__ . '/lib/functions.php'; // wajib supaya setFlashMessage() ada

// pastikan session aktif
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$auth = new Auth();
$auth->logout();

// set notif logout
setFlashMessage('success', 'You have been logged out successfully');

// redirect ke login
header('Location: ' . APP_URL . '/login.php');
exit;
