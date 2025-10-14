<?php
/**
 * Admin - Yeni Kupon Ekleme (Mantık)
 */

$auth = new Auth();
$db = Database::getInstance();

$auth->requireAdmin();

// Form değişkenlerini başlat
$error = '';
$inputs = [
    'code' => '', 'discount' => '', 'usage_limit' => '',
    'expire_date' => '', 'company_id' => ''
];

// Firma seçimi dropdown'ı için tüm firmaları çek
$companies = $db->getAllCompanies();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    CSRF::validateRequest();
    
    foreach ($inputs as $key => &$value) {
        $value = Validator::sanitizeString($_POST[$key] ?? '');
    }
    unset($value);

    // Kupon kodunu büyük harfe çevir
    $inputs['code'] = strtoupper($inputs['code']);

    // Validasyon
    $validator = new Validator();
    if (!$validator->required($inputs['code'], 'Kupon Kodu') ||
        !$validator->minLength($inputs['code'], 4, 'Kupon Kodu')) {
        $error = $validator->getFirstError();
    } elseif ($db->getCouponByCode($inputs['code'])) {
        $error = 'Bu kupon kodu zaten kullanılıyor.';
    } elseif (!$validator->required($inputs['discount'], 'İndirim Oranı') ||
              !$validator->float($inputs['discount'], 'İndirim Oranı') ||
              !$validator->between($inputs['discount'], 1, 100, 'İndirim Oranı')) {
        $error = 'İndirim oranı 1 ile 100 arasında bir sayı olmalıdır.';
    } elseif (!$validator->required($inputs['usage_limit'], 'Kullanım Limiti') ||
              !$validator->integer($inputs['usage_limit'], 'Kullanım Limiti')) {
        $error = 'Kullanım limiti geçerli bir sayı olmalıdır.';
    } elseif (!$validator->required($inputs['expire_date'], 'Son Kullanma Tarihi') ||
              !$validator->date($inputs['expire_date'], 'Son Kullanma Tarihi')) {
        $error = $validator->getFirstError();
    } elseif (new DateTime($inputs['expire_date']) < new DateTime(date('Y-m-d'))) {
        $error = 'Son kullanma tarihi geçmiş bir tarih olamaz.';
    } else {
        // company_id boş ise NULL olarak ayarla 
        $companyId = !empty($inputs['company_id']) ? $inputs['company_id'] : null;

        $couponId = $db->generateUUID();
        $sql = "INSERT INTO Coupons (id, code, discount, usage_limit, expire_date, company_id) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $params = [
            $couponId,
            $inputs['code'],
            $inputs['discount'],
            (int)$inputs['usage_limit'],
            $inputs['expire_date'],
            $companyId
        ];

        if ($db->execute($sql, $params)) {
            set_flash_message('success', "'{$inputs['code']}' kodlu kupon başarıyla eklendi.");
            header('Location: /admin/coupons');
            exit;
        } else {
            $error = 'Veritabanına kayıt sırasında bir hata oluştu.';
        }
    }
}

$pageTitle = 'Yeni Kupon Ekle - Admin Paneli';
require_once INCLUDES_PATH . '/header.php';
require_once VIEWS_PATH . '/admin/add-coupon.view.php';
require_once INCLUDES_PATH . '/footer.php';