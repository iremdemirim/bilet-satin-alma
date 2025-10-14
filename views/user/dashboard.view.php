<?php
/**
 * User Dashboard - Görünüm Dosyası
 */
?>
<div class="container mt-4">

    <div class="card">
        <div class="card-header">
            <h2>Hoş Geldin, <?= htmlspecialchars($user['name']) ?>!</h2>
        </div>
        <div class="card-body grid grid-2">
            <div>
                <strong>Email:</strong>
                <p><?= htmlspecialchars($user['email']) ?></p>
            </div>
            <div class="text-right">
                <strong>Sanal Bakiye:</strong>
                <p class="user-balance" style="font-size: 1.5rem; display: inline-block; padding: 10px 20px;">
                    💰 <?= number_format($user['balance'], 0, ',', '.') ?> ₺
                </p>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            <h3>Biletlerim</h3>
        </div>
        <div class="card-body">
            <?php if (empty($tickets)): ?>
                <div class="alert alert-info">Henüz satın alınmış biletiniz bulunmamaktadır.</div>
            <?php else: ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Sefer</th>
                                <th>Firma</th>
                                <th>Tarih / Saat</th>
                                <th>Koltuklar</th>
                                <th>Durum</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tickets as $ticket): ?>
                                <tr>
                                    <td><?= htmlspecialchars($ticket['departure_city']) ?> → <?= htmlspecialchars($ticket['destination_city']) ?></td>
                                    <td><?= htmlspecialchars($ticket['company_name']) ?></td>
                                    <td><?= date(DATETIME_FORMAT, strtotime($ticket['departure_time'])) ?></td>
                                    <td><?= htmlspecialchars($ticket['seat_numbers']) ?></td>
                                    <td>
                                        <?php if ($ticket['status'] === TICKET_ACTIVE): ?>
                                            <span class="badge badge-success">Aktif</span>
                                        <?php elseif ($ticket['status'] === TICKET_CANCELED): ?>
                                            <span class="badge badge-danger">İptal Edildi</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">Geçmiş</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="/user/ticket-pdf?id=<?= $ticket['id'] ?>" class="btn btn-info btn-sm">PDF</a>
                                        
                                        <?php
                                        // Biletin iptal edilip edilemeyeceğini kontrol et
                                        $canBeCanceled = false;
                                        if ($ticket['status'] === TICKET_ACTIVE) {
                                            $departureTime = new DateTime($ticket['departure_time']);
                                            $now = new DateTime();
                                            // Kalkışa 1 saatten fazla varsa iptal edilebilir 
                                            if ($departureTime > $now->modify('+1 hour')) {
                                                $canBeCanceled = true;
                                            }
                                        }
                                        ?>
                                        
                                        <?php if ($canBeCanceled): ?>
                                            <a href="/user/cancel-ticket?id=<?= $ticket['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bu bileti iptal etmek istediğinizden emin misiniz? Ücret iadesi hesabınıza yapılacaktır.')">İptal Et</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>