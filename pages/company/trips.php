<?php
/**
 * Firma Admini - Sefer Listeleme (Mantık)
 */

$auth = new Auth();
$db = Database::getInstance();

// Bu sayfaya sadece firma adminleri erişebilir
$auth->requireCompanyAdmin();

// Giriş yapmış olan firma admininin bilgilerini al
$companyAdmin = $auth->getCurrentUser();

// Sadece bu firmanın seferlerini veritabanından çek
$trips = $db->getTripsByCompanyId($companyAdmin['company_id']);

$pageTitle = 'Sefer Yönetimi - Firma Paneli';
require_once INCLUDES_PATH . '/header.php';
require_once VIEWS_PATH . '/company/trips.view.php';
require_once INCLUDES_PATH . '/footer.php';