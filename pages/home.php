<?php
/**
 * Ana Sayfa (Sefer Arama ve Listeleme)
 */

$db = Database::getInstance();
$auth = new Auth(); 
$cities = $db->getAllCities();

// Formdan gelen verileri tutacak değişkenleri başlat
$trips = [];
$departureCity = Validator::sanitizeString($_GET['departure_city'] ?? '');
$destinationCity = Validator::sanitizeString($_GET['destination_city'] ?? '');
$departureDate = Validator::sanitizeString($_GET['departure_date'] ?? '');

// Bir arama yapılıp yapılmadığını anlamak için bir bayrak
$isSearchPerformed = false;

// Eğer form GET metoduyla gönderildiyse (URL'de parametreler varsa)
if (!empty($departureCity) && !empty($destinationCity)) {
    $isSearchPerformed = true;
    $trips = $db->searchTrips($departureCity, $destinationCity, $departureDate);
}

// Sayfa başlığını ayarla ve view dosyalarını dahil et
$pageTitle = 'Ana Sayfa - ' . SITE_NAME;
require_once INCLUDES_PATH . '/header.php';
require_once VIEWS_PATH . '/home.view.php';
require_once INCLUDES_PATH . '/footer.php';

