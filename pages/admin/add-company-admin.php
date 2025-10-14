<?php
/**
 * Admin - Yeni Firma Admini Ekleme (Mantık)
 */

$auth = new Auth();
$db = Database::getInstance();

// Bu sayfaya sadece adminler erişebilir
$auth->requireAdmin();

// Formu ve değişkenleri hazırla
$error = '';
$fullName = '';
$email = '';
$companyId = '';

// Dropdown menüyü doldurmak için tüm firmaları çek
$companies = $db->getAllCompanies();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    CSRF::validateRequest();

    // Formdan gelen verileri temizle
    $fullName = Validator::sanitizeString($_POST['full_name'] ?? '');
    $email = Validator::sanitizeEmail($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['password_confirm'] ?? '';
    $companyId = $_POST['company_id'] ?? '';

    // Validasyon işlemleri
    $validator = new Validator();
    if (!$validator->required($fullName, 'Ad Soyad') || !$validator->minLength($fullName, 3, 'Ad Soyad')) {
        $error = $validator->getFirstError();
    } elseif (!$validator->required($email, 'Email') || !$validator->email($email, 'Email')) {
        $error = $validator->getFirstError();
    } elseif (!$validator->required($password, 'Şifre') || !$validator->minLength($password, 8, 'Şifre')) {
        $error = $validator->getFirstError();
    } elseif (!$validator->regex($password, '/[A-Z]/', 'Şifre en az bir büyük harf içermelidir.')) {
        $error = $validator->getFirstError();
    } elseif (!$validator->regex($password, '/[0-9]/', 'Şifre en az bir rakam içermelidir.')) {
        $error = $validator->getFirstError();
    } elseif (!$validator->matches($password, $passwordConfirm, 'Şifreler')) {
        $error = $validator->getFirstError();
    } elseif (!$validator->required($companyId, 'Firma')) {
        $error = 'Lütfen bir firma seçin.';
    } else {
        
        $result = $auth->register(
            $fullName,
            $email,
            $password,
            ROLE_COMPANY, 
            $companyId    
        );

        if ($result['success']) {
            set_flash_message('success', "'$fullName' adlı firma admini başarıyla oluşturuldu.");
            header('Location: /admin/company-admins');
            exit;
        } else {
            $error = $result['message']; 
        }
    }
}

$pageTitle = 'Yeni Firma Admini Ekle - Admin Paneli';
require_once INCLUDES_PATH . '/header.php';
require_once VIEWS_PATH . '/admin/add-company-admin.view.php';
require_once INCLUDES_PATH . '/footer.php';