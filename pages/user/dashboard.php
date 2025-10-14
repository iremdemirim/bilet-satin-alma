<?php
/**
 * User Dashboard - Mantık Dosyası
 */

$auth = new Auth();
$db = Database::getInstance();

// Bu sayfayı sadece giriş yapmış ve rolü 'user' olanlar görebilir.
$auth->requireUser();

// Mevcut kullanıcı bilgilerini al
$user = $auth->getCurrentUser();

$tickets = $db->getUserTickets($user['id']);

// Sayfa başlığını ayarla ve view dosyalarını dahil et
$pageTitle = 'Hesabım - ' . SITE_NAME;
require_once INCLUDES_PATH . '/header.php';
require_once VIEWS_PATH . '/user/dashboard.view.php';
require_once INCLUDES_PATH . '/footer.php';