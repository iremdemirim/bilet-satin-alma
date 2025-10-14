<?php
/**
 * Sefer Detayları ve Koltuk Seçimi (Mantık)
 */

$auth = new Auth();
$db = Database::getInstance();

$tripId = $_GET['id'] ?? null;

if (!$tripId) {
    // ID yoksa 404 hatası ver
    http_response_code(404);
    require_once ROOT_PATH . '/pages/errors/404.php';
    exit;
}

// Veritabanından sefer bilgilerini çek
$trip = $db->getTripById($tripId);

// Sefer bulunamazsa 404 hatası ver
if (!$trip) {
    http_response_code(404);
    require_once ROOT_PATH . '/pages/errors/404.php';
    exit;
}

// Bu sefere ait dolu koltukların listesini çek
$bookedSeats = $db->getBookedSeats($tripId);

$pageTitle = "{$trip['departure_city']} - {$trip['destination_city']} Seferi";
require_once INCLUDES_PATH . '/header.php';
require_once VIEWS_PATH . '/trip-details.view.php';
require_once INCLUDES_PATH . '/footer.php';