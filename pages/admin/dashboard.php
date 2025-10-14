<?php
/**
 * Admin Dashboard - Mantık Dosyası
 */

$auth = new Auth();
$db = Database::getInstance();

$auth->requireAdmin();

// Panele bazı istatistikler eklemek için verileri çekebiliriz 
$companyCount = $db->fetchOne("SELECT COUNT(id) as count FROM Bus_Company")['count'];
$userCount = $db->fetchOne("SELECT COUNT(id) as count FROM User WHERE role = 'user'")['count'];
$tripCount = $db->fetchOne("SELECT COUNT(id) as count FROM Trips")['count'];

$pageTitle = 'Admin Paneli - ' . SITE_NAME;
require_once INCLUDES_PATH . '/header.php';
require_once VIEWS_PATH . '/admin/dashboard.view.php';
require_once INCLUDES_PATH . '/footer.php';