<?php
/**
 * Admin - Kupon Silme Mantığı
 */

$auth = new Auth();
$db = Database::getInstance();

// Bu işlemi sadece adminler yapabilir
$auth->requireAdmin();

$couponId = $_GET['id'] ?? null;

// ID yoksa direkt listeye yönlendir
if (!$couponId) {
    header('Location: /admin/coupons');
    exit;
}

// Silme işlemi öncesi kuponun adını alalım ki başarı mesajında gösterebilelim.
$coupon = $db->getCouponById($couponId);

// Kuponu veritabanından sil
$sql = "DELETE FROM Coupons WHERE id = ?";

if ($db->execute($sql, [$couponId])) {
    // Başarı mesajı ayarla
    $couponCode = $coupon ? "'{$coupon['code']}' kodlu" : "Seçilen";
    set_flash_message('success', "$couponCode kupon başarıyla silindi.");
} else {
    // Hata mesajı ayarla
    set_flash_message('error', 'Kupon silinirken bir veritabanı hatası oluştu.');
}

// Kullanıcıyı kupon listesine geri yönlendir
header('Location: /admin/coupons');
exit;