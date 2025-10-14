<?php
/**
 * Biletlerim Sayfası - Mantık
 */

$auth = new Auth();
$db = Database::getInstance();

$auth->requireUser();
$user = $auth->getCurrentUser();

// Veritabanından kullanıcının tüm biletlerini çek
$tickets = $db->getUserTickets($user['id']);

$pageTitle = 'Biletlerim - ' . SITE_NAME;
require_once INCLUDES_PATH . '/header.php';
require_once VIEWS_PATH . '/user/my-tickets.view.php';
require_once INCLUDES_PATH . '/footer.php';