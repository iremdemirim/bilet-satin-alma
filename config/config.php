<?php
/**
 * Site Configuration
 * Genel site ayarları ve sabitler
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Projeyi canlıya alacağın zaman bunu 'production' olarak değiştir.
define('ENVIRONMENT', 'development');

if (ENVIRONMENT === 'development') {
    // Geliştirme ortamında tüm hataları göster
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    // Canlı ortamda hataları gösterme, dosyaya logla
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    // Projenin ana dizininde 'logs' adında bir klasör oluşturmalısın
    ini_set('error_log', dirname(__DIR__) . '/logs/php_errors.log'); 
}
// --- BİTTİ ---

define('SITE_NAME', 'Bilet Satın Alma Platformu');
define('SITE_URL', 'http://localhost:8000');

// Klasör yolları
define('ROOT_PATH', dirname(__DIR__));
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('INCLUDES_PATH', ROOT_PATH . '/includes');
define('CLASSES_PATH', ROOT_PATH . '/classes');
define('PAGES_PATH', ROOT_PATH . '/pages');
define('VIEWS_PATH', ROOT_PATH . '/views');

// Kullanıcı rolleri
define('ROLE_USER', 'user');
define('ROLE_COMPANY', 'company');
define('ROLE_ADMIN', 'admin');

// Bilet durumları
define('TICKET_ACTIVE', 'active');
define('TICKET_CANCELED', 'canceled');
define('TICKET_EXPIRED', 'expired');

// Sefer durumları
define('TRIP_ACTIVE', 'active');
define('TRIP_CANCELED', 'canceled');

// Varsayılan bakiye
define('DEFAULT_BALANCE', 800);

// Bilet iptal süresi (saat cinsinden)
define('CANCEL_TIME_LIMIT', 1);

// Tarih formatları
define('DATE_FORMAT', 'd.m.Y');
define('TIME_FORMAT', 'H:i');
define('DATETIME_FORMAT', 'd.m.Y H:i');

require_once __DIR__ . '/database.php';

spl_autoload_register(function ($class) {
    $file = CLASSES_PATH . '/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});