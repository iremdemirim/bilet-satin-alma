<?php
/**
 * Profil ve Şifre Değiştirme (Mantık)
 */

$auth = new Auth();
$db = Database::getInstance();

// Bu sayfaya sadece giriş yapmış kullanıcılar erişebilir
$auth->requireLogin();

$currentUser = $auth->getCurrentUser();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    CSRF::validateRequest();
    
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $newPasswordConfirm = $_POST['new_password_confirm'] ?? '';

    // Veritabanından kullanıcının güncel bilgilerini (ve hash'lenmiş şifresini) çek
    $userFromDb = $db->getUserById($currentUser['id']);

    $validator = new Validator();
    if (empty($currentPassword) || empty($newPassword) || empty($newPasswordConfirm)) {
        $error = 'Tüm alanlar doldurulmalıdır.';
    } elseif (!password_verify($currentPassword, $userFromDb['password'])) {
        $error = 'Mevcut şifreniz hatalı.';
    } elseif (!$validator->matches($newPassword, $newPasswordConfirm, 'Yeni şifreler')) {
        $error = $validator->getFirstError();
    } 
    // Kayıt olurken kullandığımız güçlü şifre kurallarını burada da kullanıyoruz
    elseif (!$validator->minLength($newPassword, 8, 'Yeni Şifre') ||
            !$validator->regex($newPassword, '/[A-Z]/', 'Yeni şifre en az bir büyük harf içermelidir.') ||
            !$validator->regex($newPassword, '/[a-z]/', 'Yeni şifre en az bir küçük harf içermelidir.') ||
            !$validator->regex($newPassword, '/[0-9]/', 'Yeni şifre en az bir rakam içermelidir.') ||
            !$validator->regex($newPassword, '/[^A-Za-z0-9]/', 'Yeni şifre en az bir özel karakter içermelidir.')) {
        $error = $validator->getFirstError();
    } else {
        // Tüm kontroller başarılı, yeni şifreyi hash'le ve veritabanını güncelle
        $newHashedPassword = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);
        
        if ($db->updateUserPassword($currentUser['id'], $newHashedPassword)) {
            set_flash_message('success', 'Şifreniz başarıyla güncellendi.');
            header('Location: /profile'); // Sayfayı yenileyerek başarı mesajını göster
            exit;
        } else {
            $error = 'Şifre güncellenirken bir hata oluştu.';
        }
    }
}

$pageTitle = 'Profilim - ' . SITE_NAME;
require_once INCLUDES_PATH . '/header.php';
require_once VIEWS_PATH . '/user/profile.view.php';
require_once INCLUDES_PATH . '/footer.php';