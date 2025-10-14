<?php
/**
 * Bilet PDF Oluşturma Mantığı
 */

$auth = new Auth();
$db = Database::getInstance();

// Bu sayfaya sadece giriş yapmış kullanıcılar erişebilir
$auth->requireUser();
$currentUser = $auth->getCurrentUser();

$ticketId = $_GET['id'] ?? null;

if (!$ticketId) {
    die('Geçersiz bilet IDsi.');
}

// Güvenlik için bileti hem ID hem de kullanıcı ID ile çekiyoruz.
$ticket = $db->getTicketById($ticketId, $currentUser['id']);

if (!$ticket) {
    die('Bilet bulunamadı veya bu bileti görme yetkiniz yok.');
}

// FPDF kütüphanesini dahil et
require_once ROOT_PATH . '/lib/fpdf.php';

// --- PDF OLUŞTURMA BAŞLANGICI ---

// Yeni bir PDF nesnesi oluştur
$pdf = new FPDF('P', 'mm', 'A4'); // Sayfa yönü: Portre, Birim: mm, Boyut: A4

// Türkçe karakter sorunu yaşamamak için FPDF'in kendi font oluşturucusunu kullanmak yerine
// temel fontları kullanıyoruz. Daha gelişmiş Türkçe desteği için TCPDF gibi kütüphaneler
// veya FPDF için özel font dosyaları gerekebilir.
// Bu örnekte basit karakterler düzgün görünecektir.

$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

// Başlık
$pdf->Cell(0, 10, 'SEYAHAT BILETINIZ', 0, 1, 'C');
$pdf->Ln(10); // 10mm boşluk bırak

// Firma Adı
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, utf8_decode($ticket['company_name']), 0, 1, 'C');
$pdf->Ln(5);

// Bilet Detayları
$pdf->SetFont('Arial', '', 12);
$pdf->SetFillColor(240, 240, 240); // Arka plan rengi

$pdf->Cell(40, 10, 'Yolcu Adi:', 1, 0, 'L', true);
$pdf->Cell(0, 10, utf8_decode($currentUser['name']), 1, 1, 'L');

$pdf->Cell(40, 10, 'Guzergah:', 1, 0, 'L', true);
$pdf->Cell(0, 10, utf8_decode($ticket['departure_city']) . ' -> ' . utf8_decode($ticket['destination_city']), 1, 1, 'L');

$pdf->Cell(40, 10, 'Kalkis Zamani:', 1, 0, 'L', true);
$pdf->Cell(0, 10, date(DATETIME_FORMAT, strtotime($ticket['departure_time'])), 1, 1, 'L');

$pdf->Cell(40, 10, 'Koltuk No:', 1, 0, 'L', true);
$pdf->Cell(0, 10, $ticket['seat_numbers'], 1, 1, 'L');

$pdf->Cell(40, 10, 'Odenen Tutar:', 1, 0, 'L', true);
$pdf->Cell(0, 10, number_format($ticket['total_price'], 2, ',', '.') . ' TL', 1, 1, 'L');

$pdf->Cell(40, 10, 'Bilet ID:', 1, 0, 'L', true);
$pdf->Cell(0, 10, $ticket['id'], 1, 1, 'L');

$pdf->Ln(15);

// Alt Bilgi
$pdf->SetFont('Arial', 'I', 10);
$pdf->Cell(0, 10, 'Iyi yolculuklar dileriz!', 0, 1, 'C');

// PDF'i tarayıcıya gönder
$pdf->Output('D', 'bilet-' . $ticketId . '.pdf');

exit;