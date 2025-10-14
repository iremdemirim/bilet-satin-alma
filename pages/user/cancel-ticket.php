<?php
/**
 * Bilet İptal Etme Mantığı
 */

$auth = new Auth();
$db = Database::getInstance();

$auth->requireUser();
$currentUser = $auth->getCurrentUser();

$ticketId = $_GET['id'] ?? null;

if (!$ticketId) {
    set_flash_message('error', 'Geçersiz bilet IDsi.');
    header('Location: /dashboard');
    exit;
}

// Güvenlik için bileti hem ID hem de kullanıcı ID ile çekiyoruz.
// Böylece bir kullanıcı başkasının biletini iptal edemez.
$ticket = $db->getTicketById($ticketId, $currentUser['id']);

if (!$ticket || $ticket['status'] !== TICKET_ACTIVE) {
    set_flash_message('error', 'İptal edilecek aktif bir bilet bulunamadı.');
    header('Location: /dashboard');
    exit;
}

// Kural: Kalkışa 1 saatten az kalmışsa iptal edilemez. 
$departureTime = new DateTime($ticket['departure_time']);
$now = new DateTime();
$interval = $now->diff($departureTime);
$hoursRemaining = ($interval->days * 24) + $interval->h;

// $interval->invert == 1 ise kalkış zamanı geçmiş demektir.
if ($interval->invert || $hoursRemaining < CANCEL_TIME_LIMIT) {
    set_flash_message('error', 'Kalkışa 1 saatten az bir süre kaldığı için bu bilet iptal edilemez.');
    header('Location: /dashboard');
    exit;
}

// --- VERİTABANI TRANSACTION BAŞLANGICI ---
$db->beginTransaction();
try {
    // Biletin durumunu 'canceled' olarak güncelle
    $sql = "UPDATE Tickets SET status = ?, canceled_at = CURRENT_TIMESTAMP WHERE id = ?";
    if (!$db->execute($sql, [TICKET_CANCELED, $ticketId])) {
        throw new Exception("Bilet durumu güncellenemedi.");
    }

    // Bilet ücretini kullanıcının bakiyesine iade et 
    $newBalance = $currentUser['balance'] + $ticket['total_price'];
    if (!$db->updateUserBalance($currentUser['id'], $newBalance)) {
        throw new Exception("Bakiye iadesi yapılamadı.");
    }

    // Tüm işlemler başarılıysa onayla
    $db->commit();

    // Session'daki bakiyeyi de anında güncelle
    $auth->updateSessionBalance($newBalance);

    set_flash_message('success', 'Biletiniz başarıyla iptal edildi. Ücret iadesi bakiyenize yapıldı.');
    header('Location: /dashboard');
    exit;

} catch (Exception $e) {
    // Hata olursa tüm işlemleri geri al
    $db->rollback();
    set_flash_message('error', 'İptal işlemi sırasında bir hata oluştu: ' . $e->getMessage());
    header('Location: /dashboard');
    exit;
}