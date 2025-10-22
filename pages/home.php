<?php
/**
 * Ana Sayfa (Sefer Arama ve Listeleme) - Geliştirilmiş Validasyon
 */

$db = Database::getInstance();
$auth = new Auth(); 

$cities = $db->getAllCities();

// Form değişkenleri ve hata mesajı
$trips = [];
$error = ''; 
$departureCity = Validator::sanitizeString($_GET['departure_city'] ?? '');
$destinationCity = Validator::sanitizeString($_GET['destination_city'] ?? '');
$departureDate = Validator::sanitizeString($_GET['departure_date'] ?? '');
$isSearchPerformed = false;

if (!empty($departureCity) && !empty($destinationCity)) {

    if ($departureCity === $destinationCity) {
        $error = 'Kalkış ve varış şehri aynı olamaz. Lütfen farklı şehirler seçin.';
    }
    else {
        // Şehirler farklı, arama yap
        $isSearchPerformed = true;
        $trips = $db->searchTrips($departureCity, $destinationCity, $departureDate);
    }
} elseif (isset($_GET['departure_city'])) { 
     if (empty($departureCity)) $error = 'Lütfen kalkış şehrini seçin.';
     elseif (empty($destinationCity)) $error = 'Lütfen varış şehrini seçin.';
}


$pageTitle = 'Ana Sayfa - ' . SITE_NAME;
require_once INCLUDES_PATH . '/header.php';
require_once VIEWS_PATH . '/home.view.php';
require_once INCLUDES_PATH . '/footer.php';