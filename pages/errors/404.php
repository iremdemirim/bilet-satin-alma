<?php
/**
 * 404 Not Found Sayfası
 */

// Sayfa bulunamadığı için HTTP durum kodunu 404 olarak ayarlayalım
http_response_code(404);

$pageTitle = '404 - Sayfa Bulunamadı';
$hideNavbar = true; // İsteğe bağlı, navbar'ı gizleyebiliriz

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="container text-center" style="padding: 100px 20px;">
    <h1>404</h1>
    <h2>Sayfa Bulunamadı</h2>
    <p class="mt-3">Aradığınız sayfa mevcut değil veya taşınmış olabilir.</p>
    <a href="/" class="btn btn-primary mt-4">Ana Sayfaya Dön</a>
</div>

<?php
require_once __DIR__ . '/../../includes/footer.php';