<?php
/**
 * Firma Admini - Yeni Sefer Ekleme (Mantık)
 */

$auth = new Auth();
$db = Database::getInstance();

$auth->requireCompanyAdmin();

$companyAdmin = $auth->getCurrentUser();

// Form değişkenlerini başlat
$error = '';
$inputs = [
    'departure_city' => '', 'destination_city' => '',
    'departure_date' => '', 'departure_time_input' => '',
    'arrival_date' => '', 'arrival_time_input' => '',
    'price' => '', 'capacity' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    CSRF::validateRequest();
    
    // Formdan gelen tüm verileri al ve temizle
    foreach ($inputs as $key => &$value) {
        $value = Validator::sanitizeString($_POST[$key] ?? '');
    }
    unset($value); // Referansı kaldır

    // Tarih ve saatleri birleştir
    $departureTime = date('Y-m-d H:i:s', strtotime($inputs['departure_date'] . ' ' . $inputs['departure_time_input']));
    $arrivalTime = date('Y-m-d H:i:s', strtotime($inputs['arrival_date'] . ' ' . $inputs['arrival_time_input']));

    // Validasyon
    $validator = new Validator();
    if (!$validator->required($inputs['departure_city'], 'Kalkış Şehri') ||
        // ... (diğer required kontrolleri aynı) ...
        !$validator->required($inputs['capacity'], 'Kapasite')) {
        $error = 'Tüm zorunlu alanlar doldurulmalıdır.';
    } elseif ($inputs['departure_city'] === $inputs['destination_city']) {
        $error = 'Kalkış ve varış şehri aynı olamaz.';
    } elseif (!$validator->integer($inputs['price'], 'Fiyat') || !$validator->integer($inputs['capacity'], 'Kapasite')) {
        $error = 'Fiyat ve Kapasite alanları sayısal bir değer olmalıdır.';
    } elseif ($arrivalTime <= $departureTime) {
        $error = 'Varış zamanı, kalkış zamanından daha sonra olmalıdır.';
    } elseif (new DateTime($departureTime) < new DateTime(date('Y-m-d'))) { // Sadece gün bazında geçmiş kontrolü
        $error = 'Kalkış tarihi geçmiş bir tarih olamaz.';

    // --- YENİ GELECEK TARİH SINIRI KONTROLÜ ---
    } elseif (new DateTime($inputs['departure_date']) > new DateTime('+1 year')) { // En fazla 1 yıl sonrası
        $error = 'Kalkış tarihi çok uzak bir gelecekte olamaz (En fazla 1 yıl).';
    } elseif (new DateTime($inputs['arrival_date']) > new DateTime('+1 year +1 day')) { // Varış da yaklaşık 1 yıl sınırı içinde olmalı
        $error = 'Varış tarihi çok uzak bir gelecekte olamaz.';
    // --- KONTROL BİTTİ ---

    } else {
        // Veritabanına ekle
        $tripId = $db->generateUUID();
        $sql = "INSERT INTO Trips (id, company_id, departure_city, destination_city, departure_time, arrival_time, price, capacity) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $params = [
            $tripId,
            $companyAdmin['company_id'],
            $inputs['departure_city'],
            $inputs['destination_city'],
            $departureTime,
            $arrivalTime,
            (int)$inputs['price'],
            (int)$inputs['capacity']
        ];

        if ($db->execute($sql, $params)) {
            set_flash_message('success', "{$inputs['departure_city']} - {$inputs['destination_city']} seferi başarıyla eklendi.");
            header('Location: /company/trips');
            exit;
        } else {
            $error = 'Veritabanına kayıt sırasında bir hata oluştu.';
        }
    }
}

$pageTitle = 'Yeni Sefer Ekle - Firma Paneli';
require_once INCLUDES_PATH . '/header.php';
require_once VIEWS_PATH . '/company/add-trip.view.php';
require_once INCLUDES_PATH . '/footer.php';