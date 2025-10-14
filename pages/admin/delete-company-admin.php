<?php
/**
 * Admin - Firma Admini Silme Mantığı
 */

$auth = new Auth();
$db = Database::getInstance();

// Bu işlemi sadece adminler yapabilir
$auth->requireAdmin();

$userId = $_GET['id'] ?? null;

// ID yoksa veya kullanıcı bulunamazsa, bir hata mesajı ile listeleme sayfasına yönlendir.
if (!$userId) {
    set_flash_message('error', 'Geçersiz kullanıcı IDsi.');
    header('Location: /admin/company-admins');
    exit;
}

// Silinecek kullanıcının adını mesajda göstermek için bilgilerini al
$user = $db->getUserById($userId);
if (!$user) {
    set_flash_message('error', 'Silinecek kullanıcı bulunamadı.');
    header('Location: /admin/company-admins');
    exit;
}

// Admin kendi kendini silemesin diye bir kontrol ekle
$currentUser = $auth->getCurrentUser();
if ($currentUser['id'] === $userId) {
    set_flash_message('error', 'Güvenlik nedeniyle kendinizi silemezsiniz.');
    header('Location: /admin/company-admins');
    exit;
}

$sql = "DELETE FROM User WHERE id = ? AND role = ?"; 

if ($db->execute($sql, [$userId, ROLE_COMPANY])) {
    set_flash_message('success', "'{$user['full_name']}' adlı firma admini başarıyla silindi.");
} else {
    set_flash_message('error', 'Kullanıcı silinirken bir veritabanı hatası oluştu.');
}
header('Location: /admin/company-admins');
exit;