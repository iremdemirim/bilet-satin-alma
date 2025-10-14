<?php
/**
 * Front Controller
 * Tüm web istekleri için tek giriş noktası.
 */

// Session'ı başlat 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ana yapılandırma dosyasını yükle
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../helpers/functions.php';
require_once __DIR__ . '/../router.php';