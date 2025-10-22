<?php
/**
 * Firma Admini - Sefer Düzenleme (Mantık) - TAM VALIDASYONLU
 */

$auth = new Auth();
$db = Database::getInstance();

$auth->requireCompanyAdmin();
$companyAdmin = $auth->getCurrentUser();

$error = '';
$tripId = $_GET['id'] ?? null;

if (!$tripId) {
    header('Location: /company/trips');
    exit;
}

// Düzenlenecek seferi veritabanından çek
$trip = $db->getTripById($tripId);

// Güvenlik Kontrolü: Sefer yoksa VEYA sefer bu firmanın değilse, erişimi engelle.
if (!$trip || $trip['company_id'] !== $companyAdmin['company_id']) {
    set_flash_message('error', 'Geçersiz veya yetkiniz olmayan bir sefere erişmeye çalıştınız.');
    header('Location: /company/trips');
    exit;
}

// Formu doldurmak için mevcut verileri bir diziye ata
$inputs = [
    'departure_city' => $trip['departure_city'], 'destination_city' => $trip['destination_city'],
    'departure_date' => date('Y-m-d', strtotime($trip['departure_time'])),
    'departure_time_input' => date('H:i', strtotime($trip['departure_time'])),
    'arrival_date' => date('Y-m-d', strtotime($trip['arrival_time'])),
    'arrival_time_input' => date('H:i', strtotime($trip['arrival_time'])),
    'price' => $trip['price'], 'capacity' => $trip['capacity']
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    CSRF::validateRequest(); // CSRF kontrolünü form işleme BAŞINA alalım

    // Formdan gelen tüm verileri al ve temizle
    foreach ($inputs as $key => &$value) {
        // $_POST'tan gelen güncel veriyi al, yoksa mevcut değeri koru (formda görünmesi için)
        $postedValue = $_POST[$key] ?? $value;
        $value = Validator::sanitizeString($postedValue);
    }
    unset($value); // Referansı kaldır

    // Tarih ve saatleri birleştir
    $departureTime = date('Y-m-d H:i:s', strtotime($inputs['departure_date'] . ' ' . $inputs['departure_time_input']));
    $arrivalTime = date('Y-m-d H:i:s', strtotime($inputs['arrival_date'] . ' ' . $inputs['arrival_time_input']));

    // --- VALIDASYON BAŞLANGICI (add-trip.php ile aynı) ---
    $validator = new Validator();
    if (!$validator->required($inputs['departure_city'], 'Kalkış Şehri') ||
        !$validator->required($inputs['destination_city'], 'Varış Şehri') ||
        !$validator->required($inputs['departure_date'], 'Kalkış Tarihi') ||
        !$validator->required($inputs['departure_time_input'], 'Kalkış Saati') ||
        !$validator->required($inputs['arrival_date'], 'Varış Tarihi') ||
        !$validator->required($inputs['arrival_time_input'], 'Varış Saati') ||
        !$validator->required($inputs['price'], 'Fiyat') ||
        !$validator->required($inputs['capacity'], 'Kapasite')) {
        $error = 'Tüm zorunlu alanlar doldurulmalıdır.';
    } elseif ($inputs['departure_city'] === $inputs['destination_city']) {
        $error = 'Kalkış ve varış şehri aynı olamaz.';
    } elseif (!$validator->integer($inputs['price'], 'Fiyat', 0) || // Fiyat 0 olabilir mi? Min 0 kontrolü ekleyelim.
              !$validator->integer($inputs['capacity'], 'Kapasite', 1)) { // Kapasite min 1 olmalı.
        $error = 'Fiyat ve Kapasite alanları geçerli sayılar olmalıdır (Kapasite en az 1).';
    } elseif ($arrivalTime <= $departureTime) {
        $error = 'Varış zamanı, kalkış zamanından daha sonra olmalıdır.';
    } elseif (new DateTime($inputs['departure_date']) < new DateTime(date('Y-m-d'))) { // Sadece gün bazında geçmiş kontrolü
         // Düzenlemede geçmişe dönük düzenleme engellenebilir, ama belki sadece kalkış saati geçmişse engellemek daha mantıklı?
         // Şimdilik sadece tarihe bakalım:
        $error = 'Kalkış tarihi geçmiş bir tarih olamaz.';
    } elseif (new DateTime($inputs['departure_date']) > new DateTime('+1 year')) { // En fazla 1 yıl sonrası
        $error = 'Kalkış tarihi çok uzak bir gelecekte olamaz (En fazla 1 yıl).';
    } elseif (new DateTime($inputs['arrival_date']) > new DateTime('+1 year +1 day')) { // Varış da yaklaşık 1 yıl sınırı içinde olmalı
        $error = 'Varış tarihi çok uzak bir gelecekte olamaz.';
    }
    // --- VALIDASYON BİTTİ ---
    else {
        // Hata yoksa veritabanını güncelle
        $sql = "UPDATE Trips SET departure_city = ?, destination_city = ?, departure_time = ?,
                arrival_time = ?, price = ?, capacity = ? WHERE id = ? AND company_id = ?";
        $params = [
            $inputs['departure_city'], $inputs['destination_city'],
            $departureTime, $arrivalTime,
            (int)$inputs['price'], (int)$inputs['capacity'],
            $tripId, $companyAdmin['company_id'] // Güvenlik: Sadece kendi firmasının seferini güncelleyebilir
        ];

        if ($db->execute($sql, $params)) {
            set_flash_message('success', "Sefer başarıyla güncellendi.");
            header('Location: /company/trips');
            exit;
        } else {
            $error = 'Veritabanı güncellenirken bir hata oluştu.';
        }
    }
}


$pageTitle = 'Seferi Düzenle - Firma Paneli';
require_once INCLUDES_PATH . '/header.php';
require_once VIEWS_PATH . '/company/edit-trip.view.php';
require_once INCLUDES_PATH . '/footer.php';