<?php
/**
 * CSRF Protection Class
 * Cross-Site Request Forgery saldırılarına karşı koruma
 */

class CSRF {
    
    /**
     * CSRF token oluştur ve session'a kaydet
     */
    public static function generateToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * CSRF token'ı doğrula
     */
    public static function validateToken($token) {
        if (!isset($_SESSION['csrf_token'])) {
            return false;
        }
        
        // Timing attack'a karşı hash_equals kullan
        return hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * HTML input field olarak token döndür
     */
    public static function getTokenField() {
        $token = self::generateToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }
    
    /**
     * POST isteğindeki token'ı doğrula, geçersizse exception fırlat
     */
    public static function validateRequest() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? '';
            
            if (!self::validateToken($token)) {
                http_response_code(403);
                die('CSRF token doğrulaması başarısız. Güvenlik nedeniyle işlem engellendi.');
            }
        }
    }
    
    /**
     * Token'ı yenile (önemli işlemlerden sonra)
     */
    public static function regenerateToken() {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        return $_SESSION['csrf_token'];
    }
}