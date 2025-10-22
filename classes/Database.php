<?php
/**
 * Database Class
 * SQL Injection korumalı, güvenli veritabanı işlemleri
 */

class Database {
    private $pdo;
    private static $instance = null;
    
    private function __construct() {
        $this->pdo = getDBConnection();
    }
    
    /**
     * Singleton pattern - tek instance
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * PDO nesnesini döndür
     */
    public function getConnection() {
        return $this->pdo;
    }
    
    /**
     * UUID oluştur (RFC 4122 uyumlu)
     */
    public function generateUUID() {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
    
    /**
     * SELECT sorgusu çalıştır (tek satır)
     * SQL Injection korumalı - sadece prepared statements
     */
    public function fetchOne($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (PDOException $e) {
            error_log("Database Error (fetchOne): " . $e->getMessage());
            error_log("SQL: " . $sql);
            return null;
        }
    }
    
    /**
     * SELECT sorgusu çalıştır (çok satır)
     */
    public function fetchAll($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database Error (fetchAll): " . $e->getMessage());
            error_log("SQL: " . $sql);
            return [];
        }
    }
    
    /**
     * INSERT/UPDATE/DELETE sorgusu çalıştır
     */
    public function execute($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Database Error (execute): " . $e->getMessage());
            error_log("SQL: " . $sql);
            return false;
        }
    }
    
    /**
     * INSERT sorgusu çalıştır ve UUID döndür
     */
    public function insert($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            if ($stmt->execute($params)) {
                // SQLite'da lastInsertId UUID için çalışmaz
                // UUID'yi parametre olarak geçiriyoruz zaten
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Database Error (insert): " . $e->getMessage());
            error_log("SQL: " . $sql);
            return false;
        }
    }
    
    /**
     * Transaction başlat
     */
    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }
    
    /**
     * Transaction'ı onayla
     */
    public function commit() {
        return $this->pdo->commit();
    }
    
    /**
     * Transaction'ı geri al
     */
    public function rollback() {
        return $this->pdo->rollback();
    }
    
    /**
     * Kullanıcıyı ID ile getir
     */
    public function getUserById($id) {
        $sql = "SELECT * FROM User WHERE id = ? LIMIT 1";
        return $this->fetchOne($sql, [$id]);
    }
    
    /**
     * Kullanıcıyı email ile getir
     */
    public function getUserByEmail($email) {
        $sql = "SELECT * FROM User WHERE email = ? LIMIT 1";
        return $this->fetchOne($sql, [$email]);
    }
    
    /**
     * Kullanıcı bakiyesini güncelle
     */
    public function updateUserBalance($userId, $newBalance) {
        $sql = "UPDATE User SET balance = ? WHERE id = ?";
        return $this->execute($sql, [$newBalance, $userId]);
    }

    /**
     * Kullanıcı şifresini günceller
     */
    public function updateUserPassword($userId, $newHashedPassword) {
        $sql = "UPDATE User SET password = ? WHERE id = ?";
        return $this->execute($sql, [$newHashedPassword, $userId]);
    }
    
    /**
     * Firmayı ID ile getir
     */
    public function getCompanyById($id) {
        $sql = "SELECT * FROM Bus_Company WHERE id = ? LIMIT 1";
        return $this->fetchOne($sql, [$id]);
    }
    
    /**
     * Tüm firmaları listele
     */
    public function getAllCompanies() {
        $sql = "SELECT * FROM Bus_Company ORDER BY name ASC";
        return $this->fetchAll($sql);
    }
    
    /**
     * Seferi ID ile getir
     */
    public function getTripById($id) {
        $sql = "SELECT t.*, bc.name as company_name, bc.logo_path
                FROM Trips t 
                LEFT JOIN Bus_Company bc ON t.company_id = bc.id 
                WHERE t.id = ? LIMIT 1";
        return $this->fetchOne($sql, [$id]);
    }
    
    /**
     * Seferleri ara (kalkış ve varış şehrine göre)
     */
    public function searchTrips($departureCity, $destinationCity, $departureDate = null) {
        $sql = "SELECT t.*, bc.name as company_name, bc.logo_path
                FROM Trips t
                LEFT JOIN Bus_Company bc ON t.company_id = bc.id
                WHERE t.departure_city = ? 
                AND t.destination_city = ?
                AND t.status = ?
                AND t.departure_time >= datetime('now')";
        
        $params = [$departureCity, $destinationCity, TRIP_ACTIVE];
        
        if ($departureDate) {
            $sql .= " AND DATE(t.departure_time) = ?";
            $params[] = $departureDate;
        }
        
        $sql .= " ORDER BY t.departure_time ASC";
        
        return $this->fetchAll($sql, $params);
    }
    
    /**
     * Dolu koltukları getir
     */
    public function getBookedSeats($tripId) {
        $sql = "SELECT bs.seat_number 
                FROM Booked_Seats bs
                INNER JOIN Tickets t ON bs.ticket_id = t.id
                WHERE t.trip_id = ? AND t.status = ?
                ORDER BY bs.seat_number ASC";
        
        $results = $this->fetchAll($sql, [$tripId, TICKET_ACTIVE]);
        return array_column($results, 'seat_number');
    }
    
    /**
     * Tüm kuponları, atandıkları firma adıyla birlikte getirir
     */
    public function getAllCoupons() {
        $sql = "SELECT c.*, bc.name as company_name 
                FROM Coupons c
                LEFT JOIN Bus_Company bc ON c.company_id = bc.id
                ORDER BY c.created_at DESC";
        return $this->fetchAll($sql);
    }

    /**
     * Belirli bir firmaya ait tüm kuponları getirir
     */
    public function getCouponsByCompanyId($companyId) {
        $sql = "SELECT * FROM Coupons WHERE company_id = ? ORDER BY created_at DESC";
        return $this->fetchAll($sql, [$companyId]);
    }

    /**
     * Kuponu koda göre getir
     */
    public function getCouponByCode($code) {
        $sql = "SELECT * FROM Coupons WHERE code = ? LIMIT 1";
        return $this->fetchOne($sql, [strtoupper($code)]);
    }
    
    /**
     * Kupon kullanım sayısını artır
     */
    public function incrementCouponUsage($couponId) {
        $sql = "UPDATE Coupons SET used_count = used_count + 1 WHERE id = ?";
        return $this->execute($sql, [$couponId]);
    }

    /**
     * Kuponu ID ile getir
     */
    public function getCouponById($id) {
        $sql = "SELECT * FROM Coupons WHERE id = ? LIMIT 1";
        return $this->fetchOne($sql, [$id]);
    }
    
    /**
     * Kullanıcının biletlerini getir
     */
    public function getUserTickets($userId) {
        $sql = "SELECT t.*, tr.departure_city, tr.destination_city, 
                tr.departure_time, tr.arrival_time, bc.name as company_name,
                GROUP_CONCAT(bs.seat_number) as seat_numbers
                FROM Tickets t
                INNER JOIN Trips tr ON t.trip_id = tr.id
                LEFT JOIN Bus_Company bc ON tr.company_id = bc.id
                LEFT JOIN Booked_Seats bs ON bs.ticket_id = t.id
                WHERE t.user_id = ?
                GROUP BY t.id
                ORDER BY t.created_at DESC";
        
        return $this->fetchAll($sql, [$userId]);
    }
    
    /**
     * Bileti ID ile getir (güvenlik kontrolü için user_id de kontrol edilir)
     */
    public function getTicketById($ticketId, $userId = null) {
        $sql = "SELECT t.*, tr.*, bc.name as company_name,
                GROUP_CONCAT(bs.seat_number) as seat_numbers
                FROM Tickets t
                INNER JOIN Trips tr ON t.trip_id = tr.id
                LEFT JOIN Bus_Company bc ON tr.company_id = bc.id
                LEFT JOIN Booked_Seats bs ON bs.ticket_id = t.id
                WHERE t.id = ?";
        
        $params = [$ticketId];
        
        if ($userId !== null) {
            $sql .= " AND t.user_id = ?";
            $params[] = $userId;
        }
        
        $sql .= " GROUP BY t.id LIMIT 1";
        
        return $this->fetchOne($sql, $params);
    }

    /**
     * Veritabanındaki tüm benzersiz şehirleri getirir
     */
    public function getAllCities() {
        $sql = "SELECT DISTINCT departure_city AS city FROM Trips
                UNION
                SELECT DISTINCT destination_city AS city FROM Trips
                ORDER BY city ASC";
        
        $results = $this->fetchAll($sql);
        return array_column($results, 'city');
    }

    /**
     * Belirli bir firmaya ait tüm seferleri getirir
     */
    public function getTripsByCompanyId($companyId) {
        $sql = "SELECT * FROM Trips WHERE company_id = ? ORDER BY departure_time DESC";
        return $this->fetchAll($sql, [$companyId]);
    }

    public function getApplicableCoupons($companyId, $excludeCode = null) {
        $sql = "SELECT code, discount, company_id FROM Coupons
                WHERE (company_id IS NULL OR company_id = ?) 
                  AND expire_date >= date('now')             
                  AND used_count < usage_limit             
                  ";
        $params = [$companyId];

        if ($excludeCode) {
            $sql .= " AND code != ?";
            $params[] = $excludeCode;
        }

        $sql .= " ORDER BY company_id IS NULL DESC, discount DESC";

        return $this->fetchAll($sql, $params);
    }
}