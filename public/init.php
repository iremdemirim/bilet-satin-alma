<?php
/**
 * Veritabanı Kurulum Script'i
 */

$allowedIPs = ['127.0.0.1', '::1', 'localhost'];
if (!in_array($_SERVER['REMOTE_ADDR'] ?? '', $allowedIPs)) {
    die('Bu script sadece localhost üzerinden çalıştırılabilir.');
}

if (file_exists(__DIR__ . '/data/bilet_sistemi.db') && filesize(__DIR__ . '/data/bilet_sistemi.db') > 10240) {
    die('<h2>⚠️ HATA: Veritabanı Zaten Mevcut</h2><p>Güvenlik nedeniyle kurulum yeniden yapılamaz. Sıfırdan başlamak için <code>data/bilet_sistemi.db</code> dosyasını silin.</p>');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || ($_POST['confirm_install'] ?? '') !== 'KURULUM_ONAYLI') {
    ?>
    <!DOCTYPE html>
    <html lang="tr">
    <head>
        <meta charset="UTF-8">
        <title>Veritabanı Kurulumu</title>
        <style>/* ... CSS kodları ... */</style>
    </head>
    <body>
        <h1>⚠️ Veritabanı Kurulumu</h1>
        <div class="warning">
            <h3>DİKKAT!</h3>
            <p><strong>Kurulum sonrası bu dosyayı mutlaka silin!</strong></p>
        </div>
        <form method="POST">
            <label for="confirm">Devam etmek için <strong>KURULUM_ONAYLI</strong> yazın:</label>
            <input type="text" id="confirm" name="confirm_install" required>
            <button type="submit" class="btn">Kurulumu Başlat</button>
        </form>
        <p><a href="/">İptal Et ve Ana Sayfaya Dön</a></p>
    </body>
    </html>
    <?php
    exit;
}

require_once __DIR__ . '/config/config.php';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kurulum - Bilet Satın Alma Platformu</title>
    <style>/* ... CSS kodları ... */</style>
</head>
<body>
<h2>Bilet Satın Alma Platformu - Kurulum</h2>
<hr>
<?php
try {
    // 1. Data klasörünü oluştur
    echo "<div class='step'><strong>1. Data klasörü kontrol ediliyor...</strong><br>";
    $dataDir = ROOT_PATH . '/data';
    if (!is_dir($dataDir)) {
        mkdir($dataDir, 0755, true);
        echo "<span class='success'>✓ Data klasörü oluşturuldu</span><br>";
    } else {
        echo "<span class='success'>✓ Data klasörü zaten mevcut</span><br>";
    }
    echo "</div>";

    // 2. Veritabanını ve tabloları oluştur
    echo "<div class='step'><strong>2. Veritabanı tabloları oluşturuluyor...</strong><br>";
    
    $pdo = getDBConnection(); // database.php'den bağlantıyı al
    $sqlFile = ROOT_PATH . '/sql/create_tables.sql';
    if (!file_exists($sqlFile)) {
        throw new Exception("SQL kurulum dosyası bulunamadı: " . $sqlFile);
    }
    $sql = file_get_contents($sqlFile);
    $pdo->exec($sql);

    echo "<span class='success'>✓ Veritabanı tabloları başarıyla oluşturuldu</span><br>";
    echo "</div>";

    // 3. İlk admin kullanıcısını ekle
    echo "<div class='step'><strong>3. Admin kullanıcısı oluşturuluyor...</strong><br>";
    $db = Database::getInstance();
    $auth = new Auth();
    if ($db->getUserByEmail('admin@bilet.com')) {
        echo "<span class='warning'>⚠ Admin kullanıcısı zaten mevcut</span><br>";
    } else {
        $result = $auth->register('Sistem Admin', 'admin@bilet.com', 'password123', ROLE_ADMIN);
        if (!$result['success']) {
            throw new Exception("Admin oluşturulamadı: " . $result['message']);
        }
        echo "<span class='success'>✓ Admin kullanıcısı oluşturuldu</span><br>";
    }
    echo "</div>";

    // 4. Örnek firmaları ekle
    echo "<div class='step'><strong>4. Örnek firmalar ekleniyor...</strong><br>";
    echo "</div>";

    // --- Başarı Mesajı ve Yönlendirme ---
    echo "<hr><h3 class='success'>✓ Kurulum Başarıyla Tamamlandı!</h3>";
    echo "<div class='credential-box'> ... (Giriş bilgileri kutusu) ... </div>";
    echo "<div style='...'><h4>⚠️ GÜVENLİK UYARISI</h4><p><strong>Kurulum tamamlandı. Bu dosyayı HEMEN silin!</strong></p></div>";
    
    echo "<p><a href='/login' style='...'>Giriş Yap</a></p>";

} catch (Exception $e) {
}
?>
</body>
</html>