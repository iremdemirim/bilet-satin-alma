<?php
/**
 * Logout Page
 * Güvenli çıkış işlemi
 */

// Config'i doğru path ile dahil et
if (!defined('SITE_NAME')) {
    require_once __DIR__ . '/../../config/config.php';
}

$auth = new Auth();

// Kullanıcı giriş yapmışsa çıkış yap
if ($auth->isLoggedIn()) {
    $auth->logout();
}

// Ana sayfaya yönlendir
header('Location: /');
exit;