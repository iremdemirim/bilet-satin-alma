<?php
/**
 * Firma Admini Dashboard - Mantık Dosyası
 */

$auth = new Auth();
$db = Database::getInstance();

$auth->requireCompanyAdmin();

// Giriş yapmış olan firma admininin bilgilerini al
$companyAdmin = $auth->getCurrentUser();

// Adminin atandığı firmanın bilgilerini veritabanından çek
$company = $db->getCompanyById($companyAdmin['company_id']);

// İstatistikler için
$tripCount = $db->fetchOne(
    "SELECT COUNT(id) as count FROM Trips WHERE company_id = ?", 
    [$companyAdmin['company_id']]
)['count'];

$pageTitle = 'Firma Paneli - ' . SITE_NAME;
require_once INCLUDES_PATH . '/header.php';
require_once VIEWS_PATH . '/company/dashboard.view.php';
require_once INCLUDES_PATH . '/footer.php';