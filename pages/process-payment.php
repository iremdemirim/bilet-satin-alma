<?php
/**
 * Ödeme İşleme Mantığı (Kupon Entegrasyonlu)
 */

$auth = new Auth();
$db = Database::getInstance();

// Bu işlemi sadece giriş yapmış kullanıcılar yapabilir
$auth->requireUser();

// Sadece POST metoduyla gelen istekleri kabul et
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /'); // Ana sayfaya yönlendir
    exit;
}

// CSRF token'ını doğrula
CSRF::validateRequest();

// Sepet bilgilerini session'dan güvenli bir şekilde al
if (!isset($_SESSION['checkout'])) {
    set_flash_message('error', 'Oturum süreniz dolmuş veya sepet bilgisi bulunamadı. Lütfen işlemi tekrarlayın.');
    header('Location: /'); // Ana sayfaya yönlendir
    exit;
}

$checkoutData = $_SESSION['checkout'];
$currentUser = $auth->getCurrentUser();
$tripId = $checkoutData['trip_id'];
$selectedSeats = $checkoutData['seats'];

// Sefer bilgilerini veritabanından çek
$trip = $db->getTripById($tripId);

// Sefer bilgileri geçersizse veya koltuk seçilmemişse işlemi durdur
if (!$trip || empty($selectedSeats)) {
    unset($_SESSION['checkout']); // Hatalı sepet bilgisini temizle
    set_flash_message('error', 'Geçersiz sefer veya koltuk bilgisi.');
    header('Location: /'); // Ana sayfaya yönlendir
    exit;
}

// --- FİYAT HESAPLAMA (GÜVENLİK İÇİN SUNUCUDA TEKRAR YAPILIR) ---
$basePrice = count($selectedSeats) * $trip['price']; // Temel Fiyat = Koltuk Sayısı * Bilet Fiyatı
// İndirimli Toplam Fiyat = Temel Fiyat - Session'daki İndirim Miktarı (yoksa 0)
$totalPrice = $basePrice - ($checkoutData['discount'] ?? 0);
// --- FİYAT HESAPLAMA BİTTİ ---


// --- BAKİYE KONTROLÜ (İNDİRİMLİ FİYATA GÖRE) ---
if ($currentUser['balance'] < $totalPrice) {
    set_flash_message('error', 'Yetersiz bakiye.');
    header('Location: /checkout'); // Ödeme sayfasına geri yönlendir
    exit;
}
// --- BAKİYE KONTROLÜ BİTTİ ---


// --- VERİTABANI İŞLEMLERİ (TRANSACTION İLE GÜVENLİ) ---
$db->beginTransaction(); // İşlemleri başlat

try {
    // 1. GÜVENLİK KONTROLÜ: Koltuklar hala boş mu? (Race Condition Önlemi)
    $bookedSeats = $db->getBookedSeats($tripId);
    foreach ($selectedSeats as $seat) {
        if (in_array($seat, $bookedSeats)) {
            // Eğer koltuklardan biri bu arada satılmışsa, hata ver ve işlemi geri al
            throw new Exception("Koltuk {$seat} başkası tarafından alındı. Lütfen farklı bir koltuk seçin.");
        }
    }

    // 2. KULLANICI BAKİYESİNİ DÜŞÜR (İNDİRİMLİ FİYATI KULLANARAK)
    $newBalance = $currentUser['balance'] - $totalPrice;
    if (!$db->updateUserBalance($currentUser['id'], $newBalance)) {
        throw new Exception("Bakiye güncellenirken bir veritabanı hatası oluştu.");
    }

    // 3. YENİ BİLET KAYDI OLUŞTUR (İNDİRİMLİ FİYATI KAYDET)
    $ticketId = $db->generateUUID();
    $sqlTicket = "INSERT INTO Tickets (id, trip_id, user_id, total_price) VALUES (?, ?, ?, ?)";
    if (!$db->execute($sqlTicket, [$ticketId, $tripId, $currentUser['id'], $totalPrice])) {
        throw new Exception("Bilet kaydı oluşturulurken bir veritabanı hatası oluştu.");
    }

    // 4. SEÇİLEN KOLTUKLARI KAYDET
    $sqlSeat = "INSERT INTO Booked_Seats (id, ticket_id, seat_number) VALUES (?, ?, ?)";
    foreach ($selectedSeats as $seat) {
        $seatId = $db->generateUUID();
        if (!$db->execute($sqlSeat, [$seatId, $ticketId, $seat])) {
            throw new Exception("Seçilen koltuklar kaydedilirken bir veritabanı hatası oluştu.");
        }
    }

    // 5. KUPON KULLANILDIYSA İŞLE
    if (!empty($checkoutData['coupon_id'])) {
        // a. Kuponun kullanım sayısını 1 artır
        if (!$db->incrementCouponUsage($checkoutData['coupon_id'])) {
             throw new Exception("Kupon kullanım sayısı güncellenirken bir hata oluştu.");
        }

        // b. Hangi kullanıcının hangi bilettte kupon kullandığını kaydet
        $userCouponId = $db->generateUUID();
        $sqlUserCoupon = "INSERT INTO User_Coupons (id, coupon_id, user_id, ticket_id) VALUES (?, ?, ?, ?)";
        if (!$db->execute($sqlUserCoupon, [$userCouponId, $checkoutData['coupon_id'], $currentUser['id'], $ticketId])) {
             throw new Exception("Kupon kullanım bilgisi kaydedilirken bir hata oluştu.");
        }
    }

    // 6. TÜM İŞLEMLER BAŞARILI: Değişiklikleri onayla
    $db->commit();

    // 7. Session'daki kullanıcı bakiyesini de anında güncelle
    $auth->updateSessionBalance($newBalance);

    // 8. Başarılı işlem sonrası geçici sepeti (session) temizle
    unset($_SESSION['checkout']);

    // Başarı mesajı göster ve kullanıcıyı biletlerim sayfasına yönlendir
    set_flash_message('success', 'Biletiniz başarıyla oluşturuldu! İyi yolculuklar dileriz.');
    header('Location: /dashboard');
    exit;

} catch (Exception $e) {
    // HATA DURUMU: Tüm veritabanı değişikliklerini geri al
    $db->rollback();

    // Hata mesajını göster ve kullanıcıyı ödeme sayfasına geri yönlendir
    set_flash_message('error', 'Bilet alımı sırasında bir hata oluştu: ' . $e->getMessage());
    header('Location: /checkout');
    exit;
}