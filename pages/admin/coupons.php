<?php
/**
 * Admin - Kupon Listeleme (Mantık)
 */

$auth = new Auth();
$db = Database::getInstance();

// Bu sayfaya sadece adminler erişebilir
$auth->requireAdmin();

$coupons = $db->getAllCoupons();

$pageTitle = 'Kupon Yönetimi - Admin Paneli';
require_once INCLUDES_PATH . '/header.php';
require_once VIEWS_PATH . '/admin/coupons.view.php';
require_once INCLUDES_PATH . '/footer.php';