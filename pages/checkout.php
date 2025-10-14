<?php
/**
 * Ödeme (Checkout) Sayfası - Mantık
 */

$auth = new Auth();
$db = Database::getInstance();

// Bu sayfayı sadece giriş yapmış kullanıcılar görebilir
$auth->requireUser();

// Eğer sayfa POST isteği ile geldiyse (koltuk seçiminden)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    CSRF::validateRequest();
    
    $tripId = $_POST['trip_id'] ?? null;
    $selectedSeatsStr = $_POST['selected_seats'] ?? '';

    if (!$tripId || empty($selectedSeatsStr)) {
        header('Location: /');
        exit;
    }

    // Gelen bilgileri session'a kaydet. Bu bizim geçici sepetimiz.
    $_SESSION['checkout'] = [
        'trip_id' => $tripId,
        'seats' => explode(',', $selectedSeatsStr),
        'coupon_code' => null,
        'discount' => 0
    ];
}

// Eğer session'da checkout bilgisi yoksa (sayfa doğrudan açıldıysa), ana sayfaya yönlendir.
if (!isset($_SESSION['checkout'])) {
    header('Location: /');
    exit;
}

$checkoutData = $_SESSION['checkout'];
$trip = $db->getTripById($checkoutData['trip_id']);

if (!$trip) {
    // Session'daki geçersiz veriyi temizle
    unset($_SESSION['checkout']);
    set_flash_message('error', 'Sefer bilgileri bulunamadı.');
    header('Location: /');
    exit;
}

// Fiyatları hesapla
$basePrice = count($checkoutData['seats']) * $trip['price'];
$totalPrice = $basePrice - $checkoutData['discount'];

$pageTitle = 'Ödeme - ' . SITE_NAME;
require_once INCLUDES_PATH . '/header.php';
require_once VIEWS_PATH . '/checkout.view.php';
require_once INCLUDES_PATH . '/footer.php';