<?php
/**
 * Firma Admini - Sefer Düzenleme (Mantık)
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

//Sefer yoksa VEYA sefer bu firmanın değilse, erişimi engelle.
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
    foreach ($inputs as $key => &$value) {
        $value = Validator::sanitizeString($_POST[$key] ?? '');
    }
    unset($value);

    $departureTime = date('Y-m-d H:i:s', strtotime($inputs['departure_date'] . ' ' . $inputs['departure_time_input']));
    $arrivalTime = date('Y-m-d H:i:s', strtotime($inputs['arrival_date'] . ' ' . $inputs['arrival_time_input']));

    $validator = new Validator();
    if ($arrivalTime <= $departureTime) {
        $error = 'Varış zamanı, kalkış zamanından daha sonra olmalıdır.';
    } else {
        $sql = "UPDATE Trips SET departure_city = ?, destination_city = ?, departure_time = ?, 
                arrival_time = ?, price = ?, capacity = ? WHERE id = ? AND company_id = ?";
        $params = [
            $inputs['departure_city'], $inputs['destination_city'],
            $departureTime, $arrivalTime,
            (int)$inputs['price'], (int)$inputs['capacity'],
            $tripId, $companyAdmin['company_id']
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