<?php
/**
 * Admin - Kupon Düzenleme (Mantık)
 */

$auth = new Auth();
$db = Database::getInstance();

$auth->requireAdmin();

$error = '';
$couponId = $_GET['id'] ?? null;

if (!$couponId) {
    header('Location: /admin/coupons');
    exit;
}

// Düzenlenecek kuponu veritabanından çek
$coupon = $db->getCouponById($couponId);
if (!$coupon) {
    set_flash_message('error', 'Kupon bulunamadı.');
    header('Location: /admin/coupons');
    exit;
}

// Formu doldurmak için mevcut verileri bir diziye ata
$inputs = [
    'code' => $coupon['code'], 'discount' => $coupon['discount'], 
    'usage_limit' => $coupon['usage_limit'],
    'expire_date' => date('Y-m-d', strtotime($coupon['expire_date'])), 
    'company_id' => $coupon['company_id']
];
$companies = $db->getAllCompanies();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    CSRF::validateRequest();
    
    foreach ($inputs as $key => &$value) {
        $value = Validator::sanitizeString($_POST[$key] ?? '');
    }
    unset($value);

    $inputs['code'] = strtoupper($inputs['code']);

    // Validasyon
    $existingCoupon = $db->getCouponByCode($inputs['code']);
    if ($existingCoupon && $existingCoupon['id'] !== $couponId) {
        $error = 'Bu kupon kodu başka bir kupon tarafından kullanılıyor.';
    } else {
        // ... (add-coupon.php'deki diğer validasyon kurallarının aynısı) ...
        $companyId = !empty($inputs['company_id']) ? $inputs['company_id'] : null;

        $sql = "UPDATE Coupons SET code = ?, discount = ?, usage_limit = ?, expire_date = ?, company_id = ? 
                WHERE id = ?";
        $params = [
            $inputs['code'],
            $inputs['discount'],
            (int)$inputs['usage_limit'],
            $inputs['expire_date'],
            $companyId,
            $couponId
        ];

        if ($db->execute($sql, $params)) {
            set_flash_message('success', "'{$inputs['code']}' kodlu kupon başarıyla güncellendi.");
            header('Location: /admin/coupons');
            exit;
        } else {
            $error = 'Veritabanı güncellenirken bir hata oluştu.';
        }
    }
}

$pageTitle = 'Kuponu Düzenle - Admin Paneli';
require_once INCLUDES_PATH . '/header.php';
require_once VIEWS_PATH . '/admin/edit-coupon.view.php';
require_once INCLUDES_PATH . '/footer.php';