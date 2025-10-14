<?php
/**
 * Admin - Yeni Firma Ekleme (Mantık)
 */

$auth = new Auth();
$db = Database::getInstance();

// Bu sayfaya sadece adminler erişebilir
$auth->requireAdmin();

$error = '';
$companyName = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    CSRF::validateRequest();

    $companyName = Validator::sanitizeString($_POST['company_name'] ?? '');
    $logoFile = $_FILES['logo'] ?? null;
    $logoPath = null;

    $validator = new Validator();
    if (!$validator->required($companyName, 'Firma Adı')) {
        $error = $validator->getFirstError();
    } else {
        if ($logoFile && $logoFile['error'] === UPLOAD_ERR_OK) {
            $uploadDir = ROOT_PATH . '/public/assets/images/companies/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($logoFile['type'], $allowedTypes)) {
                $error = 'Geçersiz dosya türü. Sadece JPG, PNG, GIF, WEBP formatları kabul edilir.';
            } elseif ($logoFile['size'] > 2 * 1024 * 1024) { // 2MB limit
                $error = 'Dosya boyutu çok büyük. Maksimum 2MB olabilir.';
            } else {
                $fileName = uniqid() . '-' . basename($logoFile['name']);
                $targetFile = $uploadDir . $fileName;

                if (move_uploaded_file($logoFile['tmp_name'], $targetFile)) {
                    $logoPath = '/assets/images/companies/' . $fileName;
                } else {
                    $error = 'Logo yüklenirken bir hata oluştu.';
                }
            }
        }

        // Bir hata yoksa veritabanına ekle
        if (empty($error)) {
            $companyId = $db->generateUUID();
            $sql = "INSERT INTO Bus_Company (id, name, logo_path) VALUES (?, ?, ?)";
            
            if ($db->execute($sql, [$companyId, $companyName, $logoPath])) {
                set_flash_message('success', "'$companyName' adlı firma başarıyla eklendi.");
                header('Location: /admin/companies');
                exit;
            } else {
                $error = 'Veritabanına kayıt sırasında bir hata oluştu.';
            }
        }
    }
}

$pageTitle = 'Yeni Firma Ekle - Admin Paneli';
require_once INCLUDES_PATH . '/header.php';
require_once VIEWS_PATH . '/admin/add-company.view.php';
require_once INCLUDES_PATH . '/footer.php';