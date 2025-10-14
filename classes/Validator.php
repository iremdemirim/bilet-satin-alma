<?php
/**
 * Validator Class
 * Form validasyonu ve input sanitization
 */

class Validator {
    private $errors = [];
    
    /**
     * Email validasyonu
     */
    public function email($value, $fieldName = 'Email') {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = "$fieldName geçerli bir email adresi olmalıdır.";
            return false;
        }
        return true;
    }
    
    /**
     * Zorunlu alan kontrolü
     */
    public function required($value, $fieldName = 'Bu alan') {
        if (empty(trim($value))) {
            $this->errors[] = "$fieldName boş bırakılamaz.";
            return false;
        }
        return true;
    }
    
    /**
     * Minimum uzunluk kontrolü
     */
    public function minLength($value, $min, $fieldName = 'Bu alan') {
        if (mb_strlen($value) < $min) {
            $this->errors[] = "$fieldName en az $min karakter olmalıdır.";
            return false;
        }
        return true;
    }
    
    /**
     * Maximum uzunluk kontrolü
     */
    public function maxLength($value, $max, $fieldName = 'Bu alan') {
        if (mb_strlen($value) > $max) {
            $this->errors[] = "$fieldName en fazla $max karakter olabilir.";
            return false;
        }
        return true;
    }
    
    /**
     * İki değerin eşit olup olmadığını kontrol et (şifre tekrarı için)
     */
    public function matches($value1, $value2, $fieldName = 'Değerler') {
        if ($value1 !== $value2) {
            $this->errors[] = "$fieldName eşleşmiyor.";
            return false;
        }
        return true;
    }
    
    /**
     * Sadece alfanumerik karakterler
     */
    public function alphanumeric($value, $fieldName = 'Bu alan') {
        if (!ctype_alnum($value)) {
            $this->errors[] = "$fieldName sadece harf ve rakam içerebilir.";
            return false;
        }
        return true;
    }
    
    /**
     * Integer kontrolü
     */
    public function integer($value, $fieldName = 'Bu alan') {
        if (!filter_var($value, FILTER_VALIDATE_INT)) {
            $this->errors[] = "$fieldName geçerli bir sayı olmalıdır.";
            return false;
        }
        return true;
    }
    
    /**
     * Float kontrolü
     */
    public function float($value, $fieldName = 'Bu alan') {
        if (!filter_var($value, FILTER_VALIDATE_FLOAT)) {
            $this->errors[] = "$fieldName geçerli bir sayı olmalıdır.";
            return false;
        }
        return true;
    }
    
    /**
     * Tarih formatı kontrolü (Y-m-d)
     */
    public function date($value, $fieldName = 'Tarih') {
        $d = DateTime::createFromFormat('Y-m-d', $value);
        if (!$d || $d->format('Y-m-d') !== $value) {
            $this->errors[] = "$fieldName geçerli bir tarih olmalıdır (YYYY-MM-DD).";
            return false;
        }
        return true;
    }
    
    /**
     * URL validasyonu
     */
    public function url($value, $fieldName = 'URL') {
        if (!filter_var($value, FILTER_VALIDATE_URL)) {
            $this->errors[] = "$fieldName geçerli bir URL olmalıdır.";
            return false;
        }
        return true;
    }
    
    /**
     * Değer aralığı kontrolü
     */
    public function between($value, $min, $max, $fieldName = 'Değer') {
        if ($value < $min || $value > $max) {
            $this->errors[] = "$fieldName $min ile $max arasında olmalıdır.";
            return false;
        }
        return true;
    }
    
    /**
     * Enum kontrolü (belirli değerlerden biri mi)
     */
    public function in($value, array $allowed, $fieldName = 'Değer') {
        if (!in_array($value, $allowed, true)) {
            $this->errors[] = "$fieldName geçersiz.";
            return false;
        }
        return true;
    }
    
    /**
     * Hataları döndür
     */
    public function getErrors() {
        return $this->errors;
    }
    
    /**
     * Hata var mı kontrol et
     */
    public function hasErrors() {
        return !empty($this->errors);
    }
    
    /**
     * Hataları temizle
     */
    public function clearErrors() {
        $this->errors = [];
    }
    
    /**
     * İlk hatayı döndür
     */
    public function getFirstError() {
        return $this->errors[0] ?? '';
    }
    
    /**
     * String'i temizle (XSS koruması)
     */
    public static function sanitizeString($value) {
        return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Email'i temizle
     */
    public static function sanitizeEmail($value) {
        return filter_var(trim($value), FILTER_SANITIZE_EMAIL);
    }
    
    /**
     * Integer'a çevir
     */
    public static function sanitizeInt($value) {
        return filter_var($value, FILTER_SANITIZE_NUMBER_INT);
    }
    
    /**
     * Float'a çevir
     */
    public static function sanitizeFloat($value) {
        return filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    }

    /**
     * Değerin belirli bir regex desenine uyup uymadığını kontrol eder
     * @param string $value Kontrol edilecek değer
     * @param string $pattern Regex deseni
     * @param string $message Hata mesajı
     * @return bool
     */
    public function regex($value, $pattern, $message) {
        if (!preg_match($pattern, $value)) {
            $this->errors[] = $message;
            return false;
        }
        return true;
    }
}