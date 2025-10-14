<?php
/**
 * Database Configuration
 * SQLite veritabanı bağlantı ayarları
 */

define('DB_PATH', __DIR__ . '/../data/bilet_sistemi.db');

function getDBConnection() {
    try {
        $pdo = new PDO('sqlite:' . DB_PATH);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec('PRAGMA foreign_keys = ON');
        $pdo->setAttribute(PDO::FETCH_ASSOC, true);
        return $pdo;
        
    } catch (PDOException $e) {
        // Hata detayını dosyaya logla
        error_log("Veritabanı bağlantı hatası: " . $e->getMessage());
        
        // Geliştirme modundaysa hata zaten görünecek, canlı moddaysa gizlenecek.
        if (ENVIRONMENT !== 'development') {
            die("Sistemde bir sorun oluştu. Lütfen daha sonra tekrar deneyin.");
        } else {
            die("Veritabanı bağlantı hatası: " . $e->getMessage());
        }
    }
}
