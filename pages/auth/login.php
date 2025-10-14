<?php
/**
 * Login Page
 * Güvenli giriş işlemi
 */

$auth = new Auth();
$auth->requireGuest(); // Giriş yapmışsa dashboard'a yönlendir

$error = '';
$email = '';

// POST isteği geldiğinde
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF kontrolü
    CSRF::validateRequest();
    
    // Input'ları al ve sanitize et
    $email = Validator::sanitizeEmail($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validation
    $validator = new Validator();
    
    if (!$validator->required($email, 'Email') || 
        !$validator->email($email, 'Email')) {
        $error = $validator->getFirstError();
    } elseif (!$validator->required($password, 'Şifre')) {
        $error = $validator->getFirstError();
    } else {
        // Giriş işlemi
        $result = $auth->login($email, $password);
        
        if ($result['success']) {
            // Yönlendirme kontrolü (önceki sayfa varsa oraya git)
            if (isset($_SESSION['redirect_after_login'])) {
                $redirect = $_SESSION['redirect_after_login'];
                unset($_SESSION['redirect_after_login']);
                header('Location: ' . $redirect);
            } else {
                // Rolüne göre yönlendir
                $auth->redirectToDashboard();
            }
            exit;
        } else {
            $error = $result['message'];
        }
    }
}

// HTML Template'i dahil et
$pageTitle = 'Giriş Yap - ' . SITE_NAME;
$hideNavbar = true;

require_once INCLUDES_PATH . '/header.php';
require_once VIEWS_PATH . '/auth/login.view.php';
require_once INCLUDES_PATH . '/footer.php';