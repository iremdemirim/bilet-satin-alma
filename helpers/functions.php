<?php
/**
 * Yardımcı Fonksiyonlar
 */

/**
 * Session'a tek seferlik mesaj kaydeder.
 * @param string $key 'success', 'error', 'info' gibi
 * @param string $message Gösterilecek mesaj
 */
function set_flash_message($key, $message) {
    $_SESSION['flash_messages'][$key] = $message;
}

/**
 * Session'daki tek seferlik mesajı okur ve siler.
 * @param string $key Okunacak mesajın anahtarı
 * @return string|null Mesaj varsa string, yoksa null döner.
 */
function get_flash_message($key) {
    if (isset($_SESSION['flash_messages'][$key])) {
        $message = $_SESSION['flash_messages'][$key];
        unset($_SESSION['flash_messages'][$key]);
        return $message;
    }
    return null;
}

/**
 * Görüntülenecek flash mesaj var mı kontrol eder.
 * @return bool
 */
function has_flash_messages() {
    return !empty($_SESSION['flash_messages']);
}