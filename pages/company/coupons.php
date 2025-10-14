<?php
/**
 * Firma Admini - Kupon Listeleme (Mantık)
 */

$auth = new Auth();
$db = Database::getInstance();

// Bu sayfaya sadece firma adminleri erişebilir
$auth->requireCompanyAdmin();
$companyAdmin = $auth->getCurrentUser();

// Sadece bu firmanın kuponlarını veritabanından çek
$coupons = $db->getCouponsByCompanyId($companyAdmin['company_id']);

$pageTitle = 'Kupon Yönetimi - Firma Paneli';
require_once INCLUDES_PATH . '/header.php';
require_once VIEWS_PATH . '/company/coupons.view.php';
require_once INCLUDES_PATH . '/footer.php';