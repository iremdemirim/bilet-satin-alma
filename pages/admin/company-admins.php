<?php
/**
 * Admin - Firma Admini Listeleme (Mantık)
 */

$auth = new Auth();
$db = Database::getInstance();

// Bu sayfaya sadece adminler erişebilir
$auth->requireAdmin();

$sql = "SELECT 
            u.id, 
            u.full_name, 
            u.email, 
            u.created_at, 
            bc.name as company_name 
        FROM User u
        LEFT JOIN Bus_Company bc ON u.company_id = bc.id
        WHERE u.role = ? 
        ORDER BY u.created_at DESC";

$companyAdmins = $db->fetchAll($sql, [ROLE_COMPANY]);

$pageTitle = 'Firma Admini Yönetimi - Admin Paneli';
require_once INCLUDES_PATH . '/header.php';
require_once VIEWS_PATH . '/admin/company-admins.view.php';
require_once INCLUDES_PATH . '/footer.php';