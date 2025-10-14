<?php
/**
 * Admin - Firma Silme Mantığı
 */

$auth = new Auth();
$db = Database::getInstance();

// Bu işlemi sadece adminler yapabilir
$auth->requireAdmin();

$companyId = $_GET['id'] ?? null;

// ID yoksa veya firma bulunamazsa, bir hata mesajı ile listeleme sayfasına yönlendir.
if (!$companyId) {
    set_flash_message('error', 'Geçersiz firma IDsi.');
    header('Location: /admin/companies');
    exit;
}

// Silinecek firmayı veritabanından kontrol et (opsiyonel ama iyi bir pratik)
$company = $db->getCompanyById($companyId);
if (!$company) {
    set_flash_message('error', 'Silinecek firma bulunamadı.');
    header('Location: /admin/companies');
    exit;
}

// Veritabanından firmaya ait logoyu sil (sunucudan yer kaplamasın)
if (!empty($company['logo_path']) && file_exists(ROOT_PATH . '/public' . $company['logo_path'])) {
    unlink(ROOT_PATH . '/public' . $company['logo_path']);
}

// Firmayı veritabanından sil
$sql = "DELETE FROM Bus_Company WHERE id = ?";

if ($db->execute($sql, [$companyId])) {
    // Başarı mesajı ayarla
    set_flash_message('success', "'{$company['name']}' adlı firma başarıyla silindi.");
} else {
    // Hata mesajı ayarla
    set_flash_message('error', 'Firma silinirken bir veritabanı hatası oluştu.');
}

// Kullanıcıyı firma listesine geri yönlendir
header('Location: /admin/companies');
exit;