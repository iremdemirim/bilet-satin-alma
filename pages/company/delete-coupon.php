<?php
/**
 * Firma Admini - Kupon Silme Mantığı
 */

$auth = new Auth();
$db = Database::getInstance();

// Bu işlemi sadece firma adminleri yapabilir
$auth->requireCompanyAdmin();
$companyAdmin = $auth->getCurrentUser();

$couponId = $_GET['id'] ?? null;

// ID yoksa direkt listeye yönlendir
if (!$couponId) {
    header('Location: /company/coupons');
    exit;
}

$coupon = $db->getCouponById($couponId);

if (!$coupon || $coupon['company_id'] !== $companyAdmin['company_id']) {
    set_flash_message('error', 'Geçersiz veya yetkiniz olmayan bir kuponu silmeye çalıştınız.');
    header('Location: /company/coupons');
    exit;
}

// Kuponu veritabanından sil
$sql = "DELETE FROM Coupons WHERE id = ? AND company_id = ?";

if ($db->execute($sql, [$couponId, $companyAdmin['company_id']])) {
    set_flash_message('success', "'{$coupon['code']}' kodlu kupon başarıyla silindi.");
} else {
    set_flash_message('error', 'Kupon silinirken bir veritabanı hatası oluştu.');
}

// Kullanıcıyı kupon listesine geri yönlendir
header('Location: /company/coupons');
exit;