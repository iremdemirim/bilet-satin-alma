<?php
/**
 * Ödeme İşleme Mantığı (Kupon Entegrasyonlu)
 */

$auth = new Auth();
$db = Database::getInstance();

$auth->requireUser();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /');
    exit;
}

CSRF::validateRequest();

// Sepet bilgilerini session'dan al
if (!isset($_SESSION['checkout'])) {
    header('Location: /');
    exit;
}

$checkoutData = $_SESSION['checkout'];
$currentUser = $auth->getCurrentUser();
$tripId = $checkoutData['trip_id'];
$selectedSeats = $checkoutData['seats'];
$trip = $db->getTripById($tripId);

// Fiyatları yeniden hesapla (güvenlik için)
$basePrice = count($selectedSeats) * $trip['price'];
$totalPrice = $basePrice - ($checkoutData['discount'] ?? 0);

// Bakiye kontrolü
if ($currentUser['balance'] < $totalPrice) {
    set_flash_message('error', 'Yetersiz bakiye.');
    header('Location: /checkout');
    exit;
}

// --- VERİTABANI TRANSACTION BAŞLANGICI ---
$db->beginTransaction();

try {
    // ... (Koltuk kontrolü, bakiye düşürme, bilet oluşturma... öncekiyle aynı)
    $bookedSeats = $db->getBookedSeats($tripId);
    foreach ($selectedSeats as $seat) {
        if (in_array($seat, $bookedSeats)) {
            throw new Exception("Koltuk {$seat} başkası tarafından alındı.");
        }
    }

    $newBalance = $currentUser['balance'] - $totalPrice;
    $db->updateUserBalance($currentUser['id'], $newBalance);

    $ticketId = $db->generateUUID();
    $sqlTicket = "INSERT INTO Tickets (id, trip_id, user_id, total_price) VALUES (?, ?, ?, ?)";
    $db->execute($sqlTicket, [$ticketId, $tripId, $currentUser['id'], $totalPrice]);

    $sqlSeat = "INSERT INTO Booked_Seats (id, ticket_id, seat_number) VALUES (?, ?, ?)";
    foreach ($selectedSeats as $seat) {
        $seatId = $db->generateUUID();
        $db->execute($sqlSeat, [$seatId, $ticketId, $seat]);
    }

    // --- YENİ EKLENEN KUPON İŞLEME KISMI ---
    if (!empty($checkoutData['coupon_id'])) {
        // 1. Kuponun kullanım sayısını 1 artır
        $db->incrementCouponUsage($checkoutData['coupon_id']);
        
        // 2. Hangi kullanıcının hangi bilettte kupon kullandığını kaydet
        $userCouponId = $db->generateUUID();
        $sqlUserCoupon = "INSERT INTO User_Coupons (id, coupon_id, user_id, ticket_id) VALUES (?, ?, ?, ?)";
        $db->execute($sqlUserCoupon, [$userCouponId, $checkoutData['coupon_id'], $currentUser['id'], $ticketId]);
    }

    $db->commit();
    $auth->updateSessionBalance($newBalance);

    // Başarılı işlem sonrası sepeti temizle
    unset($_SESSION['checkout']);

    set_flash_message('success', 'Biletiniz başarıyla oluşturuldu!');
    header('Location: /dashboard');
    exit;

} catch (Exception $e) {
    $db->rollback();
    set_flash_message('error', 'Bilet alımı sırasında bir hata oluştu: ' . $e->getMessage());
    header('Location: /checkout');
    exit;
}