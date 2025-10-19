<?php
/**
 * Veritabanı Kurulum Script'i
 * GÜVENLİK: Kurulum sonrası bu dosyayı MUTLAKA SİLİN!
 */

// --- GÜVENLİK KONTROLLERİ ---
$allowedIPs = ['127.0.0.1', '::1', 'localhost'];
if (!in_array($_SERVER['REMOTE_ADDR'] ?? '', $allowedIPs)) {
    die('Bu script sadece localhost üzerinden çalıştırılabilir.');
}

if (file_exists(__DIR__ . '/data/bilet_sistemi.db') && filesize(__DIR__ . '/data/bilet_sistemi.db') > 10240) {
    die('<h2>⚠️ HATA: Veritabanı Zaten Mevcut</h2><p>Güvenlik nedeniyle kurulum yeniden yapılamaz. Sıfırdan başlamak için <code>data/bilet_sistemi.db</code> dosyasını silin.</p>');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || ($_POST['confirm_install'] ?? '') !== 'KURULUM_ONAYLI') {
    // --- ONAY FORMU ---
    ?>
    <!DOCTYPE html>
    <html lang="tr">
    <head>
        <meta charset="UTF-8">
        <title>Veritabanı Kurulumu</title>
        <style>
            body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; }
            .warning { background: #fff3cd; border: 1px solid #ffc107; padding: 20px; border-radius: 5px; margin: 20px 0; }
            .btn { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
            input[type="text"] { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 5px; }
        </style>
    </head>
    <body>
        <h1>⚠️ Veritabanı Kurulumu</h1>
        <div class="warning">
            <h3>DİKKAT!</h3>
             <p>Bu işlem:</p>
            <ul>
                <li>Yeni bir veritabanı oluşturacak</li>
                <li>Tüm tabloları kuracak</li>
                <li>İlk admin kullanıcısını ekleyecek</li>
                <li>Örnek firmaları ekleyecek</li>
            </ul>
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

// --- KURULUM BAŞLANGICI ---
require_once __DIR__ . '/config/config.php';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kurulum - Bilet Satın Alma Platformu</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .warning { color: #ffc107; }
        .step { background: #f8f9fa; padding: 15px; margin: 10px 0; border-left: 4px solid #007bff; }
        .credential-box { background: #e7f3ff; padding: 20px; border-radius: 5px; margin: 20px 0; }
        .error-box { background: #f8d7da; color: #721c24; padding: 20px; border-radius: 5px; margin: 20px 0; }
    </style>
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
        if (!mkdir($dataDir, 0755, true)) { // Recursive ekledik
             throw new Exception("Data klasörü oluşturulamadı!");
        }
        echo "<span class='success'>✓ Data klasörü oluşturuldu</span><br>";
    } else {
        echo "<span class='success'>✓ Data klasörü zaten mevcut</span><br>";
    }
    echo "</div>";

    // 2. Veritabanını ve tabloları oluştur
    echo "<div class='step'><strong>2. Veritabanı tabloları oluşturuluyor...</strong><br>";
    $pdo = getDBConnection();
    $sqlFile = ROOT_PATH . '/sql/create_tables.sql';
    if (!file_exists($sqlFile)) {
        throw new Exception("SQL kurulum dosyası bulunamadı: " . $sqlFile);
    }
    $sql = file_get_contents($sqlFile);
    // PDO::ERRMODE_EXCEPTION ayarlı olduğu için hata olursa exec exception fırlatır
    $pdo->exec($sql);
    echo "<span class='success'>✓ Veritabanı tabloları başarıyla oluşturuldu</span><br>";
    echo "</div>";

    // 3. İlk admin kullanıcısını ekle
    echo "<div class='step'><strong>3. Admin kullanıcısı oluşturuluyor...</strong><br>";
    $db = Database::getInstance(); // Artık tablolar var, sınıfı kullanabiliriz
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
    $companyCount = $pdo->query("SELECT COUNT(*) FROM Bus_Company")->fetchColumn();
    if ($companyCount > 0) {
        echo "<span class='success'>✓ Firmalar zaten mevcut ($companyCount firma)</span><br>";
    } else {
        $companies = [
            ['name' => 'Metro Turizm', 'logo' => '/assets/images/companies/metro.png'],
            ['name' => 'Pamukkale Turizm', 'logo' => '/assets/images/companies/pamukkale.png'],
            ['name' => 'Kamil Koç', 'logo' => '/assets/images/companies/kamilkoc.png']
        ];
        $sqlCompany = "INSERT INTO Bus_Company (id, name, logo_path) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sqlCompany); // Prepared statement kullanalım
        foreach ($companies as $company) {
            $companyId = $db->generateUUID(); // UUID için sınıfı kullanalım
            if (!$stmt->execute([$companyId, $company['name'], $company['logo']])) {
                 throw new Exception("Firma eklenirken hata oluştu: " . $company['name']);
            }
        }
        echo "<span class='success'>✓ " . count($companies) . " örnek firma eklendi</span><br>";
    }
    echo "</div>";

    // --- Başarı Mesajı ve Yönlendirme ---
    echo "<hr><h3 class='success'>✓ Kurulum Başarıyla Tamamlandı!</h3>";
    echo "<div class='credential-box'><h4>🔐 Giriş Bilgileri:</h4><strong>Email:</strong> admin@bilet.com<br><strong>Şifre:</strong> password123<p style='color: #dc3545; margin-top: 15px;'><strong>⚠️ ÖNEMLİ:</strong> Güvenlik için bu şifreyi ilk girişten sonra mutlaka değiştirin!</p></div>";
    echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 20px 0;'><h4>⚠️ GÜVENLİK UYARISI</h4><p><strong>Kurulum tamamlandı. Bu dosyayı HEMEN silin!</strong></p></div>";
    echo "<p><a href='/login' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Giriş Yap</a></p>";

} catch (Exception $e) {
    // --- DÜZELTME: Hata mesajını ekrana yazdır ---
    echo "<div class='error-box'>";
    echo "<h3>❌ Kurulum Hatası</h3>";
    echo "<p><strong>Hata Mesajı:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Lütfen hata mesajını kontrol edin, sorunu düzeltin ve tekrar deneyin.</p>";
    // Hatanın hangi dosyada ve satırda olduğunu göster (geliştirme için faydalı)
    echo "<p><small>Dosya: " . $e->getFile() . " - Satır: " . $e->getLine() . "</small></p>";
    echo "</div>";
    // --- DÜZELTME BİTTİ ---
}
?>
</body>
</html>