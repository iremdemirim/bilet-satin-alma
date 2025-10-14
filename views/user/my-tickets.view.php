<?php
/**
 * Biletlerim Sayfası - Görünüm
 */
?>
<div class="container mt-4">
    <h1 class="mb-4">Tüm Biletlerim</h1>

    <div class="card">
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
                                        $canBeCanceled = false;
                                        if ($ticket['status'] === TICKET_ACTIVE) {
                                            $departureTime = new DateTime($ticket['departure_time']);
                                            $now = new DateTime();
                                            if ($departureTime > $now->modify('+'.CANCEL_TIME_LIMIT.' hour')) {
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