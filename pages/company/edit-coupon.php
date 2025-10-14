<?php
/**
 * Firma Admini - Kupon Düzenleme (Mantık)
 */

$auth = new Auth();
$db = Database::getInstance();

$auth->requireCompanyAdmin();
$companyAdmin = $auth->getCurrentUser();

$error = '';
$couponId = $_GET['id'] ?? null;

if (!$couponId) {
    header('Location: /company/coupons');
    exit;
}

// Düzenlenecek kuponu veritabanından çek
$coupon = $db->getCouponById($couponId);

//Kupon yoksa VEYA kupon bu firmanın değilse, erişimi engelle.
if (!$coupon || $coupon['company_id'] !== $companyAdmin['company_id']) {
    set_flash_message('error', 'Geçersiz veya yetkiniz olmayan bir kupona erişmeye çalıştınız.');
    header('Location: /company/coupons');
    exit;
}

// Formu doldurmak için mevcut verileri bir diziye ata
$inputs = [
    'code' => $coupon['code'], 'discount' => $coupon['discount'],
    'usage_limit' => $coupon['usage_limit'],
    'expire_date' => date('Y-m-d', strtotime($coupon['expire_date']))
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inputs['code'] = strtoupper(Validator::sanitizeString($_POST['code'] ?? ''));

    $sql = "UPDATE Coupons SET code = ?, discount = ?, usage_limit = ?, expire_date = ?
            WHERE id = ? AND company_id = ?";
    $params = [
        $inputs['code'],
        $_POST['discount'], // Diğer alanları da buradan al
        $_POST['usage_limit'],
        $_POST['expire_date'],
        $couponId,
        $companyAdmin['company_id'] // Güvenlik için tekrar kontrol et
    ];

    if ($db->execute($sql, $params)) {
        set_flash_message('success', 'Kupon başarıyla güncellendi.');
        header('Location: /company/coupons');
        exit;
    } else {
        $error = 'Veritabanı güncellenirken bir hata oluştu.';
    }
}

$pageTitle = 'Kuponu Düzenle - Firma Paneli';
require_once INCLUDES_PATH . '/header.php';
require_once VIEWS_PATH . '/company/edit-coupon.view.php';
require_once INCLUDES_PATH . '/footer.php';