<?php
/**
 * Firma Admini - Yeni Kupon Ekleme (Mantık) - GÜVENLİK İYİLEŞTİRMELERİ İLE
 */

$auth = new Auth();
$db = Database::getInstance();

$auth->requireCompanyAdmin();
$companyAdmin = $auth->getCurrentUser();

// Form değişkenlerini başlat
$error = '';
$inputs = [
    'code' => '', 'discount' => '', 'usage_limit' => '',
    'expire_date' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    CSRF::validateRequest();

    foreach ($inputs as $key => &$value) {
        $value = Validator::sanitizeString($_POST[$key] ?? '');
    }
    unset($value);

    // Kupon kodunu büyük harfe çevir
    $inputs['code'] = strtoupper($inputs['code']);

    // Validasyon (Süper Admin ile aynı kurallar)
    $validator = new Validator();
    if (!$validator->required($inputs['code'], 'Kupon Kodu') ||
        !$validator->minLength($inputs['code'], 4, 'Kupon Kodu') ||
        !$validator->maxLength($inputs['code'], 10, 'Kupon Kodu')) { 
        $error = $validator->getFirstError();
    } elseif ($db->getCouponByCode($inputs['code'])) {
        // Kupon kodunun benzersizliğini kontrol et 
        $error = 'Bu kupon kodu zaten kullanılıyor.';
    } elseif (!$validator->required($inputs['discount'], 'İndirim Oranı') ||
              !$validator->float($inputs['discount'], 'İndirim Oranı') ||
              !$validator->between($inputs['discount'], 1, 100, 'İndirim Oranı')) {
        $error = 'İndirim oranı 1 ile 100 arasında bir sayı olmalıdır.';

    } elseif (!$validator->required($inputs['usage_limit'], 'Kullanım Limiti') ||
              !$validator->integer($inputs['usage_limit'], 'Kullanım Limiti') ||
              (int)$inputs['usage_limit'] <= 0) { 
        $error = 'Kullanım limiti geçerli bir pozitif sayı olmalıdır.';
    } elseif ((int)$inputs['usage_limit'] > 3000) { 
        $error = 'Kullanım limiti en fazla 3000 olabilir.';

    } elseif (!$validator->required($inputs['expire_date'], 'Son Kullanma Tarihi') ||
              !$validator->date($inputs['expire_date'], 'Son Kullanma Tarihi')) {
        $error = $validator->getFirstError();
    } elseif (new DateTime($inputs['expire_date']) < new DateTime(date('Y-m-d'))) {
        $error = 'Son kullanma tarihi geçmiş bir tarih olamaz.';
    } elseif (new DateTime($inputs['expire_date']) > new DateTime('+10 years')) { 
        $error = 'Son kullanma tarihi çok uzak bir gelecekte olamaz (En fazla 10 yıl).';
    } else {
        $companyId = $companyAdmin['company_id'];

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
            header('Location: /company/coupons');
            exit;
        } else {
            $error = 'Veritabanına kayıt sırasında bir hata oluştu.';
        }
    }
}

$pageTitle = 'Yeni Kupon Ekle - Firma Paneli';
require_once INCLUDES_PATH . '/header.php';
require_once VIEWS_PATH . '/company/add-coupon.view.php';
require_once INCLUDES_PATH . '/footer.php';