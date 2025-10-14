<?php
/**
 * Admin - Firma Listeleme Sayfası (Mantık)
 */

$auth = new Auth();
$db = Database::getInstance();

// Bu sayfaya sadece adminler erişebilir
$auth->requireAdmin();

// Database sınıfındaki hazır metodu kullanarak tüm firmaları çek
$companies = $db->getAllCompanies();

$pageTitle = 'Firma Yönetimi - Admin Paneli';
require_once INCLUDES_PATH . '/header.php';
require_once VIEWS_PATH . '/admin/companies.view.php';
require_once INCLUDES_PATH . '/footer.php';