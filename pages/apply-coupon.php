<?php
/**
 * Kupon Uygulama Mantığı
 */

$auth = new Auth();
$db = Database::getInstance();

$auth->requireUser();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /checkout');
    exit;
}

CSRF::validateRequest();

$couponCode = strtoupper(trim($_POST['coupon_code'] ?? ''));

// Session'da checkout bilgisi var mı kontrol et
if (!isset($_SESSION['checkout'])) {
    header('Location: /');
    exit;
}

$checkoutData = $_SESSION['checkout'];
$trip = $db->getTripById($checkoutData['trip_id']);
$basePrice = count($checkoutData['seats']) * $trip['price'];

if (empty($couponCode)) {
    set_flash_message('error', 'Lütfen bir kupon kodu girin.');
    header('Location: /checkout');
    exit;
}

$coupon = $db->getCouponByCode($couponCode);

// --- Kupon Doğrulama Mantığı ---
// Görev dökümanındaki tüm kuralları burada kontrol ediyoruz.
if (!$coupon) {
    set_flash_message('error', 'Geçersiz kupon kodu.');
} elseif (new DateTime() > new DateTime($coupon['expire_date'])) {
    set_flash_message('error', 'Bu kuponun süresi dolmuş.');
} elseif ($coupon['used_count'] >= $coupon['usage_limit']) {
    set_flash_message('error', 'Bu kupon kullanım limitine ulaşmış.');
} elseif ($coupon['company_id'] !== null && $coupon['company_id'] !== $trip['company_id']) {
    // Kupon belirli bir firmaya aitse ve bu sefer o firmaya ait değilse hata ver.
    set_flash_message('error', 'Bu kupon, bu firma için geçerli değildir.');
} else {
    // Kupon geçerli, indirimi hesapla ve session'a kaydet
    $discountAmount = $basePrice * ($coupon['discount'] / 100);
    
    $_SESSION['checkout']['coupon_code'] = $coupon['code'];
    $_SESSION['checkout']['coupon_id'] = $coupon['id']; // Ödeme onayı için kupon ID'sini sakla
    $_SESSION['checkout']['discount'] = $discountAmount;
    
    set_flash_message('success', 'Kupon başarıyla uygulandı!');
}

header('Location: /checkout');
exit;