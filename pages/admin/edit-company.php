<?php
/**
 * Admin - Firma Düzenleme (Mantık)
 */

$auth = new Auth();
$db = Database::getInstance();

$auth->requireAdmin();

$error = '';
$companyId = $_GET['id'] ?? null;

// ID yoksa veya firma bulunamazsa, listeleme sayfasına yönlendir.
if (!$companyId) {
    header('Location: /admin/companies');
    exit;
}

// Düzenlenecek firmayı veritabanından çek
$company = $db->getCompanyById($companyId);
if (!$company) {
    set_flash_message('error', 'Firma bulunamadı.');
    header('Location: /admin/companies');
    exit;
}

// Form gönderildiğinde
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    CSRF::validateRequest();

    // Formdan gelen ID'nin de doğru olduğundan emin olalım
    $postedId = $_POST['company_id'] ?? '';
    if ($postedId !== $companyId) {
        die('Geçersiz işlem.');
    }

    $companyName = Validator::sanitizeString($_POST['company_name'] ?? '');
    $logoFile = $_FILES['logo'] ?? null;
    $logoPath = $company['logo_path']; // Mevcut logoyu koru

    $validator = new Validator();
    if (!$validator->required($companyName, 'Firma Adı')) {
        $error = $validator->getFirstError();
    } else {
        // Yeni bir logo yüklendiyse, eskisini silip yenisini kaydet
        if ($logoFile && $logoFile['error'] === UPLOAD_ERR_OK) {
            $uploadDir = ROOT_PATH . '/public/assets/images/companies/';
            // ... (Logo yükleme mantığı 'add-company.php' ile aynı) ...
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (in_array($logoFile['type'], $allowedTypes) && $logoFile['size'] <= 2 * 1024 * 1024) {
                // Eski logoyu sunucudan sil (varsa)
                if ($logoPath && file_exists(ROOT_PATH . '/public' . $logoPath)) {
                    unlink(ROOT_PATH . '/public' . $logoPath);
                }
                
                $fileName = uniqid() . '-' . basename($logoFile['name']);
                $targetFile = $uploadDir . $fileName;
                if (move_uploaded_file($logoFile['tmp_name'], $targetFile)) {
                    $logoPath = '/assets/images/companies/' . $fileName;
                } else {
                    $error = 'Yeni logo yüklenirken bir hata oluştu.';
                }
            } else {
                 $error = 'Geçersiz dosya türü veya boyutu (Maks. 2MB).';
            }
        }

        if (empty($error)) {
            $sql = "UPDATE Bus_Company SET name = ?, logo_path = ? WHERE id = ?";
            
            if ($db->execute($sql, [$companyName, $logoPath, $companyId])) {
                set_flash_message('success', "'$companyName' adlı firma başarıyla güncellendi.");
                header('Location: /admin/companies');
                exit;
            } else {
                $error = 'Veritabanı güncellenirken bir hata oluştu.';
            }
        }
    }
}

$pageTitle = 'Firmayı Düzenle - Admin Paneli';
require_once INCLUDES_PATH . '/header.php';
require_once VIEWS_PATH . '/admin/edit-company.view.php';
require_once INCLUDES_PATH . '/footer.php';