<?php
/**
 * Register Page
 */

$auth = new Auth();
$auth->requireGuest();

$error = '';
$fullName = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    CSRF::validateRequest();
    
    $fullName = Validator::sanitizeString($_POST['full_name'] ?? '');
    $email = Validator::sanitizeEmail($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['password_confirm'] ?? '';
    
    $validator = new Validator();
    
    if (!$validator->required($fullName, 'Ad Soyad') || !$validator->minLength($fullName, 3, 'Ad Soyad')) {
        $error = $validator->getFirstError();
    } elseif (!$validator->required($email, 'Email') || !$validator->email($email, 'Email')) {
        $error = $validator->getFirstError();
    } 
    //KONTROLLER
    elseif (!$validator->required($password, 'Şifre') || !$validator->minLength($password, 8, 'Şifre')) {
        $error = $validator->getFirstError();
    } elseif (!$validator->regex($password, '/[A-Z]/', 'Şifre en az bir büyük harf içermelidir.')) {
        $error = $validator->getFirstError();
    } elseif (!$validator->regex($password, '/[a-z]/', 'Şifre en az bir küçük harf içermelidir.')) {
        $error = $validator->getFirstError();
    } elseif (!$validator->regex($password, '/[0-9]/', 'Şifre en az bir rakam içermelidir.')) {
        $error = $validator->getFirstError();
    } elseif (!$validator->regex($password, '/[^A-Za-z0-9]/', 'Şifre en az bir özel karakter (@, #, $, % vb.) içermelidir.')) {
        $error = $validator->getFirstError();
    }
    
    elseif (!$validator->matches($password, $passwordConfirm, 'Şifreler')) {
        $error = $validator->getFirstError();
    } else {
        $result = $auth->register($fullName, $email, $password);
        
        if ($result['success']) {
            set_flash_message('success', 'Kaydınız başarıyla oluşturuldu. Şimdi giriş yapabilirsiniz.');
            header('Location: /login');
            exit;
        } else {
            $error = $result['message'];
        }
    }
}

$pageTitle = 'Kayıt Ol - ' . SITE_NAME;
$hideNavbar = true;

require_once INCLUDES_PATH . '/header.php';
require_once VIEWS_PATH . '/auth/register.view.php';
require_once INCLUDES_PATH . '/footer.php';