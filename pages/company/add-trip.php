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
    } elseif (!$validator->integer($inputs['price'], 'Fiyat') || !$validator->integer($inputs['capacity'], 'Kapasite')) {
        $error = 'Fiyat ve Kapasite alanları sayısal bir değer olmalıdır.';
    } elseif ($arrivalTime <= $departureTime) {
        $error = 'Varış zamanı, kalkış zamanından daha sonra olmalıdır.';
    } elseif (new DateTime($departureTime) < new DateTime()) {
        $error = 'Kalkış zamanı geçmiş bir tarih olamaz.';
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