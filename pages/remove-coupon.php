<?php
/**
 * Uygulanan Kuponu Kaldırma Mantığı
 */

$auth = new Auth();
$auth->requireUser(); // Sadece giriş yapmış kullanıcılar

// Session'da checkout bilgisi ve kupon var mı kontrol et
if (isset($_SESSION['checkout']) && isset($_SESSION['checkout']['coupon_code'])) {
    // Kupon bilgilerini session'dan kaldır
    unset($_SESSION['checkout']['coupon_code']);
    unset($_SESSION['checkout']['coupon_id']);
    unset($_SESSION['checkout']['discount']);
    
    set_flash_message('success', 'Uygulanan kupon kaldırıldı.');
}

// Kullanıcıyı ödeme sayfasına geri yönlendir
header('Location: /checkout');
exit;