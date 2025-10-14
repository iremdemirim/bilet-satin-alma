<?php
/**
 * Firma Admini - Sefer Silme Mantığı
 */

$auth = new Auth();
$db = Database::getInstance();

// Bu işlemi sadece firma adminleri yapabilir
$auth->requireCompanyAdmin();
$companyAdmin = $auth->getCurrentUser();

$tripId = $_GET['id'] ?? null;

// ID yoksa direkt listeye yönlendir
if (!$tripId) {
    header('Location: /company/trips');
    exit;
}

// Silinecek seferi veritabanından çek
$trip = $db->getTripById($tripId);

if (!$trip || $trip['company_id'] !== $companyAdmin['company_id']) {
    set_flash_message('error', 'Geçersiz veya yetkiniz olmayan bir seferi silmeye çalıştınız.');
    header('Location: /company/trips');
    exit;
}

// Seferi veritabanından sil
$sql = "DELETE FROM Trips WHERE id = ? AND company_id = ?";

if ($db->execute($sql, [$tripId, $companyAdmin['company_id']])) {
    set_flash_message('success', "Sefer başarıyla silindi.");
} else {
    set_flash_message('error', 'Sefer silinirken bir veritabanı hatası oluştu.');
}

// Kullanıcıyı sefer listesine geri yönlendir
header('Location: /company/trips');
exit;