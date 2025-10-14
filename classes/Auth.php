<?php
/**
 * Auth Class
 * Güvenli kimlik doğrulama ve yetkilendirme
 * - Session hijacking koruması
 * - Password hashing (bcrypt)
 * - CSRF koruması
 * - Rate limiting
 */

class Auth {
    private $db;
    private const MAX_LOGIN_ATTEMPTS = 5;
    private const LOGIN_TIMEOUT = 900; // 15 dakika
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->initSession();
    }
    
    /**
     * Güvenli session başlatma
     */
    private function initSession() {
        if (session_status() === PHP_SESSION_NONE) {
            // Session güvenlik ayarları
            ini_set('session.cookie_httponly', 1);
            ini_set('session.use_only_cookies', 1);
            ini_set('session.cookie_secure', 0); 
            ini_set('session.cookie_samesite', 'Strict');
            
            session_start();
            
            // Session fixation saldırısına karşı
            if (!isset($_SESSION['initiated'])) {
                session_regenerate_id(true);
                $_SESSION['initiated'] = true;
            }
            
            // Session hijacking kontrolü
            $this->validateSession();
        }
    }
    
    /**
     * Session hijacking kontrolü (IP ve User-Agent)
     */
    private function validateSession() {
        if ($this->isLoggedIn()) {
            $currentFingerprint = $this->generateFingerprint();
            
            if (!isset($_SESSION['fingerprint'])) {
                $_SESSION['fingerprint'] = $currentFingerprint;
            } elseif ($_SESSION['fingerprint'] !== $currentFingerprint) {
                // Fingerprint değişmiş, session'ı sonlandır
                $this->logout();
                http_response_code(403);
                die('Session güvenlik hatası. Lütfen tekrar giriş yapın.');
            }
        }
    }
    
    /**
     * Browser fingerprint oluştur
     */
    private function generateFingerprint() {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        return hash('sha256', $ip . $userAgent);
    }
    
    /**
     * Rate limiting kontrolü
     */
    private function checkRateLimit($email) {
        $key = 'login_attempts_' . md5($email);
        
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = ['count' => 0, 'time' => time()];
        }
        
        $attempts = $_SESSION[$key];
        
        // Zaman aşımı kontrolü
        if (time() - $attempts['time'] > self::LOGIN_TIMEOUT) {
            $_SESSION[$key] = ['count' => 0, 'time' => time()];
            return true;
        }
        
        // Deneme sayısı kontrolü
        if ($attempts['count'] >= self::MAX_LOGIN_ATTEMPTS) {
            $remainingTime = self::LOGIN_TIMEOUT - (time() - $attempts['time']);
            $minutes = ceil($remainingTime / 60);
            return ['blocked' => true, 'minutes' => $minutes];
        }
        
        return true;
    }
    
    /**
     * Başarısız giriş denemesini kaydet
     */
    private function recordFailedAttempt($email) {
        $key = 'login_attempts_' . md5($email);
        
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = ['count' => 0, 'time' => time()];
        }
        
        $_SESSION[$key]['count']++;
    }
    
    /**
     * Başarılı girişte denemeleri sıfırla
     */
    private function clearLoginAttempts($email) {
        $key = 'login_attempts_' . md5($email);
        unset($_SESSION[$key]);
    }
    
    /**
     * Kullanıcı kaydı oluştur
     */
    public function register($fullName, $email, $password, $role = ROLE_USER, $companyId = null) {
        // Input sanitization
        $fullName = Validator::sanitizeString($fullName);
        $email = Validator::sanitizeEmail($email);
        
        // Validation
        $validator = new Validator();
        
        if (!$validator->required($fullName, 'Ad Soyad') ||
            !$validator->minLength($fullName, 3, 'Ad Soyad') ||
            !$validator->maxLength($fullName, 100, 'Ad Soyad')) {
            return ['success' => false, 'message' => $validator->getFirstError()];
        }
        
        if (!$validator->required($email, 'Email') ||
            !$validator->email($email, 'Email')) {
            return ['success' => false, 'message' => $validator->getFirstError()];
        }
        
        if (!$validator->required($password, 'Şifre') ||
            !$validator->minLength($password, 6, 'Şifre')) {
            return ['success' => false, 'message' => $validator->getFirstError()];
        }
        
        // Email kontrolü
        if ($this->db->getUserByEmail($email)) {
            return ['success' => false, 'message' => 'Bu email adresi zaten kullanılıyor.'];
        }
        
        // Rol kontrolü
        $allowedRoles = [ROLE_USER, ROLE_COMPANY, ROLE_ADMIN];
        if (!in_array($role, $allowedRoles, true)) {
            return ['success' => false, 'message' => 'Geçersiz rol.'];
        }
        
        // Şifreyi hashle (bcrypt, cost=12)
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        
        // UUID oluştur
        $userId = $this->db->generateUUID();
        
        // Kullanıcıyı ekle
        $sql = "INSERT INTO User (id, full_name, email, role, password, company_id, balance, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)";
        
        $balance = ($role === ROLE_USER) ? DEFAULT_BALANCE : 5000;
        
        $params = [$userId, $fullName, $email, $role, $hashedPassword, $companyId, $balance];
        
        if ($this->db->execute($sql, $params)) {
            return ['success' => true, 'message' => 'Kayıt başarılı!', 'user_id' => $userId];
        }
        
        return ['success' => false, 'message' => 'Kayıt sırasında bir hata oluştu.'];
    }
    
    /**
     * Kullanıcı girişi
     */
    public function login($email, $password) {
        // Input sanitization
        $email = Validator::sanitizeEmail($email);
        
        // Rate limiting kontrolü
        $rateLimit = $this->checkRateLimit($email);
        if (is_array($rateLimit) && isset($rateLimit['blocked'])) {
            return [
                'success' => false, 
                'message' => "Çok fazla başarısız deneme. {$rateLimit['minutes']} dakika sonra tekrar deneyin."
            ];
        }
        
        // Kullanıcıyı bul
        $user = $this->db->getUserByEmail($email);
        
        if (!$user) {
            $this->recordFailedAttempt($email);
            return ['success' => false, 'message' => 'Email veya şifre hatalı.'];
        }
        
        // Şifreyi doğrula
        if (!password_verify($password, $user['password'])) {
            $this->recordFailedAttempt($email);
            return ['success' => false, 'message' => 'Email veya şifre hatalı.'];
        }
        
        // Şifre hash'ini güncelle (cost değişmişse)
        if (password_needs_rehash($user['password'], PASSWORD_BCRYPT, ['cost' => 12])) {
            $newHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
            $sql = "UPDATE User SET password = ? WHERE id = ?";
            $this->db->execute($sql, [$newHash, $user['id']]);
        }
        
        // Başarılı giriş - denemeleri temizle
        $this->clearLoginAttempts($email);
        
        // Session'ı yenile (session fixation koruması)
        session_regenerate_id(true);
        
        // Session'a kullanıcı bilgilerini kaydet
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_company_id'] = $user['company_id'];
        $_SESSION['user_balance'] = $user['balance'];
        $_SESSION['logged_in'] = true;
        $_SESSION['login_time'] = time();
        $_SESSION['fingerprint'] = $this->generateFingerprint();
        
        // CSRF token oluştur
        CSRF::generateToken();
        
        return ['success' => true, 'message' => 'Giriş başarılı!', 'user' => $user];
    }
    
    /**
     * Kullanıcı çıkışı
     */
    public function logout() {
        // Session verilerini temizle
        $_SESSION = [];
        
        // Session cookie'sini sil
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        
        // Session'ı yok et
        session_destroy();
        
        return ['success' => true, 'message' => 'Çıkış yapıldı.'];
    }
    
    /**
     * Kullanıcı giriş yapmış mı kontrol et
     */
    public function isLoggedIn() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }
    
    /**
     * Giriş yapmamışsa login sayfasına yönlendir
     */
    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            header('Location: /auth/login');
            exit;
        }
    }
    
    /**
     * Giriş yapmışsa dashboard'a yönlendir
     */
    public function requireGuest() {
        if ($this->isLoggedIn()) {
            $this->redirectToDashboard();
        }
    }
    
    /**
     * Kullanıcının rolünü kontrol et
     */
    public function hasRole($role) {
        return $this->isLoggedIn() && $_SESSION['user_role'] === $role;
    }
    
    /**
     * Belirtilen rollerden birine sahip mi kontrol et
     */
    public function hasAnyRole($roles) {
        if (!$this->isLoggedIn()) {
            return false;
        }
        return in_array($_SESSION['user_role'], $roles, true);
    }
    
    /**
     * Admin yetkisi kontrolü
     */
    public function requireAdmin() {
        $this->requireLogin();
        if (!$this->hasRole(ROLE_ADMIN)) {
            http_response_code(403);
            die('Bu sayfaya erişim yetkiniz yok.');
        }
    }
    
    /**
     * Firma Admin yetkisi kontrolü
     */
    public function requireCompanyAdmin() {
        $this->requireLogin();
        if (!$this->hasRole(ROLE_COMPANY)) {
            http_response_code(403);
            die('Bu sayfaya erişim yetkiniz yok.');
        }
    }
    
    /**
     * User yetkisi kontrolü
     */
    public function requireUser() {
        $this->requireLogin();
        if (!$this->hasRole(ROLE_USER)) {
            http_response_code(403);
            die('Bu sayfaya erişim yetkiniz yok.');
        }
    }
    
    /**
     * Rolüne göre dashboard'a yönlendir
     */
    public function redirectToDashboard() {
        if (!$this->isLoggedIn()) {
            header('Location: /login');
            exit;
        }
        
        $role = $_SESSION['user_role'];
        
        switch ($role) {
            case ROLE_ADMIN:
                header('Location: /admin/dashboard');
                break;
            // --- YENİ EKLENEN SATIR ---
            case ROLE_COMPANY:
                header('Location: /company/dashboard');
                break;
            // --- YENİ EKLENEN SATIR BİTTİ ---
            case ROLE_USER:
                // Normal kullanıcılar için şimdilik ana sayfaya gitsin
                header('Location: /dashboard');
                break;
            default:
                header('Location: /');
        }
        exit;
    }
    
    /**
     * Mevcut kullanıcı bilgilerini al
     */
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        return [
            'id' => $_SESSION['user_id'],
            'email' => $_SESSION['user_email'],
            'name' => $_SESSION['user_name'],
            'role' => $_SESSION['user_role'],
            'company_id' => $_SESSION['user_company_id'] ?? null,
            'balance' => $_SESSION['user_balance'] ?? 0
        ];
    }
    
    /**
     * Kullanıcı bakiyesini güncelle (session'da)
     */
    public function updateSessionBalance($newBalance) {
        if ($this->isLoggedIn()) {
            $_SESSION['user_balance'] = $newBalance;
        }
    }
    
    /**
     * Session timeout kontrolü (30 dakika)
     */
    public function checkSessionTimeout($timeout = 1800) {
        if ($this->isLoggedIn()) {
            if (isset($_SESSION['login_time'])) {
                if (time() - $_SESSION['login_time'] > $timeout) {
                    $this->logout();
                    header('Location: /auth/login.php?timeout=1');
                    exit;
                }
            }
        }
    }
}