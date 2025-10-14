<?php
/**
 * Admin - Firma Admini Düzenleme (Mantık)
 */

$auth = new Auth();
$db = Database::getInstance();

$auth->requireAdmin();

$error = '';
$userId = $_GET['id'] ?? null;

// ID yoksa veya kullanıcı bulunamazsa, listeleme sayfasına yönlendir.
if (!$userId) {
    header('Location: /admin/company-admins');
    exit;
}

// Düzenlenecek kullanıcıyı veritabanından çek
$user = $db->fetchOne("SELECT * FROM User WHERE id = ? AND role = ?", [$userId, ROLE_COMPANY]);
if (!$user) {
    set_flash_message('error', 'Firma admini bulunamadı.');
    header('Location: /admin/company-admins');
    exit;
}

$companies = $db->getAllCompanies();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    CSRF::validateRequest();

    $fullName = Validator::sanitizeString($_POST['full_name'] ?? '');
    $email = Validator::sanitizeEmail($_POST['email'] ?? '');
    $companyId = $_POST['company_id'] ?? '';
    $password = $_POST['password'] ?? '';

    $validator = new Validator();
    // Önce temel alanları doğrula
    if (!$validator->required($fullName, 'Ad Soyad')) {
        $error = $validator->getFirstError();
    } elseif (!$validator->required($email, 'Email') || !$validator->email($email, 'Email')) {
        $error = $validator->getFirstError();
    } elseif (!$validator->required($companyId, 'Firma')) {
        $error = 'Lütfen bir firma seçin.';
    } else {
        $existingUser = $db->getUserByEmail($email);
        if ($existingUser && $existingUser['id'] !== $userId) {
            $error = 'Bu email adresi başka bir kullanıcı tarafından kullanılıyor.';
        }
    }
    
    // Temel alanlarda hata yoksa ve şifre alanı DOLDURULMUŞSA, şifreyi doğrula
    if (empty($error) && !empty($password)) {
        if (!$validator->minLength($password, 8, 'Şifre')) {
            $error = $validator->getFirstError();
        } elseif (!$validator->regex($password, '/[A-Z]/', 'Şifre en az bir büyük harf içermelidir.')) {
            $error = $validator->getFirstError();
        } elseif (!$validator->regex($password, '/[a-z]/', 'Şifre en az bir küçük harf içermelidir.')) {
            $error = $validator->getFirstError();
        } elseif (!$validator->regex($password, '/[0-9]/', 'Şifre en az bir rakam içermelidir.')) {
            $error = $validator->getFirstError();
        } elseif (!$validator->regex($password, '/[^A-Za-z0-9]/', 'Şifre en az bir özel karakter içermelidir.')) {
            $error = $validator->getFirstError();
        }
    }

    // Tüm kontrollerden sonra hala bir hata yoksa, veritabanını güncelle
    if (empty($error)) {
        if (!empty($password)) {
            // Şifre güncellenecek
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
            $sql = "UPDATE User SET full_name = ?, email = ?, company_id = ?, password = ? WHERE id = ?";
            $params = [$fullName, $email, $companyId, $hashedPassword, $userId];
        } else {
            // Şifre güncellenmeyecek
            $sql = "UPDATE User SET full_name = ?, email = ?, company_id = ? WHERE id = ?";
            $params = [$fullName, $email, $companyId, $userId];
        }

        if ($db->execute($sql, $params)) {
            set_flash_message('success', "'$fullName' adlı firma admini başarıyla güncellendi.");
            header('Location: /admin/company-admins');
            exit;
        } else {
            $error = 'Veritabanı güncellenirken bir hata oluştu.';
        }
    }
}

$pageTitle = 'Firma Admini Düzenle - Admin Paneli';
require_once INCLUDES_PATH . '/header.php';
require_once VIEWS_PATH . '/admin/edit-company-admin.view.php';
require_once INCLUDES_PATH . '/footer.php';