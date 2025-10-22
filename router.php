<?php
/**
 * Router
 * Gelen URL'i analiz eder ve ilgili controller/page dosyasını çağırır.
 */

// Gelen URI'yi al ve temizle
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = rtrim($uri, '/');
$uri = $uri === '' ? '/' : $uri; // Ana sayfa için

// Rotaları ve karşılık gelen dosyaları tanımla
$routes = [
    // Genel Sayfalar
    '/' => 'pages/home.php',
    '/buy-ticket' => 'pages/buy-ticket.php',
    '/trip/([a-zA-Z0-9\-]+)' => 'pages/trip-details.php',
    '/checkout' => 'pages/checkout.php',
    '/process-payment' => 'pages/process-payment.php',
    '/apply-coupon' => 'pages/apply-coupon.php',
    '/remove-coupon' => 'pages/remove-coupon.php',
    
    // Kimlik Doğrulama
    '/login' => 'pages/auth/login.php',
    '/register' => 'pages/auth/register.php',
    '/logout' => 'pages/auth/logout.php',
    
    // User Paneli
    '/dashboard' => 'pages/user/dashboard.php',
    '/my-tickets' => 'pages/user/my-tickets.php',
    '/user/cancel-ticket' => 'pages/user/cancel-ticket.php',
    '/user/ticket-pdf' => 'pages/user/ticket-pdf.php',
    '/profile' => 'pages/user/profile.php',
    
    // Firma Admin Paneli
    '/company/dashboard' => 'pages/company/dashboard.php',
    '/company/trips' => 'pages/company/trips.php',
    '/company/trips/add' => 'pages/company/add-trip.php',
    '/company/trips/edit' => 'pages/company/edit-trip.php',
    '/company/trips/delete' => 'pages/company/delete-trip.php',
    '/company/coupons' => 'pages/company/coupons.php',
    '/company/coupons/add' => 'pages/company/add-coupon.php',
    '/company/coupons/edit' => 'pages/company/edit-coupon.php',
    '/company/coupons/delete' => 'pages/company/delete-coupon.php',
    
    // Admin Paneli
    '/admin/dashboard' => 'pages/admin/dashboard.php',
    '/admin/companies' => 'pages/admin/companies.php',
    '/admin/companies/add' => 'pages/admin/add-company.php',
    '/admin/companies/edit' => 'pages/admin/edit-company.php',
    '/admin/companies/delete' => 'pages/admin/delete-company.php',
    '/admin/company-admins/add' => 'pages/admin/add-company-admin.php',
    '/admin/company-admins' => 'pages/admin/company-admins.php',
    '/admin/company-admins/edit' => 'pages/admin/edit-company-admin.php',
    '/admin/company-admins/delete' => 'pages/admin/delete-company-admin.php',
    '/admin/coupons' => 'pages/admin/coupons.php',
    '/admin/coupons/add' => 'pages/admin/add-coupon.php',
    '/admin/coupons/edit' => 'pages/admin/edit-coupon.php',
    '/admin/coupons/delete' => 'pages/admin/delete-coupon.php'

];

// 1. Sabit Rota Kontrolü
if (array_key_exists($uri, $routes)) {
    require_once ROOT_PATH . '/' . $routes[$uri];
    exit; // Rota bulundu, işlemi sonlandır.
}

// 2. Dinamik Rota Kontrolü (Sefer Detay Sayfası için)
// Bu kod, /trip/ ile başlayan herhangi bir URL'yi yakalar.
if (preg_match('/^\/trip\/([a-zA-Z0-9\-]+)$/', $uri, $matches)) {
    // URL'den gelen ID'yi al (örn: /trip/abc-123 -> $matches[1] = 'abc-123')
    $_GET['id'] = $matches[1]; 
    require_once ROOT_PATH . '/pages/trip-details.php';
    exit;
}

// 3. Hiçbir Rota Eşleşmezse 404 Sayfası
http_response_code(404);
require_once ROOT_PATH . '/pages/errors/404.php';